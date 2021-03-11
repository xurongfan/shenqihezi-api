<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticsGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statics_game', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date',25)->default('');
            $table->integer('count')->default(0);
            $table->string('city_name',25)->default('');
            $table->string('country_code',25)->default('');
            $table->string('country_name',25)->default('');
            $table->integer('duration_total')->default(0);
            $table->integer('game_package_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statics_game');
    }
}
