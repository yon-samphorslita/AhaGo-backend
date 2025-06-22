<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationController extends Controller
{
    public function getNotifications() {
        return $notifications = Notification::all();
    }
    
    public function getDriverNotifications($driverId)
    {
        return Notification::where('driver_id', $driverId)
            ->latest()
            ->get();
    }

}
