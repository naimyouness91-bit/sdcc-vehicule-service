<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            if (! Schema::hasColumn('demandes', 'distance_travelled')) {
                $table->integer('distance_travelled')->nullable()->after('kilometers');
            }
        });

        // If old 'kilometers' column exists, copy values into new column
        if (Schema::hasColumn('demandes', 'kilometers') && Schema::hasColumn('demandes', 'distance_travelled')) {
            DB::statement('UPDATE demandes SET distance_travelled = kilometers WHERE distance_travelled IS NULL');
        }
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            if (Schema::hasColumn('demandes', 'distance_travelled')) {
                $table->dropColumn('distance_travelled');
            }
        });
    }
};
