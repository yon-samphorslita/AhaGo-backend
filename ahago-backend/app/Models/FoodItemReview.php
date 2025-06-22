<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItemReview extends Model
{
    protected $fillable = [
        'customer_id',
        'food_item_id',
        'rating',
        'comment'
    ];

    public function foodItem() 
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
    public function customer() 
    {
        return $this->belongsTo(CustomerProfile::class);
    }
}
