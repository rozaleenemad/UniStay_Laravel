<?php

namespace Tests\Feature\Owner;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyOwnershipAndLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function approvedOwner(): User
    {
        return User::factory()->create(['role' => 'owner', 'status' => 'approved']);
    }

    // ── IDOR checks ──────────────────────────────────────────────────

    public function test_owner_cannot_edit_another_owners_property(): void
    {
        $owner = $this->approvedOwner();
        $otherOwner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $otherOwner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->get(route('owner.properties.edit', $property));

        $response->assertForbidden();
    }

    public function test_owner_cannot_update_another_owners_property(): void
    {
        $owner = $this->approvedOwner();
        $otherOwner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $otherOwner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->put(route('owner.properties.update', $property), [
            'price' => 1000, 'governorate' => 'Cairo', 'location' => 'x',
            'proximity' => 1, 'floor' => 1, 'bedrooms' => 1, 'bathrooms' => 1,
            'gender_type' => 'male',
        ]);

        $response->assertForbidden();
    }

    public function test_owner_cannot_delete_another_owners_property(): void
    {
        $owner = $this->approvedOwner();
        $otherOwner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $otherOwner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->delete(route('owner.properties.destroy', $property));

        $response->assertForbidden();
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    public function test_owner_cannot_mark_another_owners_property_as_rented(): void
    {
        $owner = $this->approvedOwner();
        $otherOwner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $otherOwner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.mark-rented', $property));

        $response->assertForbidden();
    }

    // ── markAsRented business rule (fixed: now requires approved status) ─

    public function test_owner_can_mark_their_approved_property_as_rented(): void
    {
        $owner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.mark-rented', $property));

        $response->assertRedirect();
        $property->refresh();
        $this->assertSame('rented', $property->status);
        $this->assertNotNull($property->rented_at);
    }

    public function test_owner_cannot_mark_pending_property_as_rented(): void
    {
        $owner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.mark-rented', $property));

        $response->assertForbidden();
        $this->assertSame('pending', $property->refresh()->status);
    }

    // ── activateByOwner business rule (fixed: must go back to pending, not approved) ─

    public function test_owner_reactivating_rented_property_sends_it_to_pending_not_approved(): void
    {
        $owner = $this->approvedOwner();
        $property = Property::factory()->create([
            'user_id' => $owner->id,
            'status' => 'rented',
            'rented_at' => now(),
        ]);

        $response = $this->actingAs($owner)->patch(route('owner.properties.activate', $property));

        $response->assertRedirect();
        $property->refresh();
        $this->assertSame('pending', $property->status, 'Reactivation must require admin re-approval, not bypass it.');
        $this->assertNull($property->rented_at);
    }

    public function test_owner_cannot_reactivate_a_non_rented_property(): void
    {
        $owner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.activate', $property));

        $response->assertForbidden();
    }

    // ── Rented properties cannot be edited/deleted ──────────────────

    public function test_owner_cannot_edit_rented_property(): void
    {
        $owner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rented']);

        $response = $this->actingAs($owner)->get(route('owner.properties.edit', $property));

        $response->assertForbidden();
    }

    public function test_owner_cannot_delete_rented_property(): void
    {
        $owner = $this->approvedOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rented']);

        $response = $this->actingAs($owner)->delete(route('owner.properties.destroy', $property));

        $response->assertForbidden();
    }

    // ── Pending/rejected owners blocked from owner area ──────────────

    public function test_pending_owner_is_redirected_to_pending_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'pending']);

        $response = $this->actingAs($owner)->get(route('owner.dashboard'));

        $response->assertRedirect(route('owner.pending'));
    }

    public function test_rejected_owner_is_logged_out_on_protected_route(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'rejected']);

        $response = $this->actingAs($owner)->get(route('owner.dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_student_cannot_create_property(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('owner.properties.create'));

        $response->assertForbidden();
    }
}
