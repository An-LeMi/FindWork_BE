<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSkill;
use App\Models\EmployeeJob;
use App\Models\Enterprise;
use App\Models\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $employees = Employee::all();
        return response()->json([
            'employees' => $employees,
        ], Response::HTTP_OK);
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
        $field = $request->validate([
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required|email|unique:employees",
        ]);

        // check user_id is not employee
        $user = auth()->user();
        if (!$user | $user->role != 'employee') {
            return response()->json([
                'message' => 'user_id is not employee'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add user_id to feild
        $field['user_id'] = $user->id;

        $employee = Employee::create($field);

        return response()->json([
            'employee' => $employee,
            'message' => 'employee created'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeSkills = EmployeeSkill::where('employee_id', $id)->get();
        $skills = [];
        $employeeSkills->each(function ($employeeSkill) use (&$skills) {
            $skills[] = $employeeSkill->skill;
        });

        return response()->json([
            'employee' => $employee,
            'skills' => $skills,
            'message' => 'employee found'
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $field = $request->validate([
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required|email|unique:employees"
        ]);

        // update
        $employee->update($field);


        return response()->json([
            'employee' => $employee,
            'message' => 'employee updated'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $employee->delete();
    }
    /**
     * skill function
     */
    // add skill to employee
    public function storeSkill(Request $request, $id)
    {
        $employee = Employee::find($id);
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

        $field = $request->validate([
            "skill_id" => "required|integer|exists:skills,id",
            "level" => "required|integer|between:1,5",
            "years_of_experience" => "required|integer|between:1,50",
        ]);

        // check [skill_id and employee_id] is unique
        $employeeSkill = EmployeeSkill::where([
            ['skill_id', $field['skill_id']],
            ['employee_id', $employee->user_id]
        ])->get();
        if ($employeeSkill->count() > 0) {
            return response()->json([
                'message' => 'This skill is already added'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add employee_id to feild
        $field['employee_id'] = $employee->user_id;

        $employeeSkill = EmployeeSkill::create($field);

        return response()->json([
            'employeeSkill' => $employeeSkill,
            'message' => 'employeeSkill created'
        ], Response::HTTP_CREATED);
    }

    // delete skill from employee /employee/{employee}/skill/{skill}
    public function destroySkill($id, $skill_id)
    {
        $employee = Employee::find($id);
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

        $employeeSkill = EmployeeSkill::where([
            ['skill_id', $skill_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeSkill) {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeSkill->delete();

        return response()->json([
            'message' => 'employeeSkill deleted'
        ], Response::HTTP_OK);
    }

    // edit skill of employee /employee/{employee}/skill/{skill}
    public function updateSkill(Request $request, $id, $skill_id)
    {
        $employee = Employee::find($id);
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

        $employeeSkill = EmployeeSkill::where([
            ['skill_id', $skill_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeSkill) {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $field = $request->validate([
            "level" => "required|integer|between:1,5",
            "years_of_experience" => "required|integer|between:1,50",
        ]);

        $employeeSkill->update($field);

        return response()->json([
            'employeeSkill' => $employeeSkill,
            'message' => 'employeeSkill updated'
        ], Response::HTTP_OK);
    }

    // show skill of employee /employee/{employee}/skill/{skill}
    public function showSkill($id, $skill_id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeSkill = EmployeeSkill::where([
            ['skill_id', $skill_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeSkill) {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeSkill->skill;

        return response()->json([
            'employeeSkill' => $employeeSkill,
            'message' => 'employeeSkill found'
        ], Response::HTTP_OK);
    }

    // get all skills of employee
    public function getSkills($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeSkills = EmployeeSkill::where('employee_id', $employee->user_id)->get();

        $employeeSkills->each(function ($employeeSkill) {
            $employeeSkill->skill;
        });

        return response()->json([
            'employeeSkills' => $employeeSkills,
            'message' => 'employeeSkills found'
        ], Response::HTTP_OK);
    }
    // end skill function

    /**
     * offer function
     */
    // add job to employee
    public function storeOffer(Request $request, $id)
    {
        $employee = Employee::find($id);
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

        $field = $request->validate([
            "job_id" => "required|integer|exists:jobs,id",
        ]);

        // check [job_id and employee_id] is unique
        $employeeJob = EmployeeJob::where([
            ['job_id', $field['job_id']],
            ['employee_id', $employee->user_id]
        ])->get();
        if ($employeeJob->count() > 0) {
            return response()->json([
                'message' => 'This job is already added'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add employee_id to feild
        $field['employee_id'] = $employee->user_id;
        // add offer_direction to feild
        $field['offer_direction'] = "employee";

        $employeeJob = EmployeeJob::create($field);

        return response()->json([
            'employeeJob' => $employeeJob,
            'message' => 'employeeJob created'
        ], Response::HTTP_CREATED);
    }

    // update status
    // just update job from enterpise
    public function updateOffer(Request $request, $id, $job_id)
    {
        $employee = Employee::find($id);
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

        $employeeJob = EmployeeJob::where([
            ['job_id', $job_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($employeeJob->offer_direction == "employee") {
            return response()->json([
                'message' => 'You can not update job from employee'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $field = $request->validate([
                "status" => "required|string|in:pending,accepted,rejected",
            ]);

            $employeeJob->update($field);

            return response()->json([
                'employeeJob' => $employeeJob,
                'message' => 'employeeJob updated'
            ], Response::HTTP_OK);
        }
    }

    // delete job of employee
    public function destroyOffer($id, $job_id)
    {
        $employee = Employee::find($id);
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

        $employeeJob = EmployeeJob::where([
            ['job_id', $job_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeJob->delete();

        return response()->json([
            'message' => 'employeeJob deleted'
        ], Response::HTTP_OK);
    }

    // get job of employee
    public function showOffer($id, $job_id)
    {
        $employee = Employee::find($id);
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

        $employeeJob = EmployeeJob::where([
            ['job_id', $job_id],
            ['employee_id', $employee->user_id]
        ])->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $job = $employeeJob->job;

        $enterprise = Enterprise::find($job->enterprise_id);

        if ($employeeJob->status == "accepted") {
            return response()->json([
                'employeeJob' => $employeeJob,
                'enterprise' => $enterprise,
                'message' => 'employeeJob found'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'employeeJob' => $employeeJob,
                'enterprise_name' => $enterprise->name,
                'message' => 'employeeJob found'
            ], Response::HTTP_OK);
        }
    }

    // get all job of employee
    public function getOffers($id)
    {
        $employee = Employee::find($id);
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

        $employeeJobs = EmployeeJob::where('employee_id', $employee->user_id)->get();
        if (!$employeeJobs) {
            return response()->json([
                'message' => 'employeeJobs not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'employeeJobs' => $employeeJobs,
            'message' => 'employeeJobs found'
        ], Response::HTTP_OK);
    }
    // end employee job

}
