<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        // on created only
        'email',
        'password',
        'role',

        // on updated
        'address',
        'phone_number',
        'img_src'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function restaurantProfile()
    {
        return $this->hasOne(RestaurantProfile::class);
    }

    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class);
    }
}
