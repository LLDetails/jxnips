<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasketLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basket_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('basket_id')->index();
            $table->foreign('basket_id')->references('id')->on('baskets');
            $table->string('action', 30);
            $table->unsignedInteger('user_id')->nullable();
            $table->text('remark');
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
        Schema::drop('basket_logs');
    }
}
