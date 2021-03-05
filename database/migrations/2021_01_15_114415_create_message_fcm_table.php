<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageFcmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_fcm', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->integer('mer_user_id')->default(0);
            $table->string('title',255)->default('');
            $table->string('content',255)->default('');
            $table->string('to_id',1000)->default('');
            $table->string('message_id',255)->default('');
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
        Schema::dropIfExists('message_fcm');
    }
}
