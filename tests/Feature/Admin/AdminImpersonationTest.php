<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_impersonate_an_owner(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);

        $response = $this->actingAs($admin)->get(route('admin.impersonate', $owner->id));

        $response->assertRedirect('/owner/properties');
    }

    /**
     * BUG (fixed): impersonate() had no check that the target user can actually
     * be impersonated, meaning an admin could impersonate another admin account.
     */
    public function test_admin_cannot_impersonate_another_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.impersonate', $otherAdmin->id));

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_use_impersonate_route(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => 'approved']);
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('admin.impersonate', $owner->id));

        $response->assertForbidden();
    }
}
