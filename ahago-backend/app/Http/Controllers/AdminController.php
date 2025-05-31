<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // GET /api/admins
    public function getAdmins()
    {
        $admins = Admin::all(); // Fetch all admins

        return response()->json([
            'message' => 'Admin list fetched successfully',
            'data' => $admins
        ]);
    }

    // POST /api/admins
    public function createAdmin(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $admin = Admin::create($validated);

        return response()->json([
            'message' => 'Admin created successfully',
            'data' => $admin
        ], 201);
    }

    // GET /api/admins/{adminId}
    public function getAdmin($adminId)
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        return response()->json([
            'message' => "Admin #$adminId fetched successfully",
            'data' => $admin
        ]);
    }

    // PATCH /api/admins/{adminId}
    public function updateAdmin(Request $request, $adminId)
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:100',
            'lastname' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:admins,email,' . $adminId,
            'password' => 'sometimes|string|min:6',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $admin->update($validated);

        return response()->json([
            'message' => "Admin #$adminId updated successfully",
            'data' => $admin
        ]);
    }

    // DELETE /api/admins/{adminId}
    public function deleteAdmin($adminId)
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $admin->delete();

        return response()->json([
            'message' => "Admin #$adminId deleted successfully"
        ]);
    }
}
