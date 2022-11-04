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
            $table->dropColumn("performance_attributes");
            $table->dropColumn("invoice_attributes");
            $table->foreignId("performance_attributes_id")->nullable()->constrained("table_attributes")->onDelete("cascade");
            $table->foreignId("invoice_attributes_id")->nullable()->constrained("table_attributes")->onDelete("cascade");
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
            $table->json("performance_attributes")->nullable();
            $table->json("invoice_attributes")->nullable();
            $table->dropForeign("contract_subsets_performance_attributes_id_foreign");
            $table->dropForeign("contract_subsets_invoice_attributes_id_foreign");
            $table->dropColumn("performance_attributes_id");
            $table->dropColumn("invoice_attributes_id");
        });
    }
};
