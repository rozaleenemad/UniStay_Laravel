<?php

namespace Tests\Feature\Maintenance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceDashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * BUG (fixed): /maintenance/dashboard had no role middleware and no abort_if
     * check in the controller — any authenticated student or owner could open it
     * and see every maintenance request in the system (broken access control).
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

    public function test_maintenance_user_can_access_maintenance_dashboard(): void
    {
        $tech = User::factory()->create(['role' => 'maintenance', 'status' => 'approved']);

        $response = $this->actingAs($tech)->get(route('maintenance.dashboard'));

        $response->assertOk();
    }

    public function test_guest_cannot_access_maintenance_dashboard(): void
    {
        $response = $this->get(route('maintenance.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
