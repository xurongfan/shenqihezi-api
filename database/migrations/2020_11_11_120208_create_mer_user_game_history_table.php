<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerUserGameHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::select("CREATE TABLE `mer_user_game_history` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(125) NOT NULL DEFAULT '',
  `mer_user_id` int(13) NOT NULL DEFAULT '0',
  `game_package_id` int(13) unsigned NOT NULL,
  `duration` int(13) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
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
        Schema::dropIfExists('mer_user_game_history');
    }
}
