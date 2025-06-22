<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // List all reviews
    public function index()
    {
        // Add 'image_url' accessor for full image path
        $reviews = Review::all()->map(function ($review) {
            $review->image_url = $review->image
                ? url('storage/' . $review->image)
                : null;
            return $review;
        });

        return response()->json($reviews);
    }

    // Store a new review with image upload
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'author'      => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'menu'        => 'required|string|max:100',
            'rating'      => 'required|integer|min:1|max:5',
            'image'       => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('image')) {
            // Store image in 'public/reviews' folder
            $path = $request->file('image')->store('reviews', 'public');
            $validatedData['image'] = $path; // Save path relative to storage/app/public
        }

        $review = Review::create($validatedData);

        // Append full image URL to response
        $review->image_url = $review->image
            ? url('storage/' . $review->image)
            : null;

        return response()->json($review, 201);
    }

    // Show one review
    public function show($id)
    {
        $review = Review::findOrFail($id);
        $review->image_url = $review->image
            ? url('storage/' . $review->image)
            : null;

        return response()->json($review);
    }

    // Update a review (optional: update image too)
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validatedData = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'author'      => 'sometimes|required|string|max:255',
            'category'    => 'sometimes|required|string|max:100',
            'menu'        => 'sometimes|required|string|max:100',
            'rating'      => 'sometimes|required|integer|min:1|max:5',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
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

    // Delete a review
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->image && \Storage::disk('public')->exists($review->image)) {
            \Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}
