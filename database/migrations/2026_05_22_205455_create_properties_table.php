<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->unsignedInteger('price');
            $table->string('location');
            $table->string('proximity')->nullable();
            $table->unsignedInteger('floor')->default(1);
            $table->unsignedInteger('bedrooms')->default(1);
            $table->unsignedInteger('bathrooms')->default(1);
            $table->enum('gender_type', ['male', 'female']);
            $table->boolean('is_furnished')->default(false);
            $table->boolean('utilities_included')->default(false);
            $table->date('available_from')->nullable();
            $table->text('description')->nullable();
            $table->json('images')->nullable();              
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
