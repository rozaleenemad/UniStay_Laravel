<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Production ──────────────────────────────────────────────
        // Run AdminUserSeeder only — reads credentials from .env
        $this->call(AdminUserSeeder::class);

        // ── Local / Testing only ────────────────────────────────────
        // Uncomment the line below ONLY in local development:
        // $this->call(TestUsersSeeder::class);
    }
}
