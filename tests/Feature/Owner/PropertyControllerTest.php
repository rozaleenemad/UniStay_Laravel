<?php

namespace Tests\Feature\Owner;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $status = 'approved'): User
    {
        return User::factory()->create(['role' => 'owner', 'status' => $status]);
    }

    // ── store() ──────────────────────────────────────────────────────

    public function test_pending_owner_cannot_create_property(): void
    {
        $owner = $this->makeOwner('pending');

        $response = $this->actingAs($owner)->post(route('owner.properties.store'), [
            'price' => 1000,
            'governorate' => 'cairo',
            'location' => 'X',
            'floor' => 1,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'gender_type' => 'male',
        ]);

        $response->assertRedirect();
    }

    public function test_approved_owner_can_create_property_and_it_starts_pending(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner('approved');

        // 🛠️ التعديل هنا: استخدام create بدلاً من image لتفادي استدعاء الـ GD Extension
        $image = UploadedFile::fake()->create('room.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($owner)->post(route('owner.properties.store'), [
            'price' => 1000,
            'governorate' => 'cairo',
            'location' => 'X',
            'floor' => 1,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'gender_type' => 'male',
            'property_images' => [$image],
        ]);

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertDatabaseHas('properties', [
            'user_id' => $owner->id,
            'status'  => 'pending',
        ]);
    }

    public function test_student_cannot_create_property(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->post(route('owner.properties.store'), [
            'price' => 1000,
            'governorate' => 'cairo',
            'location' => 'X',
            'floor' => 1,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'gender_type' => 'male',
        ]);

        $response->assertForbidden();
    }

    // ── edit() / update() / destroy() — IDOR checks ─────────────────

    public function test_owner_cannot_edit_another_owners_property(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $ownerB->id]);

        $response = $this->actingAs($ownerA)->get(route('owner.properties.edit', $property));

        $response->assertForbidden();
    }

    public function test_owner_cannot_update_another_owners_property(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $ownerB->id, 'price' => 500]);

        $response = $this->actingAs($ownerA)->put(route('owner.properties.update', $property), [
            'price' => 9999,
            'governorate' => 'cairo',
            'location' => 'X',
            'proximity' => 1,
            'floor' => 1,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'gender_type' => 'male',
        ]);

        $response->assertForbidden();
        $this->assertSame(500, (int) $property->refresh()->price);
    }

    public function test_owner_cannot_delete_another_owners_property(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $ownerB->id]);

        $response = $this->actingAs($ownerA)->delete(route('owner.properties.destroy', $property));

        $response->assertForbidden();
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    public function test_rented_property_cannot_be_edited(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rented']);

        $response = $this->actingAs($owner)->get(route('owner.properties.edit', $property));

        $response->assertForbidden();
    }

    public function test_rented_property_cannot_be_deleted(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'rented']);

        $response = $this->actingAs($owner)->delete(route('owner.properties.destroy', $property));

        $response->assertForbidden();
    }

    // ── markAsRented() ───────────────────────────────────────────────

    public function test_owner_can_mark_approved_property_as_rented(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.mark-rented', $property));

        $response->assertRedirect();
        $this->assertSame('rented', $property->refresh()->status);
        $this->assertNotNull($property->rented_at);
    }

    /**
     * BUG (fixed): markAsRented previously had no status check at all, allowing
     * a pending or rejected property to be marked "rented" directly.
     */
    public function test_owner_cannot_mark_pending_property_as_rented(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.mark-rented', $property));

        $response->assertForbidden();
        $this->assertSame('pending', $property->refresh()->status);
    }

    public function test_owner_cannot_mark_another_owners_property_as_rented(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $ownerB->id, 'status' => 'approved']);

        $response = $this->actingAs($ownerA)->patch(route('owner.properties.mark-rented', $property));

        $response->assertForbidden();
    }

    // ── activateByOwner() ─────────────────────────────────────────────

    /**
     * BUG (fixed): activateByOwner used to set status directly to 'approved',
     * letting an owner bypass admin re-review entirely after a rental ended.
     * It must now route the property back to 'pending'.
     */
    public function test_reactivating_a_rented_property_sends_it_to_pending_not_approved(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create([
            'user_id'   => $owner->id,
            'status'    => 'rented',
            'rented_at' => now(),
        ]);

        $response = $this->actingAs($owner)->patch(route('owner.properties.activate', $property));

        $response->assertRedirect();
        $this->assertSame('pending', $property->refresh()->status);
        $this->assertNull($property->rented_at);
    }

    public function test_cannot_reactivate_a_non_rented_property(): void
    {
        $owner = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);

        $response = $this->actingAs($owner)->patch(route('owner.properties.activate', $property));

        $response->assertForbidden();
    }

    public function test_owner_cannot_reactivate_another_owners_property(): void
    {
        $ownerA = $this->makeOwner();
        $ownerB = $this->makeOwner();
        $property = Property::factory()->create(['user_id' => $ownerB->id, 'status' => 'rented']);

        $response = $this->actingAs($ownerA)->patch(route('owner.properties.activate', $property));

        $response->assertForbidden();
    }
}
