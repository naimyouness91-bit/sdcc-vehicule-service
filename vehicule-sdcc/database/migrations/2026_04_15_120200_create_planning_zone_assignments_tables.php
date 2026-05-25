<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_zone_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_zone_id')->constrained('planning_zones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['planning_zone_id', 'user_id']);
        });

        Schema::create('planning_zone_car', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_zone_id')->constrained('planning_zones')->cascadeOnDelete();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['planning_zone_id', 'car_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_zone_car');
        Schema::dropIfExists('planning_zone_user');
    }
};

