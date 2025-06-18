<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /api/categories
    public function getCategories()
    {
        return Category::all();
    }

    // POST /api/categories
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'id' => 'integer',
            'restaurant_id' => 'required|integer',
            'name' => 'string',
            'description' => ['string', 'nullable']
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    // GET /api/categories/{categId}
    public function getCategory($categId)
    {
        $category = Category::find($categId);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json([
            'message' => "Category #$categId fetched successfully",
            'data' => $category
        ]);
    }

    // PATCH /api/categories/{categId}
    public function updateCategory(Request $request, $categId)
    {
        $category = Category::find($categId);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'restaurant_id' => 'required|integer',
            'name' => 'string',
            'description' => ['string', 'nullable']
        ]);

        $category->update($validated);

        return response()->json([
            'message' => "Category #$categId updated successfully",
            'data' => $category
        ]);
    }

    // DELETE /api/categories/{categId}
    public function deleteCategory($categId)
    {
        $category = Category::find($categId);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json([
            'message' => "Category #$categId deleted successfully"
        ]);
    }
}
