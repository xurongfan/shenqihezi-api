<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpFieldToMerUserGameHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mer_user_game_history', function (Blueprint $table) {
            $table->string('country_code',25)->default('');
            $table->string('country_name',25)->default('');
            $table->string('city_name',25)->default('');
            $table->string('latitude',25)->default('');
            $table->string('longitude',25)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mer_user_game_history', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('country_name');
            $table->dropColumn('city_name');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
