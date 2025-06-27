<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /api/customers
    public function getCustomers()
    {
        return CustomerProfile::with('user')->get();
    }

     // GET /api/customers/count
    public function getCustomersCount(){
        return CustomerProfile::all()->count();
    }

    // POST /api/customers
    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'integer',
            'gender' => 'string|nullable',
            'city' => 'string|nullable'
        ]);

        $customer = CustomerProfile::create($validated);

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    // GET /api/customers/{customerId}
    public function getCustomer($customerId)
    {
        $customer = CustomerProfile::with('user')->find($customerId);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'message' => "Customer #$customerId fetched successfully",
            'data' => $customer
        ]);
    }

    // PATCH /api/customers/{customerId}
    public function updateCustomer(Request $request, $customerId)
    {
        $customer = CustomerProfile::find($customerId);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'integer',
            'gender' => 'string',
            'city' => 'string'
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => "Customer #$customerId updated successfully",
            'data' => $customer
        ]);
    }

    // DELETE /api/customers/{customerId}
    public function deleteCustomer($customerId)
    {
        $customer = CustomerProfile::find($customerId);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json([
            'message' => "Customer #$customerId deleted successfully"
        ]);
    }
}
