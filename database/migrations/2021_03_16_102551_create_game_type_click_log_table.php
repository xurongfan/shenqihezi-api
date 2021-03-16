<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTypeClickLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_type_click_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mer_user_id')->default(0);
            $table->integer('game_type_id')->default(0);
            $table->string('ip',25)->default('');
            $table->string('country_code',25)->default('');
            $table->string('country_name',25)->default('');
            $table->string('city_name',25)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_type_click_log');
    }
}
