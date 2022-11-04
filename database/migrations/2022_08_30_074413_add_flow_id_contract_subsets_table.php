<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_subsets', function (Blueprint $table) {
            $table->foreignId("performance_flow_id")->nullable()->constrained("automation_flow");
            $table->foreignId("invoice_flow_id")->nullable()->constrained("automation_flow");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_subsets', function (Blueprint $table) {
            $table->dropForeign("contract_subsets_performance_flow_id_foreign");
            $table->dropForeign("contract_subsets_invoice_flow_id_foreign");
            $table->dropColumn("performance_flow_id");
            $table->dropColumn("invoice_flow_id");
        });
    }
};
