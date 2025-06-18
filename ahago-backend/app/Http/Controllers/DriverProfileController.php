<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use Illuminate\Http\Request;

class DriverProfileController extends Controller
{
    // GET /api/drivers
    public function getDrivers()
    {
        return $drivers = DriverProfile::all();
    }

    // POST /api/drivers
    public function createDriver(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string'
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
}
