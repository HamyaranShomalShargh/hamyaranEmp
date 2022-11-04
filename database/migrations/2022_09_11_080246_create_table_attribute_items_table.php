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
        Schema::create('table_attribute_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("table_attribute_id")->constrained("table_attributes")->onDelete("cascade");
            $table->string("name");
            $table->enum("kind",["text","number"]);
            $table->enum("category",["function","advantage","deduction","note"])->nullable();
            $table->boolean("is_operable")->default(1);
            $table->text("condition")->nullable();
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
        Schema::dropIfExists('table_attribute_items');
    }
};
