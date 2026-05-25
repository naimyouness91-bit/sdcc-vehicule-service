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
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        if (Schema::hasColumn('users', 'current_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('current_role');
            });
        }

        Schema::table('demandes', function (Blueprint $table) {
            $table->index(['car_id', 'start_date'], 'demandes_car_start_idx');
            $table->index(['user_id', 'status'], 'demandes_user_status_idx');
            $table->index('status', 'demandes_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add columns only if they do not already exist to make down() idempotent
        if (! Schema::hasColumn('users', 'role') || ! Schema::hasColumn('users', 'current_role')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'role')) {
                    $table->string('role')->default('employee');
                }
                if (! Schema::hasColumn('users', 'current_role')) {
                    $table->string('current_role')->nullable()->after('role');
                }
            });
        }

        Schema::table('demandes', function (Blueprint $table) {
            $table->dropIndex('demandes_car_start_idx');
            $table->dropIndex('demandes_user_status_idx');
            $table->dropIndex('demandes_status_idx');
        });
    }
};
