<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'food_item_id',
        'order_id',
        'quantity',
        'price',
    ];

    // If you're using created_at and updated_at columns (default)
    public $timestamps = true;

    /**
     * Each order item belongs to a food item.
     */
    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class);
    }

    /**
     * Each order item also belongs to an order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
