<?php

namespace Tests\Feature\Admin;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPropertyVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'approved']);
    }

    private function makeOwner(string $status): User
    {
        return User::factory()->create(['role' => 'owner', 'status' => $status]);
    }

    /**
     * BUG (fixed): pendingPropertiesList used to filter on whereHas('owner', status=approved),
     * which silently hid every property belonging to a pending/rejected owner from the admin
     * dashboard — even though admin needs to see and act on exactly those properties.
     */
    public function test_admin_sees_properties_belonging_to_pending_owner(): void
    {
        $admin = $this->makeAdmin();
        $pendingOwner = $this->makeOwner('pending');

        $property = Property::factory()->create([
            'user_id' => $pendingOwner->id,
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('pendingPropertiesList', function ($list) use ($property) {
            return $list->contains('id', $property->id);
        });
    }

    public function test_admin_sees_properties_belonging_to_rejected_owner(): void
    {
        $admin = $this->makeAdmin();
        $rejectedOwner = $this->makeOwner('rejected');

        $property = Property::factory()->create([
            'user_id' => $rejectedOwner->id,
            'status'  => 'approved',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('pendingPropertiesList', function ($list) use ($property) {
            return $list->contains('id', $property->id);
        });
    }

    public function test_admin_can_approve_property_owned_by_pending_owner(): void
    {
        $admin = $this->makeAdmin();
        $pendingOwner = $this->makeOwner('pending');
        $property = Property::factory()->create([
            'user_id' => $pendingOwner->id,
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.properties.approve', $property->id));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertSame('approved', $property->refresh()->status);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
