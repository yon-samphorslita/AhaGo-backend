<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterAdminController extends Controller
{
    public function register(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed', // password_confirmation needed
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create new admin
        $admin = Admin::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json(['message' => 'Admin registered successfully'], 201);
    }
}
