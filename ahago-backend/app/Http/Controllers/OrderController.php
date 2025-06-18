<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Enum;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /api/orders
    public function getOrders()
    {
        return Order::all();
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
        $order = Order::find($orderId);

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
            'restaurant_id' => 'integer',
            'customer_id' => 'integer',
            'driver_id' => 'integer',
            'status' => ['required', new Enum(OrderStatus::class)],
            'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['required', new Enum(PaymentStatus::class)],
            'remark' => ['string', 'nullable'],
            'order_type' => ['required', new Enum(OrderType::class)]
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
