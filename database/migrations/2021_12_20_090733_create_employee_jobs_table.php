<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            // foreign key to the employees table
            $table->unsignedBigInteger('employee_id');
            // foreign key to the jobs table
            $table->unsignedBigInteger('job_id');
            // offer driect
            $table->string('offer_direction')->nullable();
            $table->string('status')->default('pending');
            // employee_id and job_id must be unique
            $table->unique(['employee_id', 'job_id']);


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
        Schema::dropIfExists('employee_jobs');
    }
}
