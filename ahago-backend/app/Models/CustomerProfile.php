<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Order;
class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
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
}
