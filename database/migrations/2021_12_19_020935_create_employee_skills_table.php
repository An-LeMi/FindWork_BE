<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            // foreign key to the employees table
            $table->unsignedBigInteger('employee_id');
            // foreign key to the skills table
            $table->unsignedBigInteger('skill_id');
            $table->integer('level');
            $table->integer('years_of_experience');
            // employee_id and skill_id must be unique
            $table->unique(['employee_id', 'skill_id']);
            
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
        Schema::dropIfExists('employee_skills');
    }
}
