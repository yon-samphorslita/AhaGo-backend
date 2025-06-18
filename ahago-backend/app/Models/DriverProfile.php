<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'id_card',
        'vehicle_type',
        'vehicle_name',
        'vehicle_color',
        'license_plate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
