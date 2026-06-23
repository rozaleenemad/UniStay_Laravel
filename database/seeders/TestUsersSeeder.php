<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * TestUsersSeeder
 *
 * FOR LOCAL DEVELOPMENT ONLY — never run in production.
 * Usage: php artisan db:seed --class=TestUsersSeeder
 */
class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Test Owner
        $owner        = new User();
        $owner->name  = 'Ahmed Owner';
        $owner->email = 'owner@stayuni.test';  // .test domain makes it obvious it's fake
        $owner->password = Hash::make('password');
        $owner->role  = 'owner';
        $owner->status = 'approved';
        $owner->phone = '01111111111';
        $owner->save();

        // Test Student
        $student        = new User();
        $student->name  = 'Sara Student';
        $student->email = 'student@stayuni.test';
        $student->password = Hash::make('password');
        $student->role  = 'student';
        $student->status = 'approved';
        $student->phone = '01222222222';
        $student->save();

        $this->command->info('Test users created.');
    }
}
