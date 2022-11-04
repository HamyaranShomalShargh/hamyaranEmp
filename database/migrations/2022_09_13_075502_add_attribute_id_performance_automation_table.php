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
        Schema::table('performance_automation', function (Blueprint $table) {
            $table->foreignId("attribute_id")->nullable()->constrained("table_attributes")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_automation', function (Blueprint $table) {
            $table->dropForeign("performance_automation_attribute_id_foreign");
            $table->dropColumn("attribute_id");
        });
    }
};
