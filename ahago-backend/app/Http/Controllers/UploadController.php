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
    public function uploadDriverPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        $path = Storage::disk('minio')->put("profile_photos/{$user->id}", $request->file('photo'), 'public');
        $url = Storage::disk('minio')->url($path);

        // Optionally save to DB
        $user->img_src = $url;
        $user->save();

        return response()->json(['img_src' => $url], 200);
    }
}
