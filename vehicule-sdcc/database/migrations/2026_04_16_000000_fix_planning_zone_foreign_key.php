<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix the foreign key constraint to properly handle cascade delete
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Use raw SQL to drop the existing constraint if it exists (MySQL only)
            DB::statement('ALTER TABLE users DROP FOREIGN KEY users_planning_zone_id_foreign');

            // Re-add with explicit onDelete('set null')
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('planning_zone_id')
                    ->references('id')
                    ->on('planning_zones')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Drop the new constraint
            DB::statement('ALTER TABLE users DROP FOREIGN KEY users_planning_zone_id_foreign');

            // Restore the original constraint
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('planning_zone_id')
                    ->references('id')
                    ->on('planning_zones')
                    ->nullOnDelete();
            });
        }
    }
};



