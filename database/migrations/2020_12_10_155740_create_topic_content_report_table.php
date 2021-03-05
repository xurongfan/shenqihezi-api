<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicContentReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_content_report', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mer_user_id')->default('0');
            $table->unsignedInteger('content_id')->default('0');
            $table->unsignedInteger('topic_id')->default('0');
            $table->unsignedInteger('comment_id')->default('0');
            $table->longText('report_content')->nullable();
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
        Schema::dropIfExists('topic_content_report');
    }
}
