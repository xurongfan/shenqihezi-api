<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticsRemainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statics_remain', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date',25)->default('');
            $table->integer('dru')->default(0);
            $table->integer('second_day')->default(0);
            $table->integer('third_day')->default(0);
            $table->integer('fourth_day')->default(0);
            $table->integer('fiveth_day')->default(0);
            $table->integer('seventh_day')->default(0);
            $table->integer('fourteenth_day')->default(0);
            $table->integer('thirtieth_day')->default(0);
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
        Schema::dropIfExists('statics_remain');
    }
}
