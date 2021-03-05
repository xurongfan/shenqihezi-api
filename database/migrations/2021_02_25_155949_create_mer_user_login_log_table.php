<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerUserLoginLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mer_user_login_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('mer_user_id')->default(0);
            $table->dateTime('last_login_at')->nullable();
            $table->dateTime('register_at')->nullable();
            $table->string('device_uid',255)->nullable();
            $table->string('last_login_ip',125)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mer_user_login_log');
    }
}
