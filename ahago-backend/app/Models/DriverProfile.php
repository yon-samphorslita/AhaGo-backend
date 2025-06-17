<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DriverProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'id_card',
        'vehicle_type',
        'vehicle_name',
        'vehicle_color',
        'license_plate'
    ];
}
