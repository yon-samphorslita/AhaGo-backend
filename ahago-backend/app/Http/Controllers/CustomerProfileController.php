<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerProfile;
class CustomerProfileController extends Controller
{
    public function getCustomers(){
    $customers = CustomerProfile::with('user') // load related user
            ->whereHas('user', function ($query) {
                $query->where('role', 'customer');
            })
            ->get();

        return response()->json($customers);
    }

    public function createCustomer(Request $request){
        $validated = $request->validate([
            'user_id' => 'required',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender'=> 'nullable|string',
            'city'=> 'nullable|string',
            'latitude'=> 'nullable|string',
            'longitude'=> 'nullable|string',
        ]);

        $customer = CustomerProfile::create($validated);

        return response()->json($customer, 201);
    }
    public function updateCustomerProfile(Request $request)
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
            'customer_profile.id' => 'nullable|string',
            'customer_profile.first_name' => 'nullable|string',
            'customer_profile.last_name' => 'nullable|string',
            'customer_profile.gender' => 'nullable|string',
            'customer_profile.city' => 'nullable|string',
            'customer_profile.latitude' => 'nullable|string',
            'customer_profile.longitude' => 'nullable|string',
        ]);

        // Update User model fields
        $user->update($validatedUser);

        // Update customerProfile model fields (create if missing)
        if ($user->customerProfile) {
            $user->customerProfile->update($request->input('customer_profile'));
        } else {
            $user->customerProfile()->create($request->input('customer_profile'));
        }

        return response()->json(['message' => 'customer profile updated successfully']);
    }
}
