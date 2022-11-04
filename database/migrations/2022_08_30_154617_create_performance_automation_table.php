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
        Schema::create('performance_automation', function (Blueprint $table) {
            $table->id();
            $table->foreignId("authorized_date_id")->constrained("automation_authorized_date")->onDelete("cascade");
            $table->foreignId("role_id")->constrained("roles")->onDelete("cascade");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("contract_subset_id")->constrained("contract_subsets")->onDelete("cascade");
            $table->integer("role_priority");
            $table->boolean("is_read")->default(0);
            $table->boolean("is_finished")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_automation');
    }
};
