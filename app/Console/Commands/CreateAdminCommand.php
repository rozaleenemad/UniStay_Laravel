<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create a new admin user interactively';

    public function handle()
    {
        $name = $this->ask('Enter Admin Name', 'Admin User');
        $email = $this->ask('Enter Admin Email');
        $password = $this->secret('Enter Admin Password');

        if (User::where('email', $email)->exists()) {
            $this->error('This email is already registered!');
            return Command::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        $this->info("Admin account ($email) created successfully! 🎉");
        return Command::SUCCESS;
    }
}
