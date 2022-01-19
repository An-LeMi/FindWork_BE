<?php

namespace App\Http\Controllers;

use App\Models\EmployeeJob;
use App\Models\Employee;
use App\Models\ReportJob;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportJobController extends Controller
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
     * report function
     */
    // add report to job
    public function storeReport(Request $request, $employee_id, $job_id)
    {
        $employee = Employee::find($employee_id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check employee_id is this user_id
        if ($employee->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'This employee id is not this user'
            ], Response::HTTP_BAD_REQUEST);
        }

        // check job status is accepted
        $employeeJob = EmployeeJob::where([
            ['job_id', $job_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($employeeJob->status != "accepted") {
            return response()->json([
                'message' => 'You can not report unaccpeted job',
            ], Response::HTTP_BAD_REQUEST);
        }

        $field = $request->validate([
            "reason" => "required|string",
        ]);

        // check [job_id and employee_id] is unique
        $reportJob = ReportJob::where([
            ['job_id', $job_id],
            ['employee_id', $employee->user_id]
        ])->get();
        if ($reportJob->count() > 0) {
            return response()->json([
                'message' => 'Your report already exists'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add employee_id to feild
        $field['employee_id'] = $employee->user_id;
        // add job_id to feild
        $field['job_id'] = $job_id;

        $reportJob = ReportJob::create($field);

        return response()->json([
            'reportJob' => $reportJob,
            'message' => 'Report job created'
        ], Response::HTTP_CREATED);
    }

    // update report
    public function updateReport(Request $request, $employee_id, $job_id, $report_id)
    {
        $employee = Employee::find($employee_id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check employee_id is this user_id
        if ($employee->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'This employee id is not this user'
            ], Response::HTTP_BAD_REQUEST);
        }

        $reportJob = ReportJob::find($report_id);
        if (!$reportJob) {
            return response()->json([
                'message' => 'Report job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($reportJob->job_id != $job_id || $reportJob->employee_id != $employee_id) {
            return response()->json([
                'message' => 'This is not your report'
            ], Response::HTTP_FORBIDDEN);
        }

        $field = $request->validate([
            "reason" => "required|string",
        ]);

        $reportJob->update($field);

        return response()->json([
            'reportJob' => $reportJob,
            'message' => 'Report job updated'
        ], Response::HTTP_OK);
    }

    // delete report job
    public function destroyReport($employee_id, $job_id, $report_id)
    {
        $employee = Employee::find($employee_id);
        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check employee_id is this user_id
        if ($employee->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'This employee id is not this user'
            ], Response::HTTP_BAD_REQUEST);
        }

        $reportJob = ReportJob::find($report_id);
        if (!$reportJob) {
            return response()->json([
                'message' => 'Report job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($reportJob->job_id != $job_id || $reportJob->employee_id != $employee_id) {
            return response()->json([
                'message' => 'This is not your report'
            ], Response::HTTP_FORBIDDEN);
        }

        $reportJob->delete();

        return response()->json([
            'message' => 'Report job deleted'
        ], Response::HTTP_OK);
    }

    // get all report of job
    public function getReports($employee_id, $job_id)
    {
        $employee = Employee::find($employee_id);
        if (!$employee) {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // check employee_id is this user_id
        if ($employee->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'This employee_id is not this user'
            ], Response::HTTP_BAD_REQUEST);
        }

        $reportJobs = ReportJob::where('job_id', $job_id)->get();
        if (!$reportJobs) {
            return response()->json([
                'message' => 'Report jobs not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $reportJobs->each(function ($reportJob) {
            $reportJob->employee_firstname = Employee::where('user_id', $reportJob->employee_id)->first()->first_name;
            $reportJob->employee_lastname = Employee::where('user_id', $reportJob->employee_id)->first()->last_name;
        });

        return response()->json([
            'reportJobs' => $reportJobs,
            'message' => 'Report jobs found'
        ], Response::HTTP_OK);
    }
    // end employee report
}
