<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::select("CREATE TABLE `game_package` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `icon_img` varchar(255) DEFAULT '' COMMENT 'icon图片',
  `background_img` varchar(255) DEFAULT '' COMMENT '背景主图',
  `url` varchar(255) DEFAULT '' COMMENT '游戏地址',
  `is_crack` tinyint(1) DEFAULT 0 COMMENT '是否破解',
  `crack_url` varchar(255) DEFAULT '' COMMENT '破解游戏地址',
  `crack_des` text ,
  `is_landscape` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否横屏',
  `is_rank` tinyint(1) NOT NULL COMMENT '是否排名',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态',
   `is_rec` tinyint(1) unsigned NOT NULL DEFAULT '0',
   `like_base` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢人数基数值',
   `integral_base` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '积分平均值',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_package');
    }
}
