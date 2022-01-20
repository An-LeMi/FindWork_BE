<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Job;
use App\Models\Skill;
use App\Models\JobSkill;
use App\Models\EmployeeJob;
use App\Models\EmployeeSkill;
use App\Models\Enterprise;
use App\Models\ReportEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function GuzzleHttp\Promise\each;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $jobs = Job::all();
        return response()->json([
            'jobs' => $jobs,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            "title" => "required|string",
        ]);

        // check user_id is not enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise') {
            return response()->json([
                'message' => 'user_id is not enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add enterprise_id to field
        $request['enterprise_id'] = $user->id;

        $job = Job::create($request->all());

        return response()->json([
            'job' => $job,
            'message' => 'job created'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $job = Job::find($id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkills = $job->jobSkills;
        $skills = [];
        foreach ($jobSkills as $jobSkill) {
            $skills[] = $jobSkill->skill;
        }

        return response()->json([
            'job' => $job
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $job = Job::find($id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $request->validate([
            "title" => "required|string",
        ]);

        // check this job belongs to this enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise' | $job->enterprise_id != $user->id) {
            return response()->json([
                'message' => 'this job does not belong to this enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        $job->update($request->all());
        return response()->json([
            'job' => $job,
            'message' => 'Job updated'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $job = Job::find($id);
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

        $job->delete();
        EmployeeJob::where('job_id', $id)->delete();
        return response()->json([
            'message' => 'Job and employee job deleted'
        ], Response::HTTP_OK);
    }

    // search job
    public function searchJobTitle($title)
    {
        $job = Job::where('title', 'LIKE', '%' . $title . '%')->get();
        if (count($job)) {
            return response()->json([
                'job' => $job,
                'message' => 'Successful search'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * skill function
     */
    // add skill to job
    public function storeSkill(Request $request, $id)
    {
        //
        $job = Job::find($id);
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

        $request->validate([
            "skill_id" => "required|integer",
            "level" => "required|integer|between:1,5",
        ]);

        $skill = Skill::find($request->skill_id);
        if (!$skill) {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkill = JobSkill::where('job_id', $job->id)->where('skill_id', $skill->id)->first();
        if ($jobSkill) {
            return response()->json([
                'message' => 'Skill already added to this job'
            ], Response::HTTP_BAD_REQUEST);
        }


        $jobSkill = JobSkill::create([
            'job_id' => $job->id,
            'skill_id' => $skill->id,
            'level' => $request->level,
        ]);

        return response()->json([
            'jobSkill' => $jobSkill,
            'message' => 'Skill added to this job'
        ], Response::HTTP_CREATED);
    }

    // remove skill from job
    public function destroySkill($id, $skill_id)
    {
        //
        $job = Job::find($id);
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

        $skill = Skill::find($skill_id);
        if (!$skill) {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkill = JobSkill::where('job_id', $job->id)->where('skill_id', $skill->id)->first();
        if (!$jobSkill) {
            return response()->json([
                'message' => 'Skill not found in this job'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkill->delete();

        return response()->json([
            'message' => 'Skill removed from this job'
        ], Response::HTTP_OK);
    }

    // update skill of job
    public function updateSkill(Request $request, $id, $skill_id)
    {
        //
        $job = Job::find($id);
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

        $skill = Skill::find($skill_id);
        if (!$skill) {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkill = JobSkill::where('job_id', $job->id)->where('skill_id', $skill->id)->first();
        if (!$jobSkill) {
            return response()->json([
                'message' => 'Skill not found in this job'
            ], Response::HTTP_NOT_FOUND);
        }

        // validate request
        $request->validate([
            "level" => "required|integer|between:1,5",
        ]);

        $jobSkill->update($request->all());

        return response()->json([
            'jobSkill' => $jobSkill,
            'message' => 'Skill updated in this job'
        ], Response::HTTP_OK);
    }

    // get all skills of job
    public function getSkills($id)
    {
        //
        $job = Job::find($id);
        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkills = JobSkill::where('job_id', $job->id)->get();
        if (!$jobSkills) {
            return response()->json([
                'message' => 'No skills found in this job'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobSkills = $jobSkills->map(function ($jobSkill) {
            return [
                'id' => $jobSkill->id,
                'skill' => $jobSkill->skill,
                'level' => $jobSkill->level,
            ];
        });

        return response()->json([
            'jobSkills' => $jobSkills,
            'message' => 'Job skills'
        ], Response::HTTP_OK);
    }

    /**
     * offer function
     */
    // add employee to job
    public function storeOffer(Request $request, $id)
    {
        //
        $job = Job::find($id);
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

        $request->validate([
            "employee_id" => "required|integer|exists:employees,user_id",
        ]);

        $employeeJob = EmployeeJob::where('job_id', $job->id)
            ->where('employee_id', $request->employee_id)
            ->first();
        if ($employeeJob) {
            return response()->json([
                'message' => 'Employee already added to this job'
            ], Response::HTTP_BAD_REQUEST);
        }

        $employeeJob = EmployeeJob::create([
            'job_id' => $job->id,
            'employee_id' => $request->employee_id,
            'offer_direction' => "enterprise",
        ]);

        return response()->json([
            'employeeJob' => $employeeJob,
            'message' => 'Employee added to this job'
        ], Response::HTTP_CREATED);
    }

    // remove employee from job
    public function destroyOffer($id, $employee_id)
    {
        //
        $job = Job::find($id);
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

        $employeeJob = EmployeeJob::where('job_id', $job->id)->where('employee_id', $employee_id)->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Employee not found in this job'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeJob->delete();

        return response()->json([
            'message' => 'Employee removed from this job'
        ], Response::HTTP_OK);
    }

    // update status of employee
    public function updateOffer(Request $request, $id, $employee_id)
    {
        //
        $job = Job::find($id);
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

        $employeeJob = EmployeeJob::where('job_id', $job->id)->where('employee_id', $employee_id)->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Employee not found in this job'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($employeeJob->offer_direction == 'enterprise') {
            return response()->json([
                'message' => 'you can not update status of offer'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            // validate request
            $request->validate([
                "status" => "required|string|in:pending,accepted,rejected",
            ]);

            $employeeJob->update($request->all());

            return response()->json([
                'employeeJob' => $employeeJob,
                'message' => 'Employee status updated'
            ], Response::HTTP_OK);
        }
    }

    // show offer of job
    public function showOffer($id, $employee_id)
    {
        //
        $job = Job::find($id);
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

        $employeeJob = EmployeeJob::where('job_id', $job->id)->where('employee_id', $employee_id)->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'Employee not found in this job'
            ], Response::HTTP_NOT_FOUND);
        }

        $employee = $employeeJob->employee;
        if ($employeeJob->status != "accepted") {
            $employee->phone = null;
            $employee->email = null;
            $employee->address = null;
        }
        $employeeSkills = $employee->employeeSkills;
        $employeeSkills->each(function ($employeeSkill) {
            $employeeSkill->skill;
        });

        return response()->json([
            'employeeJob' => $employeeJob,
            'message' => 'Employee offer'
        ], Response::HTTP_OK);
    }

    // show all offers of job order by created time and number of similar skill
    public function getOffers($id)
    {
        //
        $job = Job::find($id);
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

        $skills = JobSkill::where('job_id', $job->id)->get()->pluck('skill_id')->toArray();
        $employeeJobs = EmployeeJob::where('job_id', $job->id)->orderBy("created_at", "desc")->get();

        if (!$employeeJobs) {
            return response()->json([
                'message' => 'Employee job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeJobs->each(function ($employeeJob) {
            $employee = $employeeJob->employee;
            if ($employeeJob->status != "accepted") {
                $employee->phone = null;
                $employee->email = null;
                $employee->address = null;
            }
            $employeeSkills = $employee->employeeSkills;
            $employeeSkills->each(function ($employeeSkill) {
                $employeeSkill->skill;
            });
        });

        $employeeJobs = $employeeJobs->sortByDesc(function ($employeeJob) use ($skills) {
            $employeeSkillIDs = EmployeeSkill::where('employee_id', $employeeJob->employee_id)->get()->pluck('skill_id')->toArray();
            // $employeeJob->emoloyeeskill = $employeeSkillIDs;
            $similarSkills = array_intersect($employeeSkillIDs, $skills);
            // $employeeJob->similar = count($similarSkills);
            return count($similarSkills);
        });

        return response()->json([
            'employeeJobs' => $employeeJobs,
            'message' => 'Employee offers'
        ], Response::HTTP_OK);
    }

    // update rating employee
    public function updateRating(Request $request, $job_id, $employee_id)
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

        // validate rate
        $request->validate([
            "rate" => "required|integer|between:0,5",
        ]);

        // check employee id
        $employee_user = User::find($employee_id);
        if (!$employee_user || $employee_user->role != 'employee') {
            return response()->json([
                'message' => 'employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $employeeJob = EmployeeJob::where('job_id', $job_id)->where('employee_id', $employee_id)->first();
        if (!$employeeJob) {
            return response()->json([
                'message' => 'employee job not found'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($employeeJob->status != "accepted") {
            return response()->json([
                'message' => 'Cannot vote'
            ], Response::HTTP_FORBIDDEN);
        } else {
            $rating = $employee_user->rating;
            $num_rates = $employee_user->number_of_rate;
            $employee_user->rating = ($rating + $request->rate) / ($num_rates + 1);
            $employee_user->number_of_rate = $num_rates + 1;
            $employee_user->update();
            return response()->json([
                'message' => 'Rating success'
            ], Response::HTTP_OK);
        }
    }

}
