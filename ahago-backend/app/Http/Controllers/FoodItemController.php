<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    // GET /api/foodItems
    public function getFoodItems()
    {
        return FoodItem::all();
    }

    // POST /api/foodItems
    public function createFoodItem(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'integer',
            'category_id'=> 'integer',
            'name' => 'string',
            'price' => ['nullable', 'numeric', 'between:0,99.99'],
            'description' => ['nullable', 'string'],
            'available' => 'boolean',
            'discount' => ['nullable', 'integer'],
            'img_url' => ['nullable', 'string'],
        ]);

        $foodItem = FoodItem::create($validated);

        return response()->json([
            'message' => 'FoodItem created successfully',
            'data' => $foodItem,
        ], 200);
    }

    // GET /api/foodItems/{foodItemId}
    public function getFoodItem($foodItemId)
    {
        $foodItem = FoodItem::find($foodItemId);

        if (!$foodItem) {
            return response()->json(['message' => 'FoodItem not found'], 404);
        }

        return $foodItem;
    }

    // GET /api/foodItems/topsellers
    public function getTopSellers() {
        return FoodItem::orderBy('sold', 'desc')->take(3)->get();
    }

    // GET /api/foodItems/rest/{restId}
    public function getFoodItemsByRestId($restId)
    {
        return FoodItem::where('restaurant_id', $restId)->get();
    }

    // PATCH /api/foodItems/{foodItemId}
    public function updateFoodItem(Request $request, $foodItemId)
    {
        $foodItem = FoodItem::find($foodItemId);

        if (!$foodItem) {
            return response()->json(['message' => 'FoodItem not found'], 404);
        }

        $validated = $request->validate([
            'category_id'=> 'integer',
            'name' => 'string',
            'price' => ['nullable', 'numeric', 'between:0,99.99'],
            'description' => 'string|nullable',
            'available' => 'boolean',
            'discount' => 'integer|nullable',
            'img_url' => 'string|nullable',
        ]);

        $foodItem->update($validated);

        return response()->json([
            'message' => "FoodItem #$foodItemId updated successfully",
            'data' => $foodItem
        ]);
    }

    // DELETE /api/foodItems/{foodItemId}
    public function deleteFoodItem($foodItemId)
    {
        $foodItem = FoodItem::find($foodItemId);

        if (!$foodItem) {
            return response()->json(['message' => 'FoodItem not found'], 404);
        }

        $foodItem->delete();

        return response()->json([
            'message' => "FoodItem #$foodItemId deleted successfully"
        ]);
    }
}
