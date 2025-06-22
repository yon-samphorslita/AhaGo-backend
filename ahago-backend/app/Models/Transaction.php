<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'order_id',
        'payment',
        'amount'
    ];

    public function customer() {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function restaurant() {
        return $this->belongsTo(RestaurantProfile::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
