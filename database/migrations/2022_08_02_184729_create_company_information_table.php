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
        Schema::create('company_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId("ceo_user_id")->nullable()->constrained("users")->onDelete("cascade");
            $table->foreignId("substitute_user_id")->nullable()->constrained("users")->onDelete("cascade");
            $table->foreignId("user_id")->nullable()->constrained("users")->onDelete("cascade");
            $table->string("ceo_title")->nullable();
            $table->string("substitute_title")->nullable();
            $table->string("name")->nullable();
            $table->string("short_name")->nullable();
            $table->string("description")->nullable();
            $table->string("registration_number")->nullable();
            $table->string("national_id")->nullable();
            $table->string("website")->nullable();
            $table->text("address")->nullable();
            $table->string("phone")->nullable();
            $table->string("fax")->nullable();
            $table->string("app_version")->nullable();
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
        Schema::dropIfExists('company_information');
    }
};
