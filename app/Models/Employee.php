<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'language',
        'certificates',
        'overview',
        'work_history',
        'education',
        'visibility',
        'user_id',
    ];

    // primary key is user_id
    protected $primaryKey = 'user_id';

    // employee belongs to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // employee has many employee_skills
    public function employeeSkills()
    {
        return $this->hasMany(EmployeeSkill::class, 'employee_id');
    }

    // employee has many employee_jobs
    public function employeeJobs()
    {
        return $this->hasMany(EmployeeJob::class, 'employee_id');
    }

    // employee has many report jobs
    public function reportJobs()
    {
        return $this->hasMany(ReportJob::class, 'employee_id');
    }

    // employee has many reports from enterprise
    public function reportEmployees()
    {
        return $this->hasMany(ReportEmployee::class, 'employee_id');
    }
}
