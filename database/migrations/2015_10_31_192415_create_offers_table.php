<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bid_id')->index();
            $table->foreign('bid_id')->references('id')->on('bids');
            $table->unsignedInteger('demand_id')->index();
            $table->foreign('demand_id')->references('id')->on('demands');
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('delivery_mode');
            $table->decimal('delivery_costs', 10, 2);
            $table->decimal('price', 10, 2)->index();
            $table->decimal('quantity_floor', 10, 2);
            $table->decimal('quantity_caps', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('offers');
    }
}
