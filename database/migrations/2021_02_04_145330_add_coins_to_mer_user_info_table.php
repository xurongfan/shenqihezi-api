<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoinsToMerUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mer_user_info', function (Blueprint $table) {
            $table->unsignedInteger('coins')->default('0');
            $table->unsignedTinyInteger('first_wechat_bind')->default('0')->comment('是否领取微信奖励1已领取');
            $table->unsignedTinyInteger('first_play_game')->default('0')->comment('是否领取首次游戏奖励1已领取');
            $table->integer('total_game_time')->default('0')->comment('总游戏时长');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mer_user_info', function (Blueprint $table) {
            //
        });
    }
}
