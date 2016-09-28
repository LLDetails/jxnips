<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bid_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('days');
            $table->unsignedInteger('bid_counts');
            $table->unsignedInteger('failed_bid_counts');
            $table->unsignedInteger('goods_counts');
            $table->decimal('quantity', 13, 5);
            $table->decimal('amount', 13, 5);
            $table->unsignedInteger('supplier_counts');
            $table->unsignedInteger('offer_counts');
            $table->date('generated_at')->index();
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
        Schema::drop('bid_counts');
    }
}
