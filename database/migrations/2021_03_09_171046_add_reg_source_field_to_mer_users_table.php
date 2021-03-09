<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegSourceFieldToMerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mer_users', function (Blueprint $table) {
            $table->tinyInteger('reg_source')->default(1)->comment('1手机号2fb3google4wechat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mer_users', function (Blueprint $table) {
            $table->dropColumn('reg_source');
        });
    }
}
