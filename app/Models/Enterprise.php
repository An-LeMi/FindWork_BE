<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'website_url',
        'overview',
        'user_id',
    ];

    // primary key is user_id
    protected $primaryKey = 'user_id';

    // enterprise belongs to user
    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // enterprise has many jobs
    public function jobs()
    {
        return $this->hasMany(Job::class, 'enterprise_id');
    }

    // enterprise has many report employee
    public function reportEmployees()
    {
        return $this->hasMany(ReportEmployee::class, 'enterprise_id');
    }
}
