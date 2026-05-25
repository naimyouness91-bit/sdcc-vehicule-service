<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add planning_zone_id foreign key to cars table
     * Enables proper relationship between cars and planning zones
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Add the foreign key column if it doesn't exist
            if (!Schema::hasColumn('cars', 'planning_zone_id')) {
                $table->foreignId('planning_zone_id')
                    ->nullable()
                    ->constrained('planning_zones')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['planning_zone_id']);
            $table->dropColumnIfExists('planning_zone_id');
        });
    }
};
