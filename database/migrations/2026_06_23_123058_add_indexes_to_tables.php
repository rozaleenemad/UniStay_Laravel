<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'users_role_status_index');

            $table->index('governorate', 'users_governorate_index');
            $table->index('maintenance_type', 'users_maintenance_type_index');
        });

        Schema::table('properties', function (Blueprint $table) {

            $table->index(['status', 'governorate'], 'properties_status_governorate_index');

            $table->index('gender_type', 'properties_gender_type_index');
        });
    }

   
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_status_index');
            $table->dropIndex('users_governorate_index');
            $table->dropIndex('users_maintenance_type_index');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('properties_status_governorate_index');
            $table->dropIndex('properties_gender_type_index');
        });
    }
};
