<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodItemController extends Controller
{
    // GET /api/foodItems
    public function getFoodItems()
    {
        return response()->json([
            'data' => FoodItem::all()
        ]);
    }

    // GET /api/foodItems/count
    public function getFoodItemsCount()
    {
        $count = FoodItem::count();
        return response()->json(['count' => $count]);
    }

    // GET /api/foodItems/stock
    public function getStockLevel()
    {
        $counts = DB::table('food_items')
            ->select('available', DB::raw('count(*) as total'))
            ->groupBy('available')
            ->get();

        return response()->json([
            'data' => $counts
        ]);
    }

    // POST /api/foodItems
    public function createFoodItem(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurant_profiles,id',
            'category_id'=> 'nullable|integer|exists:categories,id',
            'name' => 'required|string',
            'price' => ['required', 'numeric', 'between:0,99.99'],
            'description' => ['nullable', 'string'],
            'available' => 'boolean',
            'discount' => ['nullable', 'integer'],
            'rating' => ['nullable', 'integer'],
            'sold' => ['nullable', 'integer'],
            'favourite' => 'boolean',
            'img_file' => ['nullable', 'image', 'max:2048'], // Image upload
        ]);

        // Check if a food item with this restaurant_id already exists
        $existingFoodItem = FoodItem::where('restaurant_id', $validated['restaurant_id'])->first();
        if ($existingFoodItem) {
            return response()->json([
                'message' => 'Food item for this restaurant already exists. Use update instead.'
            ], 409); // Conflict status
        }

        // Handle image upload
        if ($request->hasFile('img_file')) {
            $file = $request->file('img_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foodimages', $filename);
            $validated['img_url'] = '/storage/foodimages/' . $filename;
        } else {
            $validated['img_url'] = null;
        }

        $foodItem = FoodItem::create($validated);

        return response()->json([
            'message' => 'FoodItem created successfully',
            'data' => $foodItem,
        ], 201);
    }

    // GET /api/foodItems/{foodItemId}
    public function getFoodItem($foodItemId)
    {
        $foodItem = FoodItem::find($foodItemId);

        if (!$foodItem) {
            return response()->json(['message' => 'FoodItem not found'], 404);
        }

        return response()->json([
            'data' => $foodItem
        ]);
    }

    // GET /api/foodItems/top
    public function getTopSellers()
    {
        return response()->json([
            'data' => FoodItem::orderBy('sold', 'desc')->take(10)->get()
        ]);
    }

    // GET /api/foodItems/rest/{restId}
    public function getFoodItemsByRestId($restId)
    {
        return response()->json([
            'data' => FoodItem::where('restaurant_id', $restId)->get()
        ]);
    }

    // PATCH /api/foodItems/{foodItemId}
    public function updateFoodItem(Request $request, $foodItemId)
    {
        $foodItem = FoodItem::find($foodItemId);

        if (!$foodItem) {
            return response()->json(['message' => 'FoodItem not found'], 404);
        }

        $validated = $request->validate([
            'category_id'=> 'nullable|integer|exists:categories,id',
            'name' => 'string',
            'price' => ['nullable', 'numeric', 'between:0,99.99'],
            'description' => 'nullable|string',
            'available' => 'boolean',
            'discount' => 'nullable|integer',
            'rating' => 'nullable|integer',
            'sold' => 'nullable|integer',
            'favourite' => 'boolean',
            'img_file' => ['nullable', 'image', 'max:2048'], // Image upload
        ]);

        // Handle image upload
        if ($request->hasFile('img_file')) {
            $file = $request->file('img_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foodimages', $filename);
            $validated['img_url'] = '/storage/foodimages/' . $filename;
        }

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

    // GET /api/restaurants/{restaurantId}/foodItems
    public function getFoodItemsByRestaurant($restaurantId)
    {
        return response()->json([
            'data' => FoodItem::where('restaurant_id', $restaurantId)->get()
        ]);
    }

    // GET /api/foodItems/count (alias)
    public function getCount()
    {
        $count = FoodItem::count();
        return response()->json(['count' => $count]);
    }

    // GET /api/foodItems/stock (alias)
    public function getStock()
    {
        $inStockCount = FoodItem::where('available', true)->count();
        $outOfStockCount = FoodItem::where('available', false)->count();

        return response()->json([
            ['type' => 'inStock', 'total' => $inStockCount],
            ['type' => 'outStock', 'total' => $outOfStockCount],
        ]);
    }
}
