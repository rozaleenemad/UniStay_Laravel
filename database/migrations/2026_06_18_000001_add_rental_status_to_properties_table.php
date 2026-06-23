<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'rented' to the status enum
        DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('pending','approved','rejected','rented') NOT NULL DEFAULT 'pending'");

        // Add governorate column if missing (may already exist from earlier migration)
        if (!Schema::hasColumn('properties', 'governorate')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('governorate')->nullable()->after('user_id');
            });
        }

        // Track when a property was marked rented
        Schema::table('properties', function (Blueprint $table) {
            $table->timestamp('rented_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('rented_at');
        });
        DB::statement("ALTER TABLE properties MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
