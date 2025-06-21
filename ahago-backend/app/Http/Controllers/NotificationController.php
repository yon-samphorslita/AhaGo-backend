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

}
