<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// public routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('user/{id}/update_password', [UserController::class, 'update_password']);

    /**
     * enterprise resource
     */
    Route::resource('enterprise', EnterpriseController::class)->except(['create', 'edit']);
    // get all jobs of enterprise
    Route::get('enterprise/{enterprise}/jobs', [EnterpriseController::class, 'getJobs']);
    // end enterprise resource

    /**
     * Employee resource
     */
    Route::resource('employee', EmployeeController::class);
    // add skill to employee
    Route::post('employee/{employee}/skill', [EmployeeController::class, 'storeSkill']);
    // search employee
    Route::get('employee/search/{name}', [EmployeeController::class, 'searchEmployeeName']);

    // employee-skill relationship
    // delete skill from employee
    Route::delete('employee/{employee}/skill/{skill}', [EmployeeController::class, 'destroySkill']);
    // edit skill of employee
    Route::put('employee/{employee}/skill/{skill}', [EmployeeController::class, 'updateSkill']);
    // show a skill of employee
    Route::get('employee/{employee}/skill/{skill}', [EmployeeController::class, 'showSkill']);
    // get all skills of employee
    Route::get('employee/{employee}/skills', [EmployeeController::class, 'getSkills']);

    // employee-job relationship
    // add offer (employee request job)
    Route::post('employee/{employee}/job', [EmployeeController::class, 'storeOffer']);
    // delete offer (employee cancel job)
    Route::delete('employee/{employee}/job/{job}', [EmployeeController::class, 'destroyOffer']);
    // edit offer (employee update offer from job)
    Route::put('employee/{employee}/job/{job}', [EmployeeController::class, 'updateOffer']);
    // show a offer
    Route::get('employee/{employee}/job/{job}', [EmployeeController::class, 'showOffer']);
    // get all offers of employee
    Route::get('employee/{employee}/jobs', [EmployeeController::class, 'getOffers']);
    // get all invited jobs of employee
    Route::get('employee/{employee}/invited',[EmployeeController::class, 'getInvitedJobs']);
    // get alls job (order by number of similar skill)
    Route::get('employee/{employee}/jobs/match', [EmployeeController::class, 'showJobsMatch']);
    // get jobs by recent
    Route::get('employee/{employee}/jobs/recent', [EmployeeController::class, 'showJobsRecent']);

    // report-job relationship
    // create report
    Route::post('employee/{employee}/job/{job}/report', [EmployeeController::class, 'storeReport']);
    // update report
    Route::put('employee/{employee}/job/{job}/report/{report}', [EmployeeController::class, 'updateReport']);
    // delete report
    Route::delete('employee/{employee}/job/{job}/report/{report}', [EmployeeController::class, 'destroyReport']);
    // show all report
    Route::get('employee/{employee}/job/{job}/reports', [EmployeeController::class, 'getReports']);
    // end employee resource

    /**
     * Job resource
     */
    Route::resource('job', JobController::class);
    // add skill to job
    Route::post('job/{job}/skill', [JobController::class, 'storeSkill']);
    // delete skill from job
    Route::delete('job/{job}/skill/{skill}', [JobController::class, 'destroySkill']);
    // edit skill of job
    Route::put('job/{job}/skill/{skill}', [JobController::class, 'updateSkill']);
    // get all skills of a job
    Route::get('job/{job}/skills', [JobController::class, 'getSkills']);
    // search job by name
    Route::get('job/search/{title}',[JobController::class, 'searchJobTitle']);

    // employer-job relationship
    // add offer (job request employee)
    Route::post('job/{job}/employee', [JobController::class, 'storeOffer']);
    // delete offer (job cancel employee)
    Route::delete('job/{job}/employee/{employee}', [JobController::class, 'destroyOffer']);
    // edit offer (job update offer from employee)
    Route::put('job/{job}/employee/{employee}', [JobController::class, 'updateOffer']);
    // show a offer
    Route::get('job/{job}/employee/{employee}', [JobController::class, 'showOffer']);
    // get all offers of job (for enterprise)
    Route::get('job/{job}/employees', [JobController::class, 'getOffers']);
    // rate employee
    Route::post('job/{job}/employee/{employee}/rating', [JobController::class, 'updateRating']);

    // report-employee relationship
    // create report
    Route::post('job/{job}/employee/{employee}/report', [JobController::class, 'storeReport']);
    // update report
    Route::put('job/{job}/employee/{employee}/report/{report}', [JobController::class, 'updateReport']);
    // delete report
    Route::delete('job/{job}/employee/{employee}/report/{report}', [JobController::class, 'destroyReport']);
    // show all report
    Route::get('job/{job}/employee/{employee}/reports', [JobController::class, 'getReports']);
    // end job resource

    // category resource
    Route::resource('category', CategoryController::class);
    // get all skills of category
    Route::get('category/{category}/skills', [CategoryController::class, 'getSkills']);
    // skill resource
    Route::resource('skill', SkillController::class);
    // search skill by name
    Route::get('skill/search/{name}', [SkillController::class, 'searchSkillName']);
});
