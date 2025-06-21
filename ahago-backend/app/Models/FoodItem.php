<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'price',
        'description',
        'available',
        'discount',
        'img_url',
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
