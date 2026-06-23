<?php

namespace Tests\Feature\Student;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(): User
    {
        return User::factory()->create(['role' => 'student', 'status' => 'approved']);
    }

    public function test_student_dashboard_only_shows_approved_properties(): void
    {
        $student = $this->makeStudent();
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $approved = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);
        $pending  = Property::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);
        $rejected = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rejected']);
        $rented   = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rented']);

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertViewHas('properties', function ($list) use ($approved, $pending, $rejected, $rented) {
            return $list->contains('id', $approved->id)
                && !$list->contains('id', $pending->id)
                && !$list->contains('id', $rejected->id)
                && !$list->contains('id', $rented->id);
        });
    }

    /**
     * BUG (fixed): StudentMaintenanceController::index() used Property::all(),
     * leaking every property regardless of status (pending/rejected included)
     * to any logged-in student via the maintenance tab's property list.
     */
    public function test_student_maintenance_page_only_shows_approved_properties(): void
    {
        $student = $this->makeStudent();
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $approved = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);
        $pending  = Property::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($student)->get(route('student.maintenance.index'));

        $response->assertOk();
        $response->assertViewHas('properties', function ($list) use ($approved, $pending) {
            return $list->contains('id', $approved->id)
                && !$list->contains('id', $pending->id);
        });
    }

    public function test_gender_filter_only_returns_matching_properties(): void
    {
        $student = $this->makeStudent();
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved', 'gender_type' => 'male']);
        $female = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved', 'gender_type' => 'female']);

        $response = $this->actingAs($student)->get(route('student.dashboard', ['gender' => 'female']));

        $response->assertViewHas('properties', function ($list) use ($female) {
            return $list->count() === 1 && $list->first()->id === $female->id;
        });
    }

    public function test_owner_cannot_access_student_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $response = $this->actingAs($owner)->get(route('student.dashboard'));

        $response->assertForbidden();
    }
}
