<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add ENUM constraint to demandes status column
     * Prevents invalid status values from being inserted
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // MySQL: Modify column to ENUM
            Schema::table('demandes', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                    ->default('pending')
                    ->change();
            });
        }
        // SQLite doesn't support ENUM, but validation happens at application level
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('demandes', function (Blueprint $table) {
                $table->string('status')
                    ->default('pending')
                    ->change();
            });
        }
    }
};
