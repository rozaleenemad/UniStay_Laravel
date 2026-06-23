<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * AdminUserSeeder
 *
 * FOR LOCAL DEVELOPMENT ONLY.
 * Never run this in production — use: php artisan admin:create
 *
 * Usage: php artisan db:seed --class=AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Reads from .env — no hardcoded credentials
        $email    = env('ADMIN_EMAIL', 'admin@stayuni.com');
        $password = env('ADMIN_PASSWORD');

        if (!$password) {
            $this->command->error('ADMIN_PASSWORD is not set in .env — aborting.');
            return;
        }

        $admin = User::where('email', $email)->first();

        if ($admin) {
            $this->command->warn("Admin with email [{$email}] already exists. Skipping.");
            return;
        }

        $admin        = new User();
        $admin->name  = 'System Admin';
        $admin->email = $email;
        $admin->password = Hash::make($password);
        $admin->role  = 'admin';
        $admin->status = 'approved';
        $admin->phone = '00000000000';
        $admin->save();

        $this->command->info("Admin created: {$email}");
    }
}
