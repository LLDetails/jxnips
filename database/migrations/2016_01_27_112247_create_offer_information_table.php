<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_information', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('offer_basket_id')->index();
            $table->foreign('offer_basket_id')->references('id')->on('offer_baskets');
            $table->unsignedInteger('supplier_id')->index();
            $table->foreign('supplier_id')->references('id')->on('users');
            $table->unsignedInteger('goods_id')->index();
            $table->foreign('goods_id')->references('id')->on('goods');
            $table->text('quality_standard');
            $table->decimal('quantity', 13, 5);
            $table->decimal('price', 10, 2);
            $table->jsonb('companies');
            $table->jsonb('delivery_modes');
            $table->string('payment');
            $table->string('price_validity', 30);
            $table->timestamp('delivery_start');
            $table->timestamp('delivery_stop');
            $table->boolean('bargaining');
            $table->enum('state', ['created', 'published'])->index();
            $table->softDeletes();
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
        Schema::drop('offer_information');
    }
}
