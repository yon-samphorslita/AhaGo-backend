<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Enum;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    // GET /api/orders
    public function getOrders()
    {
        return Order::with('foodItems')->get();
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
            'status' => ['required', new Enum(OrderStatus::class)],
            'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['required', new Enum(PaymentStatus::class)],
            'remark' => ['string', 'nullable'],
            'order_type' => ['required', new Enum(OrderType::class)]
        ]);

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    // GET /api/orders/{orderId}
    public function getOrder($orderId)
    {
        $order = Order::with('foodItems')->find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json([
            'message' => "Order #$orderId fetched successfully",
            'data' => $order
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
            'status' => ['nullable', new Enum(OrderStatus::class)],
            // 'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['nullable', new Enum(PaymentStatus::class)],
            // 'remark' => ['string', 'nullable'],
            // 'order_type' => ['required', new Enum(OrderType::class)]
        ]);

        $order->update($validated);

        return response()->json([
            'message' => "Order #$orderId updated successfully",
            'data' => $order
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

        return response()->json([
            'message' => "Order #$orderId deleted successfully"
        ]);
    }
}
