<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerUserCoinsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mer_user_coins_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mer_user_id')->default('0');
            $table->unsignedTinyInteger('type')->default('1');
            $table->unsignedInteger('before_operate_amount')->default('0');
            $table->unsignedInteger('amount')->default('0');
            $table->unsignedInteger('after_operate_amount')->default('0');
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
        Schema::dropIfExists('mer_user_coins_log');
    }
}
