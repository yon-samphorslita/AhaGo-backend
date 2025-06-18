<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RestaurantProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'working_hours',
        'description',
        'latitude',
        'longitude',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'restaurant_id');
    }

        public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
