<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Carbon\Carbon;

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
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/orders/count
    public function getOrdersCount()
    {
        $orderC = Order::all()->count();
        return $orderC;
    }

    // GET /api/orders/orderTypes
    public function getOrdersTypes()
    {
        return DB::table('orders')
            ->select('order_type', DB::raw('count(*) as total'))
            ->groupBy('order_type')
            ->get();
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

    // ✅ FIXED: GET /api/orders/{orderId}
    // public function getOrder($orderId)
    // {
    //     try {
    //         $order = Order::with([
    //             'foodItems',
    //             'restaurant',
    //             'customer',
    //             'driver',
    //             'orderItems',
    //             'orderItems.foodItem'
    //         ])->find($orderId);

    //         if (!$order) {
    //             return response()->json(['message' => 'Order not found'], 404);
    //         }

    //         return response()->json([
    //             'message' => "Order #$orderId fetched successfully",
    //             'data' => $order
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error("Error fetching order #$orderId: " . $e->getMessage());
    //         return response()->json([
    //             'message' => 'Error fetching order',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

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
        try {
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
            'order_type' => ['sometimes', new Enum(OrderType::class)],
        ]);

        $oldStatus = $order->status;
        $order->update($validated);
        $newStatus = $order->status;

        if ($oldStatus !== $newStatus && $order->driver_id) {
            $this->sendNotificationByStatus($order->driver_id, $order->id, $newStatus);
        }

            return response()->json([
                'message' => "Order #$orderId updated successfully",
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating order #$orderId: " . $e->getMessage());
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
            Log::error("Error deleting order #$orderId: " . $e->getMessage());
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
            Log::error("Error updating status for order #$orderId: " . $e->getMessage());
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

        $order = Order::find($validated['order_id']);
        $order->driver_id = $validated['driver_id'];
        $order->status = OrderStatus::PENDING->value;
        $order->save();

        return response()->json(['message' => 'Order assigned successfully']);
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
                        'quantity' => $item->quantity ?? 1,
                        'notes' => $item->note ?? '-',
                        'price' => $item->price,
                        'total' => ($item->quantity ?? 1) * $item->price
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
            Log::error("Error fetching detailed info for order #$orderId: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔔 Notify driver about new order
    protected function notifyDriverIncomingOrder($driverId, $orderId)
    {
        Notification::create([
            'driver_id' => $driverId,
            'title' => "New Incoming Order",
            'message' => "You have a new incoming order #{$orderId}. Please check your app for details.",
        ]);
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
