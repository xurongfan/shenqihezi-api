<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysClientErrorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_client_error', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key',125)->default('');
            $table->text('error_msg');
            $table->string('device_uid',125)->default('');
            $table->string('ip',125)->default('');
            $table->string('country_name',50)->default('');
            $table->string('city_name',50)->default('');
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
        Schema::dropIfExists('sys_client_error');
    }
}
