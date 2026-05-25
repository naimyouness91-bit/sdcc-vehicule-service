<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support modifying foreign keys easily, skip for SQLite
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        
        // Drop the foreign key constraint that has cascadeOnDelete
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Recreate the foreign key without cascading delete
        // This preserves reservations when users are deleted/deactivated
        Schema::table('demandes', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();  // Prevent deletion of users who have reservations
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        
        // Revert to the original constraint with cascadeOnDelete
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('demandes', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
