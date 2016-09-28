<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeContractGradesTable1512181926 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_grades', function (Blueprint $table) {
            $table->dropColumn('company_graded_at');
            $table->dropColumn('supplier_graded_at');
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
