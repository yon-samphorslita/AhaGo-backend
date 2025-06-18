<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name'
    ];
}
