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
        Schema::create('advantage_automation', function (Blueprint $table) {
            $table->id();
            $table->foreignId("role_id")->constrained("roles")->onDelete("cascade");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
            $table->foreignId("advantage_id")->constrained("advantages")->onDelete("cascade");
            $table->string("contract")->nullable();
            $table->string("advantage_form")->nullable();
            $table->enum("type",["add","remove"])->nullable();
            $table->json("texts")->nullable();
            $table->json("files")->nullable();
            $table->string("start_month")->nullable();
            $table->string("end_month")->nullable();
            $table->integer("role_priority");
            $table->boolean("is_committed")->default(0);
            $table->boolean("is_referred")->default(0);
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
        Schema::dropIfExists('advantage_automation');
    }
};
