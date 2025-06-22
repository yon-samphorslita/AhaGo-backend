<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $query = Notification::query();

        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $notifications = $query->get();

        return response()->json($notifications);
    }

    public function getDriverNotifications($driverId)
    {
        return Notification::where('driver_id', $driverId)
            ->latest()
            ->get();
    }

    // GET /api/notifications/rest/:restId
    public function getOwnerNotifications($restId)
    {
        return Notification::where('restaurant_id', $restId)
            ->get();
    }

    // POST /api/notifications/
    public function createNotification(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'restaurant_id' => 'nullable|integer',
            'driver_id' => 'nullable|integer',
            'admin_id' => 'nullable|integer',
            'title' => 'string|required',
            'message' => 'string'
        ]);

        $notification = Notification::create($validated);

        return response()->json([
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }

}
