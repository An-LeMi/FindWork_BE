<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('language')->nullable();
            $table->text('certificates')->nullable();
            $table->text('overview')->nullable();
            $table->text('work_history')->nullable();
            $table->text('education')->nullable();
            $table->text('visibility')->nullable();
            $table->timestamps();
            // user_id is the foreign key to the users table and is the primary key
            $table->unsignedBigInteger('user_id')->primary();
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
}
