<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'salary',
        'location',
        'enterprise_id',
    ];

    // job belongs to enterprise
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    // job has many job_skills
    public function jobSkills()
    {
        return $this->hasMany(JobSkill::class);
    }

    // job has many employee_jobs
    public function employeeJobs()
    {
        return $this->hasMany(EmployeeJob::class);
    }
}
