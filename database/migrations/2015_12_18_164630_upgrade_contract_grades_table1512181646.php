<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeContractGradesTable1512181646 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_grades', function (Blueprint $table) {
            $table->dropColumn('company_grade');
            $table->dropColumn('supplier_grade');
            $table->unsignedSmallInteger('company_grade_1')->nullable()->index();
            $table->unsignedSmallInteger('company_grade_2')->nullable()->index();
            $table->unsignedSmallInteger('company_grade_3')->nullable()->index();
            $table->unsignedSmallInteger('supplier_grade_1')->nullable()->index();
            $table->unsignedSmallInteger('supplier_grade_2')->nullable()->index();
            $table->unsignedSmallInteger('supplier_grade_3')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_grades', function (Blueprint $table) {
            //
        });
    }
}
