<?php

namespace App\Http\Controllers;


use App\Models\EmployeeJob;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\ReportEmployee;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * report employeefunction
     */
    // add report to employee
    public function storeReport(Request $request, $job_id, $employee_id)
    {
        // check job id
        $job = Job::find($job_id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check this job belongs to this enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise' | $job->enterprise_id != $user->id) {
            return response()->json([
                'message' => 'this job does not belong to this enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        // check job status is accepted
        $employeeJob = EmployeeJob::where([
            ['job_id', $job_id],
            ['employee_id', $employee_id]
        ])->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($employeeJob->status != "accepted") {
            return response()->json([
                'message' => 'You can not report unaccepted job',
            ], Response::HTTP_BAD_REQUEST);
        }

        $field = $request->validate([
            "reason" => "required|string",
        ]);

        // check [job_id and employee_id] is unique
        $reportEmployee = ReportEmployee::where([
            ['enterprise_id', $job->enterprise_id],
            ['employee_id', $employee_id]
        ])->get();
        if ($reportEmployee->count() > 0) {
            return response()->json([
                'message' => 'Your report already exists'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add employee_id to feild
        $field['employee_id'] = $employee_id;
        // add job_id to feild
        $field['enterprise_id'] = $job->enterprise_id;

        $reportEmployee = ReportEmployee::create($field);

        return response()->json([
            'reportEmployee' => $reportEmployee,
            'message' => 'Report employee created'
        ], Response::HTTP_CREATED);
    }

    // update report employee
    public function updateReport(Request $request,$job_id, $employee_id, $report_id)
    {
        // check job id
        $job = Job::find($job_id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check this job belongs to this enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise' | $job->enterprise_id != $user->id) {
            return response()->json([
                'message' => 'this job does not belong to this enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        $reportEmployee = ReportEmployee::find($report_id);
        if (!$reportEmployee) {
            return response()->json([
                'message' => 'Report employee not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($reportEmployee->enterprise_id != $job->enterprise_id || $reportEmployee->employee_id != $employee_id) {
            return response()->json([
                'message' => 'This is not your report'
            ], Response::HTTP_FORBIDDEN);
        }

        $field = $request->validate([
            "reason" => "required|string",
        ]);

        $reportEmployee->update($field);

        return response()->json([
            'reportEmployee' => $reportEmployee,
            'message' => 'Report employee updated'
        ], Response::HTTP_OK);
    }

    // delete report employee
    public function destroyReport($job_id, $employee_id, $report_id)
    {
        // check job id
        $job = Job::find($job_id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check this job belongs to this enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise' | $job->enterprise_id != $user->id) {
            return response()->json([
                'message' => 'this job does not belong to this enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        $reportEmployee = ReportEmployee::find($report_id);
        if (!$reportEmployee) {
            return response()->json([
                'message' => 'Report job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($reportEmployee->enterprise_id != $job->enterprise_id || $reportEmployee->employee_id != $employee_id) {
            return response()->json([
                'message' => 'This is not your report'
            ], Response::HTTP_FORBIDDEN);
        }

        $reportEmployee->delete();

        return response()->json([
            'message' => 'Report employee deleted'
        ], Response::HTTP_OK);
    }

    // get all reports of employee
    public function getReports($job_id, $employee_id)
    {
        // check job id
        $job = Job::find($job_id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check this job belongs to this enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise' | $job->enterprise_id != $user->id) {
            return response()->json([
                'message' => 'this job does not belong to this enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        $reportEmployees = ReportEmployee::where('employee_id', $employee_id)->get();
        if (!$reportEmployees) {
            return response()->json([
                'message' => 'Report jobs not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $reportEmployees->each(function ($reportEmployee) {
            $reportEmployee->enterprise_name = Enterprise::where('user_id', $reportEmployee->enterprise_id)->first()->name;
        });

        return response()->json([
            'reportEmployees' => $reportEmployees,
            'message' => 'Report employees found'
        ], Response::HTTP_OK);
    }
}
