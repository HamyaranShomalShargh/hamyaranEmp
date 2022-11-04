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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contract_subset_id")->constrained("contract_subsets")->onDelete("cascade");
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            $table->enum("gender",['m','f'])->nullable();
            $table->string("national_code");
            $table->string("id_number")->nullable();
            $table->string("birth_date")->nullable();
            $table->string("birth_city")->nullable();
            $table->string("education")->nullable();
            $table->enum("marital_status",['m','s'])->nullable();
            $table->integer("children_number")->default(0);
            $table->string("insurance_number")->nullable();
            $table->string("insurance_days")->nullable();
            $table->enum("military_status",['h','e','n'])->nullable();
            $table->unsignedDecimal("basic_salary",20,2)->default(0);
            $table->unsignedDecimal("daily_wage",20,2)->default(0);
            $table->unsignedDecimal("worker_credit",20,2)->default(0);
            $table->unsignedDecimal("housing_credit",20,2)->default(0);
            $table->unsignedDecimal("child_credit",20,2)->default(0);
            $table->integer("job_group")->default(1);
            $table->string("bank_name")->nullable();
            $table->string("bank_account")->nullable();
            $table->string("credit_card")->nullable();
            $table->string("sheba_number")->nullable();
            $table->string("phone")->nullable();
            $table->string("mobile")->nullable();
            $table->string("address")->nullable();
            $table->boolean("unemployed")->default(0);
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
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
        Schema::dropIfExists('employees');
    }
};
