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
        Schema::table('cars', function (Blueprint $table) {
            if (! Schema::hasColumn('cars', 'kilometrage_max')) {
                $table->integer('kilometrage_max')->default(0)->after('km');
            }
            if (! Schema::hasColumn('cars', 'kilometrage_actuel')) {
                $table->integer('kilometrage_actuel')->default(0)->after('kilometrage_max');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'kilometrage_actuel')) {
                $table->dropColumn('kilometrage_actuel');
            }
            if (Schema::hasColumn('cars', 'kilometrage_max')) {
                $table->dropColumn('kilometrage_max');
            }
        });
    }
};
