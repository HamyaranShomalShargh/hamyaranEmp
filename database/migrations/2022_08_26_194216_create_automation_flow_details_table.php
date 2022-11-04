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
        Schema::create('automation_flow_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("automation_flow_id")->constrained("automation_flow")->onDelete("cascade");
            $table->foreignId("role_id")->constrained("roles")->onDelete("cascade");
            $table->integer("priority");
            $table->boolean("is_main_role")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('automation_flow_details');
    }
};
