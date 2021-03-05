<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdBuryingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::select("CREATE TABLE `ad_burying` (
          `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
          `package_id` int(10) NOT NULL DEFAULT 0,
          `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '1展示2点击',
          `show_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1开屏2视频3插屏4横幅',
          `ip` varchar(25) NOT NULL DEFAULT '',
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL,
          `device_uid` varchar(125) NOT NULL DEFAULT '' COMMENT '设备号',
          `uid` varchar(125) NOT NULL DEFAULT '' COMMENT '',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_burying');
    }
}
