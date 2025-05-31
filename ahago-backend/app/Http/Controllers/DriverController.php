<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    // GET /api/drivers
    public function getDrivers()
    {
        $drivers = Driver::all();

        return response()->json([
            'message' => 'Driver list fetched successfully',
            'data' => $drivers
        ]);
    }

    // POST /api/drivers
    public function createDriver(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:drivers,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'id_card' => 'nullable|string|max:10',
            'vehicle_type' => 'nullable|string',
            'vehicle_name' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'license_plate' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $driver = Driver::create($validated);

        return response()->json([
            'message' => 'Driver created successfully',
            'data' => $driver
        ], 201);
    }

    // GET /api/drivers/{driverId}
    public function getDriver($driverId)
    {
        $driver = Driver::find($driverId);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        return response()->json([
            'message' => "Driver #$driverId fetched successfully",
            'data' => $driver
        ]);
    }

    // PATCH /api/drivers/{driverId}
    public function updateDriver(Request $request, $driverId)
    {
        $driver = Driver::find($driverId);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:100',
            'lastname' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:drivers,email,' . $driverId,
            'password' => 'sometimes|string|min:6',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'id_card' => 'nullable|string|max:10',
            'vehicle_type' => 'nullable|string',
            'vehicle_name' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'license_plate' => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $driver->update($validated);

        return response()->json([
            'message' => "Driver #$driverId updated successfully",
            'data' => $driver
        ]);
    }

    // DELETE /api/drivers/{driverId}
    public function deleteDriver($driverId)
    {
        $driver = Driver::find($driverId);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $driver->delete();

        return response()->json([
            'message' => "Driver #$driverId deleted successfully"
        ]);
    }
}
