<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeContractGradesTable1512201516 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_grades', function (Blueprint $table) {
            $table->dropColumn('company_grade_3');
            $table->dropColumn('company_remark');
            $table->dropColumn('supplier_remark');
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
