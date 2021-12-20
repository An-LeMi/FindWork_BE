<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJob extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'job_id',
        'status',
        'offer_direction',
    ];

    // employee_job belongs to employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // employee_job belongs to job
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
