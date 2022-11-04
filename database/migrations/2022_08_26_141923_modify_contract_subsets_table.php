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
            $table->integer("registration_start_day")->nullable();
            $table->integer("registration_final_day")->nullable();
            $table->integer("overtime_registration_limit")->nullable();
            $table->json("performance_attributes")->nullable();
            $table->json("invoice_attributes")->nullable();
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
            $table->dropColumn("registration_start_day");
            $table->dropColumn("registration_final_day");
            $table->dropColumn("overtime_registration_limit");
            $table->dropColumn("performance_attributes");
            $table->dropColumn("invoice_attributes");
        });
    }
};
