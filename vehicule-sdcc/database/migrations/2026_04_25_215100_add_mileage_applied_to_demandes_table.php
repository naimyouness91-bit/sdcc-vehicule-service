<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            if (! Schema::hasColumn('demandes', 'mileage_applied')) {
                $table->boolean('mileage_applied')->default(false)->after('distance_travelled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            if (Schema::hasColumn('demandes', 'mileage_applied')) {
                $table->dropColumn('mileage_applied');
            }
        });
    }
};
