<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Http\Request;
class DriverProfileController extends Controller
{
    // GET /api/drivers
    public function getDrivers() {
        $drivers = DriverProfile::with('user') // load related user
            ->whereHas('user', function ($query) {
                $query->where('role', 'driver');
            })
            ->get();

        return response()->json($drivers); 
    }

    // POST /api/drivers
    public function createDriver(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'id_card'=> 'nullable|string',
            'vehicle_type'=> 'nullable|string',
            'vehicle_name'=> 'nullable|string',
            'vehicle_color'=> 'nullable|string',
            'license_plate'=> 'nullable|string',
        ]);

        $driver = DriverProfile::create($validated);

        return response()->json([
            'message' => 'DriverProfile created successfully',
            'data' => $driver
        ], 201);
    }

    // GET /api/drivers/{driverId}
    public function getDriver($driverId)
    {
        $driver = DriverProfile::find($driverId);

        if (!$driver) {
            return response()->json(['message' => 'driver not found'], 404);
        }

        return response()->json([
            'message' => "DriverProfile #$driverId fetched successfully",
            'data' => $driver
        ]);
    }

    // PATCH /api/drivers/{driverId}
    public function updateDriver(Request $request, $driverId)
    {
        $driver = DriverProfile::find($driverId);

        if (!$driver) {
            return response()->json(['message' => 'DriverProfile not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'string',
            'last_name' => 'string'
        ]);

        $driver->update($validated);

        return response()->json([
            'message' => "DriverProfile #$driverId updated successfully",
            'data' => $driver
        ]);
    }

    // DELETE /api/drivers/{driverId}
    public function deleteDriver($driverId)
    {
        $driver = DriverProfile::find($driverId);

        if (!$driver) {
            return response()->json(['message' => 'DriverProfile not found'], 404);
        }

        $driver->delete();

        return response()->json([
            'message' => "DriverProfile #$driverId deleted successfully"
        ]);
    }

    public function updateDriverProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate user fields
        $validatedUser = $request->validate([
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'img_src' => 'nullable|string',
        ]);

        // Validate driver profile fields
        $validatedProfile = $request->validate([
            'driver_profile.first_name' => 'nullable|string',
            'driver_profile.last_name' => 'nullable|string',
            'driver_profile.id_card' => 'nullable|string',
            'driver_profile.vehicle_type' => 'nullable|string',
            'driver_profile.vehicle_name' => 'nullable|string',
            'driver_profile.vehicle_color' => 'nullable|string',
            'driver_profile.license_plate' => 'nullable|string',
        ]);

        // Update User model fields
        $user->update($validatedUser);

        // Update DriverProfile model fields (create if missing)
        if ($user->driverProfile) {
            $user->driverProfile->update($request->input('driver_profile'));
        } else {
            $user->driverProfile()->create($request->input('driver_profile'));
        }

        return response()->json(['message' => 'Driver profile updated successfully']);
    }
}
