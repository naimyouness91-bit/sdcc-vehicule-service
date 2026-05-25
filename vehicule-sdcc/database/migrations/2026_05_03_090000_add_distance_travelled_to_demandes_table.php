<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('demandes')) {
            Schema::table('demandes', function (Blueprint $table) {
                if (!Schema::hasColumn('demandes', 'distance_travelled')) {
                    $table->integer('distance_travelled')->nullable()->after('kilometers');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('demandes')) {
            Schema::table('demandes', function (Blueprint $table) {
                if (Schema::hasColumn('demandes', 'distance_travelled')) {
                    $table->dropColumn('distance_travelled');
                }
            });
        }
    }
};
