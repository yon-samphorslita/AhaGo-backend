<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // List users with admin profile
    public function index()
    {
        $roles = ['driver', 'customer', 'restaurant', 'admin'];

        $users = User::whereIn('role', $roles)
            ->with('adminProfile')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'address' => $user->address,
                    'phone_number' => $user->phone_number,
                    'img_src' => $user->img_src,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'admin_profile' => $user->adminProfile ? [
                        'firstname' => $user->adminProfile->firstname,
                        'lastname' => $user->adminProfile->lastname,
                        'address' => $user->adminProfile->address,
                        'city' => $user->adminProfile->city,
                        'phone_number' => $user->adminProfile->phone_number,
                        'img_src' => $user->adminProfile->img_src,
                    ] : null,
                ];
            });

        return response()->json($users);
    }

    // Show single user by ID
    public function show($id)
    {
        $user = User::with('adminProfile')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'address' => $user->address,
            'phone_number' => $user->phone_number,
            'img_src' => $user->img_src,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'admin_profile' => $user->adminProfile ? [
                'firstname' => $user->adminProfile->firstname,
                'lastname' => $user->adminProfile->lastname,
                'address' => $user->adminProfile->address,
                'city' => $user->adminProfile->city,
                'phone_number' => $user->adminProfile->phone_number,
                'img_src' => $user->adminProfile->img_src,
            ] : null,
        ]);
    }

    // Create new user and admin profile (if admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'role' => ['nullable', Rule::in(['admin', 'customer', 'driver', 'restaurant'])],
            'firstname' => 'required_if:role,admin|string|max:100',
            'lastname' => 'required_if:role,admin|string|max:100',
            'city' => 'nullable|string|max:255',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imgPath = null;
        if ($request->hasFile('img')) {
            // Store relative path only (e.g. images/abc123.jpg)
            $imgPath = $request->file('img')->store('images', 'public');
            $validated['img_src'] = $imgPath;
        }

        $user = null;
        DB::transaction(function () use ($validated, &$user, $imgPath) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'address' => $validated['address'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'img_src' => $imgPath, // save image path here for user table
                'role' => $validated['role'] ?? 'customer',
            ]);

            if ($user->role === 'admin') {
                $user->adminProfile()->create([
                    'firstname' => $validated['firstname'],
                    'lastname' => $validated['lastname'],
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'phone_number' => $validated['phone_number'] ?? null,
                    'img_src' => $imgPath, // save image path here for admin_profiles table
                ]);
            }
        });

        // Return user with relative path in img_src (no asset() here)
        return response()->json($user->load('adminProfile'), 201);
    }

    public function update(Request $request, $id)
{
    $user = User::with('adminProfile')->find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
        'password' => 'sometimes|string|min:6',
        'address' => 'nullable|string|max:255',
        'phone_number' => 'nullable|string|max:255',
        'role' => ['nullable', Rule::in(['admin', 'customer', 'driver', 'restaurant'])],
        'firstname' => 'sometimes|string|max:100',
        'lastname' => 'sometimes|string|max:100',
        'city' => 'nullable|string|max:255',
        'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $imgPath = null;
    if ($request->hasFile('img')) {
        $imgPath = $request->file('img')->store('images', 'public');
        $validated['img_src'] = $imgPath; // add to validated so it updates users table
    }

    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

    $user->update($validated);

    if (($validated['role'] ?? $user->role) === 'admin') {
        $profileData = [
            'firstname' => $validated['firstname'] ?? ($user->adminProfile->firstname ?? null),
            'lastname' => $validated['lastname'] ?? ($user->adminProfile->lastname ?? null),
            'address' => $validated['address'] ?? ($user->adminProfile->address ?? null),
            'city' => $validated['city'] ?? ($user->adminProfile->city ?? null),
            'phone_number' => $validated['phone_number'] ?? ($user->adminProfile->phone_number ?? null),
        ];
        if ($imgPath) {
            $profileData['img_src'] = $imgPath; // update admin profile image path
        }

        if ($user->adminProfile) {
            $user->adminProfile()->update($profileData);
        } else {
            $user->adminProfile()->create($profileData);
        }
    }

    return response()->json($user->load('adminProfile'));
}


    // Delete user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
