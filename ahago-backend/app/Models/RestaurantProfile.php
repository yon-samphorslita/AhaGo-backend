<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantProfile extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'working_hours',
        'description'
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
