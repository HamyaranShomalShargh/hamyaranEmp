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
        Schema::table('invoice_automation', function (Blueprint $table) {
            $table->foreignId("invoice_cover_title_id")->constrained("invoice_cover_titles")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_automation', function (Blueprint $table) {
            $table->dropForeign("invoice_automation_invoice_cover_title_id_foreign");
            $table->dropColumn("invoice_cover_title_id");
        });
    }
};
