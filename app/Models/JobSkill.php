<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSkill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'skill_id',
        'level',
    ];

    // job_skill belongs to job
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    // job_skill belongs to skill
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
