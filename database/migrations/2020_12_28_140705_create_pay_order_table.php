<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \Illuminate\Support\Facades\DB::select("CREATE TABLE `pay_order` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `mer_user_id` int(13) unsigned NOT NULL DEFAULT '0',
  `order_num` varchar(125) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `currency_code` varchar(25) COLLATE utf8mb4_bin NOT NULL DEFAULT '',  
  `pay_project_id` int(13) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
  `pay_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) unsigned DEFAULT '0',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1vip2订阅',
  `game_package_id` int(13) unsigned NOT NULL DEFAULT '0',
  `pay_time` datetime DEFAULT NULL,
  `desc` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `request_data` text COLLATE utf8mb4_bin,
  `cancel_reason` tinyint(1) DEFAULT '1',
  `cancel_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_order');
    }
}
