<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Store the file in storage/app/uploads
        $path = $request->file('document')->store('driver-sections');

        // Return a JSON response
        return response()->json(['path' => $path], 200);
    }
}
