<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Enum;
use App\Enums\Role;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/users
    public function getAllUsers() {
        return User::all();
    }
    
    // POST /api/users
    public function createUser(Request $request) {
        // protected $fillable = [
        //     'email',
        //     'password',
        //     'role'
        // ];
        // validate request
        $validated = $request->validate([
            'email' => 'required',
            'password' =>'required|string|min:8',
            'role' => ['required', new Enum(Role::class)],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        // add to db
        $user = User::create($validated);
        // return success
        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // GET /api/users/{userId}
    public function getUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => "User #$userId fetched successfully",
            'data' => $user
        ]);
    }

    // PATCH /api/users/{userId}
    public function updateUser(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'email' => 'string',
            'address' => 'string',
            'phone_number' => 'string',
            'img_src' => 'string'
        ]);

        $user->update($validated);

        return response()->json([
            'message' => "User #$userId updated successfully",
            'data' => $user
        ]);
    }

    // DELETE /api/users/{userId}
    public function deleteUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => "User #$userId deleted successfully"
        ]);
    }
    
}
