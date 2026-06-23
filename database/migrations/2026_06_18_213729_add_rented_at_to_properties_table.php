<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('properties', 'rented_at')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->timestamp('rented_at')->nullable()->after('status');
            });
        }
    }

 public function down(): void
{
    if (Schema::hasColumn('properties', 'rented_at')) {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('rented_at');
        });
    }
}
};
