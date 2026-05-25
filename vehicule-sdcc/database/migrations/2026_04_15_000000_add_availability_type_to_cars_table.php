<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'availability_type')) {
                $table->enum('availability_type', ['weekday', 'weekend', 'both'])
                    ->default('both')
                    ->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'availability_type')) {
                $table->dropColumn('availability_type');
            }
        });
    }
};

