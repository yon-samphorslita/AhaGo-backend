<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    // GET /api/orderItems
    public function getAllOrderItems()
    {
        return OrderItem::with('foodItem')->get();
    }

    // GET /api/orderItems/:restId
    public function getAllOrderItemsById($restId)
    {
        $orders = OrderItem::with('foodItem', 'order')->get();

        $ordersById = $orders->where('foodItem.restaurant_id', $restId);

        return $ordersById;
    }

    // GET /api/orderItems/topCategories
    public function getTopCategories() {
        // Step 1: Get top-selling food items by total quantity
        $topOrderItems = OrderItem::select('food_item_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('food_item_id')
        ->orderByDesc('total_quantity')
        ->take(20)
        ->with('foodItem.category')
        ->get();

        // Group by category name and sum quantities
        $categoryData = [];

        foreach ($topOrderItems as $item) {
            $category = $item->foodItem->category ?? null;

            if ($category) {
                $name = $category->name;
                $categoryData[$name] = ($categoryData[$name] ?? 0) + $item->total_quantity;
            }
        }

        // Split into 2 arrays
        $categories = array_keys($categoryData);
        $totals = array_values($categoryData);

        return response()->json([
            'categories' => $categories,
            'quantities' => $totals
        ]);
    }

    // POST /api/orderItems
    public function createOrderItem(Request $request)
    {
        $validated = $request->validate([
            'food_item_id' => 'integer',
            'order_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'numeric'
        ]);

        $orderItem = OrderItem::create($validated);

        return response()->json([
            'message' => 'OrderItem created successfully',
            'data' => $orderItem
        ], 201);
    }

    // GET /api/orderItems/{orderItemId}
    public function getOrderItems($orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'message' => "OrderItem #$orderItemId fetched successfully",
            'data' => $orderItem
        ]);
    }

    // PATCH /api/orderItems/{orderItemId}
    public function updateOrderItem(Request $request, $orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'quantity' => 'integer',
            'price' => 'numeric'
        ]);

        $orderItem->update($validated);

        return response()->json([
            'message' => "OrderItem #$orderItemId updated successfully",
            'data' => $orderItem
        ]);
    }

    // DELETE /api/orderItems/{orderItemId}
    public function deleteOrderItem($orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) {
            return response()->json(['message' => 'OrderItem not found'], 404);
        }

        $orderItem->delete();

        return response()->json([
            'message' => "OrderItem #$orderItemId deleted successfully"
        ]);
    }
}
