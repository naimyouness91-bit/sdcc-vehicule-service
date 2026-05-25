<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, let's update any existing 'weekday' values to 'both' to maintain compatibility
        if (Schema::hasTable('cars')) {
            DB::table('cars')->where('availability_type', 'weekday')->update(['availability_type' => 'both']);
            
            // For SQLite, we need a different approach
            if (DB::getDriverName() === 'sqlite') {
                // SQLite doesn't support MODIFY, so we'll use a workaround
                // Just update existing values, the enum definition will be enforced at the application level
            } else {
                // For MySQL, use MODIFY
                DB::statement("ALTER TABLE cars MODIFY availability_type ENUM('weekend', 'both', 'unavailable') DEFAULT 'both'");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cars')) {
            DB::table('cars')->where('availability_type', 'unavailable')->update(['availability_type' => 'both']);
            
            if (DB::getDriverName() !== 'sqlite') {
                DB::statement("ALTER TABLE cars MODIFY availability_type ENUM('weekday', 'weekend', 'both') DEFAULT 'both'");
            }
        }
    }
};
