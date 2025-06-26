<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
     public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Store the file in storage/app/uploads
        $path = $request->file('document')->store('restaurant', 'minio');

        // Return a JSON response
        return response()->json(['path' => $path], 201);
    }
    // Upload photo for any user based on their role
    public function uploadPhoto(Request $request)
    {
        // Validate the photo
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Determine the folder based on the user's role
        $role = $user->role;  // Assuming you have a 'role' field in your users table

        // Define the path based on the role
        if ($role == 'driver') {
            $folder = 'profile_photos/drivers';
        } elseif ($role == 'customer') {
            $folder = 'profile_photos/customers';
        } else {
            return response()->json(['error' => 'Invalid user role'], 400);
        }

        // Store the photo in the corresponding folder
        $path = Storage::disk('minio')->put("{$folder}/{$user->id}", $request->file('photo'), 'public');
        $url = Storage::disk('minio')->url($path);

        // Optionally save the URL to the database
        $user->img_src = $url;
        $user->save();

        return response()->json(['img_src' => $url], 200);
    }
}
