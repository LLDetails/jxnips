<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeContractGradesTable1512181927 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_grades', function (Blueprint $table) {
            $table->timestamp('company_graded_at')->nullable();
            $table->timestamp('supplier_graded_at')->nullable();
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
