<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    // List all banners
    public function index()
    {
        // Return all banners ordered by latest first
        return Banner::orderBy('created_at', 'desc')->get();
    }

    // Store new banner with image upload
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048',
        ]);

        // Store the image file in 'storage/app/public/banners'
        $path = $request->file('image')->store('banners', 'public');
        $validated['image_path'] = $path;

        $banner = Banner::create($validated);

        return response()->json($banner, 201);
    }

    // Show one banner
    public function show($id)
    {
        return Banner::findOrFail($id);
    }

    // Update banner with optional new image upload
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_path'] = $path;
        }

        $banner->update($validated);

        return response()->json($banner);
    }

    // Delete banner
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(null, 204);
    }
}
