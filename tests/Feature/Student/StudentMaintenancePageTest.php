<?php

namespace Tests\Feature\Student;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentMaintenancePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * BUG (fixed): StudentMaintenanceController::index() used Property::all(),
     * leaking pending/rejected properties (still awaiting admin review) to students.
     */
    public function test_student_only_sees_approved_properties(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $approved = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);
        $pending  = Property::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);
        $rejected = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rejected']);

        $response = $this->actingAs($student)->get(route('student.maintenance.index'));

        $response->assertViewHas('properties', function ($list) use ($approved, $pending, $rejected) {
            return $list->contains('id', $approved->id)
                && !$list->contains('id', $pending->id)
                && !$list->contains('id', $rejected->id);
        });
    }

    public function test_student_maintenance_page_lists_only_approved_technicians(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $approvedTech = User::factory()->create(['role' => 'maintenance', 'status' => 'approved']);
        $pendingTech = User::factory()->create(['role' => 'maintenance', 'status' => 'pending']);

        $response = $this->actingAs($student)->get(route('student.maintenance.index'));

        $response->assertViewHas('technicians', function ($list) use ($approvedTech, $pendingTech) {
            return $list->contains('id', $approvedTech->id) && !$list->contains('id', $pendingTech->id);
        });
    }

    public function test_owner_cannot_access_student_only_routes(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $response = $this->actingAs($owner)->get(route('student.maintenance.index'));

        $response->assertForbidden();
    }

    public function test_student_dashboard_only_shows_approved_properties(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);
        $approved = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);
        $rented = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rented']);

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertViewHas('properties', function ($list) use ($approved, $rented) {
            return $list->contains('id', $approved->id) && !$list->contains('id', $rented->id);
        });
    }
}
