<?php

namespace Tests\Feature\Maintenance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_discovery_lists_approved_technicians(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);
        $tech = User::factory()->create([
            'role' => 'maintenance',
            'status' => 'approved',
            'maintenance_type' => 'plumbing',
            'governorate' => 'cairo',
        ]);

        $response = $this->actingAs($student)->get('/student/maintenance?governorate=cairo&maintenance_type=plumbing');

        $response->assertOk();
        $response->assertViewHas('technicians', function ($list) use ($tech) {
            return $list->contains('id', $tech->id);
        });
    }

    /**
     * Filter must not silently return unapproved/rejected technicians.
     */
    public function test_student_discovery_excludes_non_approved_technicians(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);
        $rejectedTech = User::factory()->create([
            'role' => 'maintenance',
            'status' => 'rejected',
            'maintenance_type' => 'plumbing',
            'governorate' => 'cairo',
        ]);

        $response = $this->actingAs($student)->get('/student/maintenance');
        $response->assertViewHas('technicians', function ($list) use ($rejectedTech) {
            return !$list->contains('id', $rejectedTech->id);
        });
    }

    public function test_filter_by_governorate_excludes_other_governorates(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'approved']);

        // 🛠️ التعديل: جعل المحافظات حروف صغيرة لتطابق الـ Controller
        $cairoTech = User::factory()->create([
            'role' => 'maintenance',
            'status' => 'approved',
            'governorate' => 'cairo',
            'maintenance_type' => 'plumbing'
        ]);

        $assiutTech = User::factory()->create([
            'role' => 'maintenance',
            'status' => 'approved',
            'governorate' => 'assiut',
            'maintenance_type' => 'plumbing'
        ]);

        // 🛠️ التعديل: إرسال الـ parameter بحروف صغيرة 'cairo'
        $response = $this->actingAs($student)->get('/student/maintenance?governorate=cairo');

        $response->assertViewHas('technicians', function ($list) use ($cairoTech, $assiutTech) {
            return $list->contains('id', $cairoTech->id) && !$list->contains('id', $assiutTech->id);
        });
    }
}
