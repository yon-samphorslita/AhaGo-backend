<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'address',
        'password',
        'city',
        'phone_number',
    ];

    protected $hidden = [
        'password',
    ];

    // If you want to hash password in model (optional)
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Relationship back to User if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
