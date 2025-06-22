<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantReview extends Model
{
    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'comment',
        'rating'
    ];

    public function customer() {
        return $this->belongsTo(CustomerProfile::class);
    }

}
