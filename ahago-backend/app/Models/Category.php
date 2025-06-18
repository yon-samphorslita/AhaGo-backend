<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'description'
    ];

    public function restaurant()
    {
        return $this->belongsTo(RestaurantProfile::class);
    }

    public function foodItems()
    {
        return $this->hasMany(FoodItem::class);
    }
}
