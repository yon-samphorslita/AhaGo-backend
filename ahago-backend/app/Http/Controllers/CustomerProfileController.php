<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerProfile;

class CustomerProfileController extends Controller
{
    public function getCustomers()
    {
        $customers = CustomerProfile::with('user') // load related user
            ->whereHas('user', function ($query) {
                $query->where('role', 'customer');
            })
            ->get();

        return response()->json($customers);
    }

    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'gender' => 'nullable|string',
            'city' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $customer = CustomerProfile::create($validated);

        return response()->json($customer, 201);
    }
}
