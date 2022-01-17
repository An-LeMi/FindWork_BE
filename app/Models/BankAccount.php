<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'card_number',
        'password',
        'card_holder_name',
        'card_alias',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'card_number',
        'user_id',
        'created_at',
        'updated_at',
    ];
    // bank_account belongs to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
