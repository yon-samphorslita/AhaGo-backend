<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantProfile;
class RestaurantProfileController extends Controller
{
    public function getRestaurants(){
    $restaurants = RestaurantProfile::with('user', 'categories.foodItems') // load related user
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

    public function updateRestaurant(Request $request, $id)
{
    try {
        $restaurant = RestaurantProfile::with('categories.foodItems')->findOrFail($id);

        $input = $request->all();

        // If input is an array with a single restaurant object, extract it
        if (is_array($input) && count($input) === 1 && isset($input[0]) && is_array($input[0])) {
            $input = $input[0];
        }

        $validated = \Validator::make($input, [
            'name' => 'sometimes|required|string',
            'working_hours' => 'nullable|string',
            'description' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',

            'categories' => 'nullable|array',
            'categories.*.id' => 'nullable|integer|exists:categories,id',
            'categories.*.name' => 'required|string',
            'categories.*.description' => 'nullable|string',

            'categories.*.food_items' => 'nullable|array',
            'categories.*.food_items.*.id' => 'nullable|integer|exists:food_items,id',
            'categories.*.food_items.*.name' => 'required|string',
            'categories.*.food_items.*.price' => 'required|numeric',
            'categories.*.food_items.*.img_url' => 'nullable|string',
        ])->validate();

        // Update restaurant fields
        $restaurant->update([
            'name' => $validated['name'] ?? $restaurant->name,
            'working_hours' => $validated['working_hours'] ?? $restaurant->working_hours,
            'description' => $validated['description'] ?? $restaurant->description,
            'latitude' => $validated['latitude'] ?? $restaurant->latitude,
            'longitude' => $validated['longitude'] ?? $restaurant->longitude,
        ]);

        if (array_key_exists('categories', $validated) && $validated['categories'] !== null) {
            // Ensure categories is always an array
            $categoriesData = is_array($validated['categories']) ? $validated['categories'] : [$validated['categories']];
            $categoryIdsInRequest = [];

            foreach ($categoriesData as $catData) {
                $category = null;
                if (!empty($catData['id'])) {
                    $category = $restaurant->categories()->where('id', $catData['id'])->first();
                    if ($category) {
                        $category->update([
                            'name' => $catData['name'],
                            'description' => $catData['description'] ?? null,
                        ]);
                    }
                }

                // Create new category if needed
                if (!$category) {
                    $category = $restaurant->categories()->create([
                        'name' => $catData['name'],
                        'description' => $catData['description'] ?? null,
                    ]);
                }

                $categoryIdsInRequest[] = $category->id;

                // Sync food items inside category
                if (array_key_exists('food_items', $catData) && $catData['food_items'] !== null) {
                    // Ensure food_items is always an array
                    $foodItemsData = is_array($catData['food_items']) ? $catData['food_items'] : [$catData['food_items']];
                    $foodItemIdsInRequest = [];

                    foreach ($foodItemsData as $item) {
                        if (!empty($item['id'])) {
                            $foodItem = $category->foodItems()->where('id', $item['id'])->first();
                            if ($foodItem) {
                                $foodItem->update([
                                    'name' => $item['name'],
                                    'price' => $item['price'],
                                    'img_url' => $item['img_url'] ?? null,
                                ]);
                                $foodItemIdsInRequest[] = $foodItem->id;
                                continue;
                            }
                        }
                        // Create new food item
                        $newFoodItem = $category->foodItems()->create([
                            'name' => $item['name'],
                            'price' => $item['price'],
                            'img_url' => $item['img_url'] ?? null,
                            'restaurant_id' => $restaurant->id, // Ensure restaurant_id is set
                        ]);
                        $foodItemIdsInRequest[] = $newFoodItem->id;
                    }

                    // Delete food items not included in request
                    $category->foodItems()->whereNotIn('id', $foodItemIdsInRequest)->delete();
                } else {
                    // If no food_items sent, keep existing food items untouched
                }
            }

            // Delete categories not included in request
            $restaurant->categories()->whereNotIn('id', $categoryIdsInRequest)->delete();
        }

        return response()->json($restaurant->load('user', 'categories.foodItems'));

    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}
}