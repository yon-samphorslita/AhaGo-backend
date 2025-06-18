<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'restaurant_id',
        'customer_id',
        'driver_id',
        'status',
        'total_amount',
        'payment_status',
        'remark',
        'order_type'
    ];
}
