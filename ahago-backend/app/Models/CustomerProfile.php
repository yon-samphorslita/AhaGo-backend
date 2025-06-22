<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Order;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $with = ['user'];

    protected $fillable = [
        'user_id',
        'firstname',   // added
        'lastname',    // added
        'gender',
        'city',
        'latitude',
        'longitude',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function restaurantReview(){
        return $this->belongsToMany(RestaurantReview::class);
    }
}
