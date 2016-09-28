<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('basket_id')->index();
            $table->foreign('basket_id')->references('id')->on('baskets');
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedInteger('goods_id')->index();
            $table->foreign('goods_id')->references('id')->on('goods');
            $table->decimal('quantity', 10, 2);
            $table->string('price_validity'); //报价有效期
            $table->decimal('price_floor');
            $table->decimal('price_caps');
            $table->timestamp('delivery_date_start');
            $table->timestamp('delivery_date_stop');
            $table->jsonb('assign_rule');
            $table->jsonb('history');
            $table->string('state', 30)->index();
            $table->jsonb('states');
            $table->unsignedInteger('trigger')->nullable()->index();
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
        Schema::drop('demands');
    }
}
