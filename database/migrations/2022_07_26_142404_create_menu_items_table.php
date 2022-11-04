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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("menu_header_id")->constrained("menu_headers")->onDelete("cascade");
            $table->foreignId("parent_id")->nullable()->constrained("menu_items")->onDelete("cascade");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("name");
            $table->string("short_name")->nullable();
            $table->string("main_route")->nullable();
            $table->string("icon")->nullable();
            $table->string("route")->nullable();
            $table->integer("priority")->default(0);
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
        Schema::dropIfExists('menu_items');
    }
};
