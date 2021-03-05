<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->unsignedInteger('days')->default('0');
            $table->decimal('amount')->default('0.00');
            $table->string('google_pay_id')->default('');
            $table->unsignedTinyInteger('is_vip')->default('0')->comment('0游戏订阅1vip');
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
        Schema::dropIfExists('pay_project');
    }
}
