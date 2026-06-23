<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature   = 'admin:create';
    protected $description = 'Create a new admin account interactively (safe for production)';

    public function handle(): void
    {
        $this->info('=== Create Admin Account ===');

        // Email
        $email = $this->ask('Admin email');
        $validator = Validator::make(['email' => $email], ['email' => 'required|email|unique:users,email']);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());
            return;
        }

        // Name
        $name = $this->ask('Admin name', 'System Admin');

        // Password
        $password = $this->secret('Admin password (min 8 chars)');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return;
        }

        $confirm = $this->secret('Confirm password');
        if ($password !== $confirm) {
            $this->error('Passwords do not match.');
            return;
        }

        // Create
        $admin           = new User();
        $admin->name     = $name;
        $admin->email    = $email;
        $admin->password = Hash::make($password);
        $admin->role     = 'admin';
        $admin->status   = 'approved';
        $admin->phone    = '00000000000';
        $admin->save();

        $this->info("✅ Admin account created successfully: {$email}");
        $this->warn('Keep these credentials safe — they are not stored anywhere else.');
    }
}
