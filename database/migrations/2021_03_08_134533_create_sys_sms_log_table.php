<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysSmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_sms_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_code',25)->default('');
            $table->string('phone',125)->default('');
            $table->string('content',525)->default('');
            $table->string('template_code',25)->default('');
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
        Schema::dropIfExists('sys_sms_log');
    }
}
