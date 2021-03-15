<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',50)->default('');
            $table->string('title_en',50)->default('');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('game_package_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->default(0);
            $table->integer('type_id')->default(0);
            $table->index('type_id','type_id');
            $table->index('package_id','package_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_type');
        Schema::dropIfExists('game_package_type');
    }
}
