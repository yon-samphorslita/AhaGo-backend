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

class OrderController extends Controller
{
    // GET /api/orders
    public function getOrders()
    {
        try {
            $orders = Order::with(['foodItems', 'restaurant', 'customer', 'driver'])->get();

            return response()->json([
                'message' => 'Orders fetched successfully',
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch orders: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/orders/{orderId}
    public function getOrder($orderId)
    {
        try {
            $order = Order::with(['foodItems', 'restaurant', 'customer', 'driver'])->find($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            return response()->json([
                'message' => "Order #$orderId fetched successfully",
                'data' => $order
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching order #' . $orderId . ': ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/orders
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurant_profiles,id',
            'customer_id' => 'required|integer|exists:customer_profiles,id',
            'driver_id' => 'nullable|integer|exists:driver_profiles,id',
            'status' => ['nullable', new Enum(OrderStatus::class)],
            'total_amount' => ['nullable', 'numeric'],
            'payment_status' => ['nullable', new Enum(PaymentStatus::class)],
            'remark' => ['nullable', 'string'],
            'order_type' => ['nullable', new Enum(OrderType::class)]
        ]);

        try {
            $order = Order::create($validated);

            // Notify driver if assigned and status is pending
            if (!empty($order->driver_id) && $order->status === OrderStatus::PENDING->value) {
                $this->notifyDriverIncomingOrder($order->driver_id, $order->id);
            }

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating order: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // PATCH /api/orders/{orderId}
    public function updateOrder(Request $request, $orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $validated = $request->validate([
                'status' => ['nullable', new Enum(OrderStatus::class)],
                'payment_status' => ['nullable', new Enum(PaymentStatus::class)],
                'remark' => ['nullable', 'string'],
                'order_type' => ['nullable', new Enum(OrderType::class)],
                'total_amount' => ['nullable', 'numeric'],
            ]);

            $order->update($validated);

            return response()->json([
                'message' => "Order #$orderId updated successfully",
                'data' => $order
            ]);
        } catch (\Exception $e) {
            \Log::error("Error updating order #$orderId: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/orders/{orderId}
    public function deleteOrder($orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $order->delete();

            return response()->json([
                'message' => "Order #$orderId deleted successfully"
            ]);
        } catch (\Exception $e) {
            \Log::error("Error deleting order #$orderId: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // PATCH /api/orders/{orderId}/status
    public function updateOrderStatus(Request $request, $orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $validated = $request->validate([
                'status' => ['required', new Enum(OrderStatus::class)],
            ]);

            $order->status = $validated['status'];
            $order->save();

            return response()->json([
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            \Log::error("Error updating status for order #$orderId: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/orders/assign
    public function assignOrderToDriver(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:driver_profiles,id',
        ]);

        try {
            $order = Order::find($validated['order_id']);
            $order->driver_id = $validated['driver_id'];
            $order->status = OrderStatus::PENDING->value;
            $order->save();

            $this->notifyDriverIncomingOrder($validated['driver_id'], $validated['order_id']);

            return response()->json(['message' => 'Order assigned and driver notified']);
        } catch (\Exception $e) {
            \Log::error("Error assigning order #{$validated['order_id']} to driver #{$validated['driver_id']}: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to assign order to driver',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/orders/{orderId}/details
    public function showOrderDetails($orderId)
    {
        try {
            $order = Order::with([
                'items.foodItem',
                'customer',
                'driver',
                'restaurant'
            ])->find($orderId);

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            return response()->json([
                'order_id' => $order->id,
                'status' => $order->status,
                'order_type' => $order->order_type,
                'payment_status' => $order->payment_status,
                'remark' => $order->remark,
                'total_amount' => $order->total_amount,

                'items' => $order->items->map(function ($item) {
                    return [
                        'item_name' => optional($item->foodItem)->name ?? 'Unknown',
                        'quantity' => $item->quantity ?? 1,   // default to 1 if null
                        'notes' => $item->note ?? '-',
                        'price' => $item->price,
                        'total' => ($item->quantity ?? 1) * $item->price  // safer total calculation
                    ];
            }),

                'customer' => [
                    'firstname' => optional($order->customer)->firstname ?? '',
                    'lastname' => optional($order->customer)->lastname ?? '',
                    'phone' => optional($order->customer)->phone_number ?? '',
                    'address' => optional($order->customer)->address ?? ''
                ],

                'driver' => [
                    'name' => optional($order->driver)->full_name ?? 'N/A',
                    'phone' => optional($order->driver)->phone_number ?? '',
                    'vehicle' => optional($order->driver)->vehicle ?? '',
                    'vehicle_number' => optional($order->driver)->vehicle_number ?? '',
                    'image_url' => $order->driver && $order->driver->image
                        ? asset('storage/' . $order->driver->image)
                        : null
                ],

                'restaurant' => [
                    'name' => optional($order->restaurant)->name ?? '',
                    'address' => optional($order->restaurant)->address ?? '',
                    'phone' => optional($order->restaurant)->phone_number ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("Error fetching detailed info for order #$orderId: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Helper to notify driver about incoming order
    protected function notifyDriverIncomingOrder($driverId, $orderId)
    {
        Notification::create([
            'driver_id' => $driverId,
            'title' => "New Incoming Order",
            'message' => "You have a new incoming order #{$orderId}. Please check your app for details.",
        ]);
    }

    // Helper to notify driver about accepted order
    protected function notifyDriverAcceptedOrder($driverId, $orderId)
    {
        Notification::create([
            'driver_id' => $driverId,
            'title' => "Order Accepted",
            'message' => "You have successfully accepted order #{$orderId}. Please proceed with the delivery.",
        ]);
    }
}
