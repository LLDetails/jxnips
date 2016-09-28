<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_id')->index();
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->jsonb('company_grade');
            $table->timestamp('company_graded_at');
            $table->jsonb('supplier_grade');
            $table->timestamp('supplier_graded_at');
            $table->text('company_remark');
            $table->text('supplier_remark');
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
        Schema::drop('contract_grades');
    }
}
