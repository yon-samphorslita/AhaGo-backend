<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone_number',
        'address',
        'password',
        'id_card',
        'vehicle_type',
        'vehicle_name',
        'vehicle_color',
        'license_plate',
    ];

    protected $hidden = [
        'password',
    ];
}
