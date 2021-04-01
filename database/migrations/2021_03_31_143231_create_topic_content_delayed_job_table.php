<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicContentDelayedJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_content_delayed_job', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_content_id')->default(0);
            $table->tinyInteger('content_type')->default(1);
            $table->text('extra_info');
            $table->dateTime('delayed_time');
            $table->dateTime('run_time');
            $table->tinyInteger('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_content_delayed_job');
    }
}
