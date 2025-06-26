<?php

namespace App\Http\Controllers;

use App\Models\FoodItemReview;
use Illuminate\Http\Request;

class FoodItemReviewController extends Controller
{
    // GET /api/foodItem_reviews
    public function getReviews()
    {
        return FoodItemReview::all();
    }

    // POST /api/foodItem_reviews
    public function createReview(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'integer',
            'food_item_id' => 'integer',
            'comment' => 'required|string',
            'rating' => 'required|integer',
        ]);

        $review = FoodItemReview::create($validated);

        return response()->json([
            'message' => 'FoodItemReview created successfully',
            'data' => $review
        ], 201);
    }

    // GET /api/foodItem_reviews/{food_item_id}
    public function getReviewsByFoodItem($food_item_id)
    {
        $reviews = FoodItemReview::where('food_item_id', $food_item_id)->with('customer')->get();

        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No reviews found for this food item'], 404);
        }

        return response()->json($reviews);
    }

    // PATCH /api/foodItem_reviews/{reviewId}
    public function updateReview(Request $request, $reviewId)
    {
        $review = FoodItemReview::find($reviewId);

        if (!$review) {
            return response()->json(['message' => 'FoodItemReview not found'], 404);
        }

        $validated = $request->validate([
            'customer_id' => 'integer',
            'food_item_id' => 'integer',
            'comment' => 'required|string',
            'rating' => 'required|integer',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => "FoodItemReview #$reviewId updated successfully",
            'data' => $review
        ]);
    }

    // DELETE /api/foodItem_reviews/{reviewId}
    public function deleteReview($reviewId)
    {
        $review = FoodItemReview::find($reviewId);

        if (!$review) {
            return response()->json(['message' => 'FoodItemReview not found'], 404);
        }

        $review->delete();

        return response()->json([
            'message' => "FoodItemReview #$reviewId deleted successfully"
        ]);
    }
}
