<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_content', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mer_user_id')->default('0');
            $table->text('content')->nullable();
            $table->longText('image_resource')->nullable();
            $table->tinyInteger('is_anonymous')->default('0');
            $table->tinyInteger('status')->default('1');
//            $table->unsignedInteger('like_count')->default('0');
            $table->dateTime('last_comment_at')->nullable()->comment('最后评论时间');
            $table->string('longitude')->default('')->comment('经度');
            $table->string('latitude')->default('')->comment('纬度');
            $table->string('ip')->default('')->comment('ip');
            $table->longText('position_info')->nullable();
//            $table->tinyInteger('status')->default('0');
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
        Schema::dropIfExists('topic_content');
    }
}
