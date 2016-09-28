<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 30)->unique();
            $table->string('password', 60);
            $table->string('phone', 15)->nullable();
            $table->unsignedInteger('role_id')->index();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedInteger('area_id')->nullable()->index();
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->enum('type', ['staff', 'supplier']);
            $table->boolean('allow_login')->default(true);
            $table->boolean('accept_agreement')->default(false);
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
