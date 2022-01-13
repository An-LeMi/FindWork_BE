<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportJob extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reason',
        'employee_id',
        'job_id'
    ];

    // report job belongs to employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // report job belongs to job
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
