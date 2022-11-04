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
        Schema::create('contract_subsets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contract_id")->constrained("contracts")->onDelete("cascade");
            $table->foreignId("parent_id")->nullable()->constrained("contract_subsets")->onDelete("cascade");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("name");
            $table->string("workplace")->nullable();
            $table->boolean("files")->default(0);
            $table->boolean("inactive")->default(0);
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
        Schema::dropIfExists('contract_subsets');
    }
};
