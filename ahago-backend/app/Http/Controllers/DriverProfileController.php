<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverProfile;
class DriverProfileController extends Controller
{
    public function getDrivers() {
        $drivers = DriverProfile::with('user') // load related user
            ->whereHas('user', function ($query) {
                $query->where('role', 'driver');
            })
            ->get();

        return response()->json($drivers);    
    }

    public function getDriver(Request $request) {
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

        return response()->json($driver, 201);    }
}
