<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('area_id')->index();
            $table->foreign('area_id')->references('id')->on('areas');
            $table->string('name', 60)->unique();
            $table->jsonb('goods')->default('[]');
            $table->enum('type', ['个体户', '企业法人', '自然人']);
            $table->text('address')->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->jsonb('tel')->default('[]');
            $table->string('fax', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 128)->nullable();
            $table->string('business_license', 100)->nullable();
            $table->string('organization_code', 100)->nullable();
            $table->string('tax_id', 100)->nullable();
            $table->decimal('registered_capital', 10, 2)->nullable();
            $table->unsignedSmallInteger('company_scale')->nullable();
            $table->string('id_number', 20)->nullable();
            $table->string('conact', 10)->nullable();
            $table->string('bank', 100)->nullable();
            $table->string('bank_account', 40)->nullable();
            $table->jsonb('grade')->nullable();
            $table->jsonb('addition')->nullable();
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
        Schema::drop('suppliers');
    }
}
