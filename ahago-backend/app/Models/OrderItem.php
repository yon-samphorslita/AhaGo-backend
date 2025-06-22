<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'food_item_id',
        'order_id',
        'quantity',
        'price'
    ];

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id', 'id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);

//     public function foodItems() {
//         return $this->belongsTo(FoodItem::class,'food_item_id');

    }
}
