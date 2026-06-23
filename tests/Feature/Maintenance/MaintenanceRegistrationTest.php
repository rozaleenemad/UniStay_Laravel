<?php

namespace Tests\Feature\Maintenance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * BUG (fixed): MaintenanceRegisterController::register() used User::create([...])
     * with 'role' and 'status' keys, but those fields are deliberately excluded from
     * $fillable on the User model (to block mass-assignment privilege escalation
     * elsewhere). Laravel silently drops unfillable keys, so every technician who
     * registered ended up with role = null (or DB default 'student') instead of
     * 'maintenance', and was invisible to every maintenance discovery/filter query.
     */
    public function test_registering_as_maintenance_sets_role_and_status_correctly(): void
    {
        $response = $this->post(route('maintenance.register'), [
            'name'             => 'Test Tech',
            'email'            => 'tech@example.com',
            'password'         => 'password123',
            'password_confirmation' => 'password123',
            'phone'            => '01012345678',
            'location'         => 'Some street',
            'governorate'      => 'assiut',
            'maintenance_type' => 'plumbing',
        ]);

        $user = User::where('email', 'tech@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('maintenance', $user->role, 'role must actually persist as maintenance');
        $this->assertSame('approved', $user->status);
        $response->assertRedirect(route('maintenance.dashboard'));
    }

    public function test_newly_registered_technician_is_discoverable_by_owners(): void
    {
        $this->post(route('maintenance.register'), [
            'name'             => 'Findable Tech',
            'email'            => 'findable@example.com',
            'password'         => 'password123',
            'password_confirmation' => 'password123',
            'phone'            => '01099999999',
            'location'         => 'Somewhere',
            'governorate'      => 'cairo',
            'maintenance_type' => 'electricity',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'findable@example.com',
            'role'  => 'maintenance',
        ]);
    }

    public function test_registration_rejects_invalid_governorate(): void
    {
        $response = $this->post(route('maintenance.register'), [
            'name'             => 'Bad Gov',
            'email'            => 'badgov@example.com',
            'password'         => 'password123',
            'password_confirmation' => 'password123',
            'phone'            => '01012345678',
            'governorate'      => 'not_a_real_governorate',
            'maintenance_type' => 'plumbing',
        ]);

        $response->assertSessionHasErrors('governorate');
        $this->assertDatabaseMissing('users', ['email' => 'badgov@example.com']);
    }

    public function test_registration_rejects_invalid_maintenance_type(): void
    {
        $response = $this->post(route('maintenance.register'), [
            'name'             => 'Bad Type',
            'email'            => 'badtype@example.com',
            'password'         => 'password123',
            'password_confirmation' => 'password123',
            'phone'            => '01012345678',
            'governorate'      => 'cairo',
            'maintenance_type' => 'rocket_science',
        ]);

        $response->assertSessionHasErrors('maintenance_type');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->post(route('maintenance.register'), [
            'name'             => 'Dup Tech',
            'email'            => 'dup@example.com',
            'password'         => 'password123',
            'password_confirmation' => 'password123',
            'phone'            => '01012345678',
            'governorate'      => 'cairo',
            'maintenance_type' => 'plumbing',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
