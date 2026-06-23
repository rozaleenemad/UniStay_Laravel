<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RegistrationRoleSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Defence-in-depth: even though 'role' isn't in $fillable, the registration
     * form should never be able to create an admin account via the public form.
     */
    public function test_cannot_register_as_admin_through_public_form(): void
    {
        // 🛠️ التعديل: إرسال 'admin' في الحقل لمعرفة إن كان النظام سيرفضه أم لا
        $response = $this->post('/register', [
            'name' => 'Hacker User',
            'email' => 'hacker@example.com',
            'phone' => '01012345678',
            'role' => 'admin', // محاولة التسجيل كأدمن
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // يجب أن يرفض حقل الـ role لأن الأدمن ليس من الخيارات المتاحة بالتسجيل (in:student,owner,maintenance)
        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_student_registers_as_approved_immediately(): void
    {
        $this->post(route('register'), [
            'name'     => 'Student One',
            'email'    => 'student1@example.com',
            'phone'    => '01000000001',
            'role'     => 'student',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'student1@example.com')->first();
        $this->assertSame('student', $user->role);
        $this->assertSame('approved', $user->status);
    }

    public function test_owner_registers_as_pending_and_cannot_self_approve(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Owner One',
            'email'                 => 'owner1@example.com',
            'phone'                 => '01000000002',
            'role'                  => 'owner',
            'national_id'           => '12345678901234',
            // 🛠️ التعديل هنا: استخدام create بدلاً من image لتخطي غياب مكتبة GD
            'id_card_image'         => UploadedFile::fake()->create('id.jpg', 100, 'image/jpeg'),
            'status'                => 'approved', // محاولة رفع الصلاحية لتخطي الانتظار
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'owner1@example.com')->first();

        // تأكيد أن المستخدم تم إنشاؤه بنجاح ولم يعد null
        $this->assertNotNull($user);
        $this->assertSame('owner', $user->role);
        // يجب أن تظل الحالة معلقة pending حتى لو حاول المالك إرسال حالة مقبول approved
        $this->assertSame('pending', $user->status);
    }
}
