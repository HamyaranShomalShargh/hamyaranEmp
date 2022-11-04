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
            $table->foreignId("invoice_cover_id")->nullable()->constrained("invoice_cover_titles")->onDelete("cascade");
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
            $table->dropForeign('contract_subsets_invoice_cover_id_foreign');
            $table->dropColumn("invoice_cover_id");
        });
    }
};
