<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kilometrage_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('recorded_at');
            $table->decimal('kilometers', 10, 1);
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['car_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kilometrage_entries');
    }
};

