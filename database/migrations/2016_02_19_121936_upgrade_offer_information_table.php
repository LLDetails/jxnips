<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeOfferInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_information', function (Blueprint $table) {
            $table->dropColumn('companies');
            $table->dropColumn('delivery_modes');
            $table->dropColumn('price');
            $table->jsonb('prices')->nullable();
            $table->jsonb('addresses')->nullable();
            $table->jsonb('prices_with_addresses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_information', function (Blueprint $table) {
            //
        });
    }
}
