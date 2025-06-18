<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantProfile;
class RestaurantProfileController extends Controller
{
    public function getRestaurants(){
    $restaurants = RestaurantProfile::with('user') // load related user
            ->whereHas('user', function ($query) {
                $query->where('role', 'restaurant');
            })
            ->get();

        return response()->json($restaurants);
    }

    public function createRestaurant(Request $request){
        $validated = $request->validate([
            'user_id' => 'required',
            'name' => 'required|string',
            'working_hours'=> 'nullable|string',
            'description'=> 'nullable|string',
            'latitude'=> 'nullable|string',
            'longitude'=> 'nullable|string',
        ]);

        $restaurant = RestaurantProfile::create($validated);

        return response()->json($restaurant, 201);
    }
}
