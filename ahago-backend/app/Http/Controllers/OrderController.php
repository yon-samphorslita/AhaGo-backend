<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Enum;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // GET /api/orders
    public function getOrders(Request $request)
    {
//         $query = Order::with(['restaurant', 'customer', 'orderItems', 'orderItems.foodItem']);

        return Order::with('foodItems','restaurant', 'customer')->get();
        
      
//         $query = Order::with(['restaurant', 'customer']);


//         if ($request->has('status')) {
//             $query->where('status', $request->status);
//         }

//         return response()->json($query->get());
    }

    // GET /api/orders/count
    public function getOrdersCount()
    {
        return Order::all()->count();
    }

    // GET /api/orders/orderTypes
    public function getOrdersTypes()
    {
        $counts = DB::table('orders')
            ->select('order_type', DB::raw('count(*) as total'))
            ->groupBy('order_type')
            ->get();
        return $counts;
    }

    // GET /api/orders/rest/:restId
    public function getOrdersByRest($restId)
    {
        return Order::with('foodItems')
         ->where('restaurant_id', $restId)
         ->get();
    }

    // GET /api/orders/recent/:restId
    public function getRecentOrders($restId)
    {
        // array of dates from last 7 days
        $dates = [];
        $ordersByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $dates[] = $date;
            $orders = Order::whereDate('created_at', $date)
            ->where('restaurant_id', $restId)
            ->get();
            $ordersByDay[] = $orders->count();
        };
        // array of #orders per day from last 7 days
        // $sevenDaysAgo = now()->subDays(7)->toDateString(); // returns 'YYYY-MM-DD'
        // $orders = Order::whereDate('created_at', '>=', $sevenDaysAgo)
        //             ->where('restaurant_id', $restId)
        //             ->get();
        
        return response()->json([
            'dates' => $dates,
            'orders' => $ordersByDay
        ]);

    }

    // POST /api/orders
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'integer',
            'customer_id' => 'integer',
            'driver_id' => 'integer',
            'status' => ['nullable', new Enum(OrderStatus::class)],
            'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['sometimes', new Enum(PaymentStatus::class)],
            'remark' => ['string', 'nullable'],
            'order_type' => ['nullable', new Enum(OrderType::class)]
        ]);

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    // GET /api/orders/{orderId}
    public function getOrder($orderId)
    {

        $order = Order::with(['restaurant', 'customer', 'orderItems', 'orderItems.foodItem'])->find($orderId);

//         $order = Order::with('foodItems')->find($orderId);


        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json([
            'message' => "Order #$orderId fetched successfully",
            'data' => $order,
        ]);
    }

    // PATCH /api/orders/{orderId}
    public function updateOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validated = $request->validate([

            // 'restaurant_id' => 'integer',
            // 'customer_id' => 'integer',
            // 'driver_id' => 'integer',
            'status' => ['sometimes', new Enum(OrderStatus::class)],
            // 'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['sometimes', new Enum(PaymentStatus::class)],
            // 'remark' => ['string', 'nullable'],
            // 'order_type' => ['sometimes', new Enum(OrderType::class)],

        ]);

        $oldStatus = $order->status;
        $order->update($validated);
        $newStatus = $order->status;

        if ($oldStatus !== $newStatus && $order->driver_id) {
            $this->sendNotificationByStatus($order->driver_id, $order->id, $newStatus);
        }

        return response()->json([
            'message' => "Order #$orderId updated successfully",
            'data' => $order,
        ]);
    }

    // DELETE /api/orders/{orderId}
    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => "Order #$orderId deleted successfully"]);
    }

    // POST /api/orders/assign
    public function assignOrderToDriver(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:driver_profiles,id',
        ]);

        $order = Order::find($validated['order_id']);
        $order->driver_id = $validated['driver_id'];
        $order->status = OrderStatus::PENDING->value;
        $order->save();

        // ❌ DO NOT notify here to prevent duplication
        // The updateOrder() will notify when status is set

        return response()->json(['message' => 'Order assigned successfully']);
    }

    // Status Notification Dispatcher
protected function sendNotificationByStatus($driverId, $orderId, $status)
{
    switch ($status) {
        case OrderStatus::PENDING->value:
            $this->notifyDriver($driverId, $orderId, "New Incoming Order", "You have a new incoming order #$orderId. Please check your app for details.");
            break;

        case OrderStatus::PREPARING->value:
            $this->notifyDriver($driverId, $orderId, "Order Accepted", "You have successfully accepted order #$orderId. Please proceed with the delivery.");
            break;

        case OrderStatus::COMPLETED->value:
            $this->notifyDriver($driverId, $orderId, "Order Completed", "Order #$orderId has been completed successfully. Thank you for your service!");
            break;
    }
}


    // Create Notification Only If It Doesn't Exist
protected function notifyDriver($driverId, $orderId, $title, $message)
{
    Notification::create([
        'driver_id' => $driverId,
        'title' => $title,
        'message' => $message,
    ]);
    Log::info("Notification sent: [$title] to driver $driverId for order $orderId");
}

}
