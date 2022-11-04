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
        Schema::create('advantage_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("advantage_id")->constrained("advantages")->onDelete("cascade");
            $table->json("texts")->nullable();
            $table->json("files")->nullable();
            $table->boolean("period")->default(0);
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
        Schema::dropIfExists('advantage_attachments');
    }
};
