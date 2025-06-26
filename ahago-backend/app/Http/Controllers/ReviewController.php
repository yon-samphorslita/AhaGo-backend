<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class FoodItemReviewController extends Controller
{
    // Get all reviews
    public function getReviews()
    {
        $reviews = Review::with('customer')->get()->map(function ($review) {
            $review->image_url = $review->image
                ? url('storage/' . $review->image)
                : null;
            return $review;
        });

        return response()->json(['data' => $reviews]);
    }

    // Create a new review
    public function createReview(Request $request)
    {
        $validatedData = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'author'       => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'menu'         => 'required|string|max:100',
            'rating'       => 'required|integer|min:1|max:5',
            'food_item_id' => 'required|integer|exists:food_items,id',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('reviews', 'public');
            $validatedData['image'] = $path;
        }

        $review = Review::create($validatedData);

        $review->image_url = $review->image
            ? url('storage/' . $review->image)
            : null;

        return response()->json($review, 201);
    }

    // Get reviews by food item ID
    public function getReviewsByFoodItem($food_item_id)
    {
        $reviews = Review::where('food_item_id', $food_item_id)
            ->with('customer')  // eager load related customer info, adjust relation name as needed
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                $review->image_url = $review->image
                    ? url('storage/' . $review->image)
                    : null;
                return $review;
            });

        return response()->json(['data' => $reviews]);
    }

    // Update a review by ID
    public function updateReview(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        $validatedData = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'description'  => 'sometimes|required|string',
            'author'       => 'sometimes|required|string|max:255',
            'category'     => 'sometimes|required|string|max:100',
            'menu'         => 'sometimes|required|string|max:100',
            'rating'       => 'sometimes|required|integer|min:1|max:5',
            'image'        => 'nullable|image|max:2048',
            'food_item_id' => 'sometimes|required|integer|exists:food_items,id',
        ]);

        if ($request->hasFile('image')) {
            if ($review->image && \Storage::disk('public')->exists($review->image)) {
                \Storage::disk('public')->delete($review->image);
            }
            $path = $request->file('image')->store('reviews', 'public');
            $validatedData['image'] = $path;
        }

        $review->update($validatedData);

        $review->image_url = $review->image
            ? url('storage/' . $review->image)
            : null;

        return response()->json($review);
    }

    // Delete a review by ID
    public function deleteReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if ($review->image && \Storage::disk('public')->exists($review->image)) {
            \Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}
