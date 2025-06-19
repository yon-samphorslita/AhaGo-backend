<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules\Enum;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Notification;
class OrderController extends Controller
{
    // GET /api/orders
    public function getOrders(Request $request)
    {
        $query = Order::with(['restaurant', 'customer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
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
            'payment_status' => ['nullable', new Enum(PaymentStatus::class)],
            'remark' => ['string', 'nullable'],
            'order_type' => ['nullable', new Enum(OrderType::class)]
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
            'status' => ['sometimes', new Enum(OrderStatus::class)],
            'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['sometimes', new Enum(PaymentStatus::class)],
            'remark' => ['string', 'nullable'],
            'order_type' => ['sometimes', new Enum(OrderType::class)]
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

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validated = $request->validate([
            'status' => ['required', new \Illuminate\Validation\Rules\Enum(OrderStatus::class)],
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json(['message' => 'Order updated successfully', 'data' => $order]);
    }
    
    public function notifyDriverIncomingOrder($driverId, $orderId)
    {
        $title = "New Incoming Order";
        $message = "You have a new incoming order #{$orderId}. Please check your app for details.";

        Notification::create([
            'driver_id' => $driverId,
            'title' => $title,
            'message' => $message,
        ]);
    }

    // When a driver accepts an order:
    public function notifyDriverAcceptedOrder($driverId, $orderId)
    {
        $title = "Order Accepted";
        $message = "You have successfully accepted order #{$orderId}. Please proceed with the delivery.";

        Notification::create([
            'driver_id' => $driverId,
            'title' => $title,
            'message' => $message,
        ]);
    }

    // Example usage when assigning order:
   public function assignOrderToDriver(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'driver_id' => 'required|exists:driver_profiles,id', // assuming you have a drivers table
    ]);

    $order = Order::find($validated['order_id']);
    $order->driver_id = $validated['driver_id'];
    $order->status = OrderStatus::Incoming; // or whatever status applies
    $order->save();

    $this->notifyDriverIncomingOrder($validated['driver_id'], $validated['order_id']);

    return response()->json(['message' => 'Order assigned and driver notified']);
}

}
