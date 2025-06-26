<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
    
        'title',
        'description',
        'author',
        'category',
        'menu',
        'image',
        'rating',
        'food_item_id'
    ];
    public function foodItem()
{
    return $this->belongsTo(FoodItem::class, 'food_item_id');
}
public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id');
}


}
