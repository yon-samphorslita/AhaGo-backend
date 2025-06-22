<?php

namespace App\Http\Controllers;

use App\Models\RestaurantReview;
use Illuminate\Http\Request;

class RestaurantReviewController extends Controller
{
    // GET /api/restaurant_reviews
    public function getReviews()
    {
        return RestaurantReview::all(); 
    }

    // POST /api/restaurant_reviews
    public function createReview(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'integer',
            'restaurant_id' => 'integer',
            'comment' => 'required|string',
            'rating' => 'required|integer',
        ]);

        $review = RestaurantReview::create($validated);

        return response()->json([
            'message' => 'RestReview created successfully',
            'data' => $review
        ], 201);
    }

    // GET /api/restaurant_reviews/{restId}
    public function getReviewsByRestaurant($restId)
    {
        $reviews = RestaurantReview::find($restId)->with('customer')->get();

        if (!$reviews) {
            return response()->json(['message' => 'RestReview not found'], 404);
        }

        return $reviews;
    }

    // PATCH /api/restaurant_reviews/{adminId}
    public function updateReview(Request $request, $reviewId)
    {
        $review = RestaurantReview::find($reviewId);

        if (!$review) {
            return response()->json(['message' => 'RestReview not found'], 404);
        }

        $validated = $request->validate([
            'customer_id' => 'integer',
            'restaurant_id' => 'integer',
            'comment' => 'required|string',
            'rating' => 'required|integer',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => "review #$reviewId updated successfully",
            'data' => $review
        ]);
    }

    // DELETE /api/restaurant_reviews/{reviewId}
    public function deleteReview($reviewId)
    {
        $review = RestaurantReview::find($reviewId);

        if (!$review) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $review->delete();

        return response()->json([
            'message' => "RestReview #$reviewId deleted successfully"
        ]);
    }
}
