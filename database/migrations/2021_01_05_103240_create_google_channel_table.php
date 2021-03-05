<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_google', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('')->comment('渠道名称');
            $table->string('key')->default('');
            $table->string('google_reward_key')->default('');
            $table->string('google_interstitial_key')->default('');
            $table->string('trad_plus_reward_key')->default('');
            $table->string('trad_plus_interstitial_key')->default('');
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
        Schema::dropIfExists('google_channel');
    }
}
