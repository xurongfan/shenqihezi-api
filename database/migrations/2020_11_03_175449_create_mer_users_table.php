<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       \Illuminate\Support\Facades\DB::select("CREATE TABLE `mer_users` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `profile_img` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `nick_name` varchar(50) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '昵称',
  `description` varchar(1025) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '描述',
  `area_code` varchar(25) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '区号',
  `phone` varchar(25) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '手机号',
  `sex` enum('male','female','none') COLLATE utf8mb4_bin NOT NULL DEFAULT 'male' COMMENT '性别',
  `birth` varchar(25) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '生日',
  `last_login_ip` varchar(25) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `last_login_date` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态',
  `facebook_auth_code` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `google_auth_code` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '',  
  `wechat_auth_code` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `device_uid` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '注册设备号',
  `vip` tinyint(1) NOT NULL DEFAULT '0',
   `vip_start_at` datetime DEFAULT NULL COMMENT 'vip开始时间',
  `vip_end_at` datetime DEFAULT NULL COMMENT 'vip结束时间',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL COMMENT '头像',
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
        Schema::dropIfExists('mer_users');
    }
}
