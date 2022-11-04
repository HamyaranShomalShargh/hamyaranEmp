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
        Schema::table('advantages', function (Blueprint $table) {
            $table->foreignId("automation_flow_id")->nullable()->constrained("automation_flow")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advantages', function (Blueprint $table) {
            $table->dropForeign("advantages_automation_flow_id_foreign");
            $table->dropColumn("automation_flow_id");
        });
    }
};
