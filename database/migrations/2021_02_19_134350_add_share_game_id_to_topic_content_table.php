<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShareGameIdToTopicContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topic_content', function (Blueprint $table) {
            $table->integer('game_package_id')->default(0);
            $table->tinyInteger('is_export')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topic_content', function (Blueprint $table) {
            $table->dropColumn('game_package_id');
        });
    }
}
