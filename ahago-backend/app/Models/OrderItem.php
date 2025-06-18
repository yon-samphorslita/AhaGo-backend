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
}
