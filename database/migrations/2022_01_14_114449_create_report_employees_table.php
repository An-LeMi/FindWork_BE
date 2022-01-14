<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_employees', function (Blueprint $table) {
            $table->id();
            // foreign key to the employee table
            $table->unsignedBigInteger('employee_id');
            // foreign key to the jobs table
            $table->unsignedBigInteger('enterprise_id');
            $table->text('reason')->nullable();
            // employee_id and enterprise_id must be unique
            $table->unique(['employee_id', 'enterprise_id']);

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
        Schema::dropIfExists('report_employees');
    }
}
