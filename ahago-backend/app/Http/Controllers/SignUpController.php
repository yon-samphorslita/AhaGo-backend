<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SignUpController extends Controller
{
    public function register(Request $request, $role)
{
    // Validate role
    $allowedRoles = ['admin', 'customer', 'driver', 'restaurant'];
    if (!in_array($role, $allowedRoles)) {
        return response()->json(['message' => 'Invalid role'], 400);
    }

    // Validate request input
    $validatedData = $request->validate([
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    // Create new user
    $user = new User();
    $user->firstName = $validatedData['firstName'];
    $user->lastName = $validatedData['lastName'];
    $user->name = $validatedData['firstName'] . ' ' . $validatedData['lastName'];
    $user->email = $validatedData['email'];
    $user->password = Hash::make($validatedData['password']);
    $user->role = $role;
    $user->save();

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
    ], 201);
}

    
}
