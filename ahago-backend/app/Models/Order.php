<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'customer_id',
        'driver_id',
        'status',
        'total_amount',
        'payment_status',
        'remark',
        'order_type',
    ];


    public function foodItems()
    {
        return $this->belongsToMany(FoodItem::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantProfile::class, 'restaurant_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id', 'id');
    }

    public function driver() {
        return $this->belongsTo(DriverProfile::class, 'driver_id', 'id');
    }
}
