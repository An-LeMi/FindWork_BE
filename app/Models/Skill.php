<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
    ];

    // skill belongs to category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // skill has many job_skills
    public function jobSkills()
    {
        return $this->hasMany(JobSkill::class);
    }

    // skill has many employee_skills
    public function employeeSkills()
    {
        return $this->hasMany(EmployeeSkill::class);
    }
}
