<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mer_user_id')->default('0')->comment('用户');
            $table->unsignedInteger('originate_user_id')->default('0');
            $table->unsignedTinyInteger('type')->default('1')->comment('1评论2点赞3评论点赞');
            $table->unsignedInteger('content_id')->default('0')->comment('话题内容id');
            $table->unsignedInteger('comment_id')->default('0')->comment('评论id');
            $table->unsignedTinyInteger('status')->default('1')->comment('1');
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
        Schema::dropIfExists('notice');
    }
}
