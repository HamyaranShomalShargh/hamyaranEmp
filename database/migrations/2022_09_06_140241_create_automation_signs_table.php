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
        Schema::create('automation_signs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("signable_id");
            $table->string("signable_type");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("sign")->nullable();
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
        Schema::dropIfExists('automation_signs');
    }
};
