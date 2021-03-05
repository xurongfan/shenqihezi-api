<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysOperateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::select("CREATE TABLE `sys_operate_log` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `method` varchar(25) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `route` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_bin,
  `response` text COLLATE utf8mb4_bin,
  `error_message` text COLLATE utf8mb4_bin,
  `user_id` int(13) DEFAULT NULL,
  `ip` varchar(25) COLLATE utf8mb4_bin DEFAULT NULL,
  `device_uid` varchar(0) COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_operate_log');
    }
}
