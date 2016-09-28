<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeBasketLogsTable1512070619 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basket_logs', function (Blueprint $table) {
            $table->dropColumn('collected_at');
            $table->timestamp('bided_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basket_logs', function (Blueprint $table) {
            //
        });
    }
}
