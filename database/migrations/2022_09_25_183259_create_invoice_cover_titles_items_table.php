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
        Schema::create('invoice_cover_titles_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("invoice_cover_id")->constrained("invoice_cover_titles")->onDelete("cascade");
            $table->string("name");
            $table->enum("kind",["text","number"]);
            $table->boolean("is_operable")->default(1);
            $table->timestamps();
            $table->timestamp("deleted_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_cover_titles_items');
    }
};
