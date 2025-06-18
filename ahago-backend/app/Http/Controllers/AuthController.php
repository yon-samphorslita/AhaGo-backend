<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required_if:role,restaurant|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,customer,restaurant,driver',
            'first_name' => 'required_if:role,driver,admin,customer|string',
            'last_name' => 'required_if:role,driver,admin,customer|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'address' => $request->address ?? null,
                'phone_number' => $request->phone_number ?? null,
                'img_src' => $request->img_src ?? null,
            ]);

            switch ($request->role) {
                case 'driver':
                    $user->driverProfile()->create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'id_card' => $request->id_card ?? null,
                        'vehicle_type' => $request->vehicle_type ?? null,
                        'vehicle_name' => $request->vehicle_name ?? null,
                        'vehicle_color' => $request->vehicle_color ?? null,
                        'license_plate' => $request->license_plate ?? null,
                    ]);
                    break;

                case 'customer':
                    $user->customerProfile()->create([
                        'gender' => $request->gender ?? null,
                        'city' => $request->city ?? null,
                        'latitude' => $request->latitude ?? null,
                        'longitude' => $request->longitude ?? null,
                    ]);
                    break;

                case 'restaurant':
                    $user->restaurantProfile()->create([
                        'name' => $request->restaurant_name ?? $request->name,
                        'working_hours' => $request->working_hours ?? null,
                        'description' => $request->description ?? null,
                        'latitude' => $request->latitude ?? null,
                        'longitude' => $request->longitude ?? null,
                    ]);
                    break;

                case 'admin':
                    $user->adminProfile()->create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'city' => $request->city ?? null,
                    ]);
                    break;
            }

            DB::commit();

            return response()->json([
                'message' => 'Registration successful',
                'user' => $request->role === 'admin' ? $user->load('adminProfile') :  $user->load($request->role . 'Profile')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request, $role)
    {
        $allowedRoles = ['admin', 'customer', 'driver', 'restaurant'];

        if (!in_array($role, $allowedRoles)) {
            return response()->json(['message' => 'Invalid role'], 400);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', $role)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
        ]);
    }
}
