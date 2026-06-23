<?php

namespace Tests\Feature\Maintenance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceAccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Defence-in-depth: /maintenance/dashboard had no role middleware at all.
     * Any authenticated user (student, owner) could view every maintenance
     * request in the system just by visiting the URL directly.
     */
    public function test_student_cannot_access_maintenance_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('maintenance.dashboard'));

        $response->assertForbidden();
    }

    public function test_owner_cannot_access_maintenance_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $response = $this->actingAs($owner)->get(route('maintenance.dashboard'));

        $response->assertForbidden();
    }

    public function test_maintenance_technician_can_access_own_dashboard(): void
    {
        $tech = User::factory()->create(['role' => 'maintenance', 'status' => 'approved']);

        $response = $this->actingAs($tech)->get(route('maintenance.dashboard'));

        $response->assertOk();
    }

    public function test_guest_redirected_to_login_from_maintenance_dashboard(): void
    {
        $response = $this->get(route('maintenance.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * BUG (fixed): MaintenanceDiscoveryController used to list ALL users with
     * role=maintenance regardless of status, so a rejected technician could
     * still be surfaced to property owners looking for help.
     */
    public function test_owner_discovery_only_shows_approved_technicians(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);

        $approvedTech = User::factory()->create([
            'role' => 'maintenance',
            'status' => 'approved',
            'maintenance_type' => 'plumbing',
            'governorate' => 'cairo',
        ]);

        $rejectedTech = User::factory()->create([
            'role' => 'maintenance',
            'status' => 'rejected',
            'maintenance_type' => 'plumbing',
            'governorate' => 'cairo',
        ]);

        // 🛠️ التعديل: إرسال 'cairo' بحروف صغيرة لتتطابق تماماً مع بيانات الـ Factory والـ Controller المحدث
        $response = $this->actingAs($student)->get(route('student.maintenance.index', ['governorate' => 'cairo']));

        $response->assertViewHas('technicians', function ($list) use ($approvedTech, $rejectedTech) {
            return $list->contains('id', $approvedTech->id)
                && !$list->contains('id', $rejectedTech->id);
        });
    }
}
