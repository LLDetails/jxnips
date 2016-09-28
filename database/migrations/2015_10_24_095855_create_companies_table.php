<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('area_id')->index();
            $table->foreign('area_id')->references('id')->on('areas');
            $table->string('name', 60)->unique();
            $table->string('code', 10)->unique();
            $table->string('delivery_address', 100);
            $table->jsonb('addition');
            $table->jsonb('grade')->nullable();
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
        Schema::drop('companies');
    }
}
