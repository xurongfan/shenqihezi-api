<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicContentCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_content_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('content_id')->default('0');
            $table->unsignedInteger('pid')->default('0')->nullable()->comment('顶级评论id');
            $table->unsignedInteger('fid')->default('0')->nullable()->comment('父评论id');
            $table->unsignedInteger('mer_user_id')->default('0')->comment('发布人');
            $table->unsignedInteger('reply_user_id')->default('0')->comment('回复用户');
            $table->unsignedInteger('like_count')->default('0')->comment('点赞数');
            $table->text('comment')->nullable()->comment('评论');
            $table->string('ip')->default('')->comment('ip');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_content_comment');
    }
}
