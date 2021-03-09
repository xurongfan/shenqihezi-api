<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegSourceFieldToStaticsRemainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statics_remain', function (Blueprint $table) {
            $table->longText('reg_source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statics_remain', function (Blueprint $table) {
            $table->dropColumn('reg_source');
        });
    }
}
