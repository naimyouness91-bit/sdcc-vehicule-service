<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->nullable()->constrained('cars')->nullOnDelete();
            $table->string('destination');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time')->nullable();
            $table->time('return_time')->nullable();
            $table->unsignedInteger('kilometers')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
