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

    public function restaurant()
    {
        return $this->belongsTo(RestaurantProfile::class, 'restaurant_id', 'id');
    }

    // public function restaurantProfile()
    // {
    // return $this->hasOneThrough(
    //         User::class,
    //         RestaurantProfile::class,
    //         'id',         // restaurant_profiles.id (matches orders.restaurant_id)
    //         'id',         // users.id (matches restaurant_profiles.user_id)
    //         'restaurant_id', // orders.restaurant_id
    //         'user_id'        // restaurant_profiles.user_id
    //     )->where('role', 'restaurant');
    // }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->where('role', 'customer');
    }

    public function customerProfile()
    {
    // return $this->hasOneThrough(
    //         User::class,
    //         CustomerProfile::class,
    //         'id',         // restaurant_profiles.id (matches orders.restaurant_id)
    //         'id',         // users.id (matches restaurant_profiles.user_id)
    //         'customer_id', // orders.customer_id
    //         'user_id'        // customer_profiles.user_id
    //     )->where('role', 'customer');
    return $this->hasOne(CustomerProfile::class, 'user_id', 'customer_id');
    }

}
