<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    // GET /api/orderItems
    public function getAllOrderItem()
    {
//         return OrderItem::with('foodItem')->get();

        // Use singular 'foodItem' relation
        $orderItems = OrderItem::with('foodItem')->get();

        return response()->json([
            'message' => 'Order items fetched successfully',
            'data' => $orderItems,
        ]);
    }

    // GET /api/orderItems/topCategories
    public function getTopCategories()
    {
        // Step 1: Get top-selling food items by total quantity
        $topOrderItems = OrderItem::select('food_item_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('food_item_id')
            ->orderByDesc('total_quantity')
            ->take(20)
            ->with('foodItem.category') // singular here too
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

        // Split into 2 arrays for response
        $categories = array_keys($categoryData);
        $quantities = array_values($categoryData);

        return response()->json([
            'categories' => $categories,
            'quantities' => $quantities,
        ]);
    }

    // POST /api/orderItems
    public function createOrderItem(Request $request)
    {
        $validated = $request->validate([
            'food_item_id' => 'required|integer',
            'order_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $orderItem = OrderItem::create($validated);

        return response()->json([
            'message' => 'Order item created successfully',
            'data' => $orderItem,
        ], 201);
    }

    // GET /api/orderItems/{orderItemId}
    public function getOrderItem($orderItemId)
    {
        $orderItem = OrderItem::with('foodItem')->find($orderItemId);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        return response()->json([
            'message' => "Order item #$orderItemId fetched successfully",
            'data' => $orderItem,
        ]);
    }

    // PATCH /api/orderItems/{orderItemId}
    public function updateOrderItem(Request $request, $orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $validated = $request->validate([
            'quantity' => 'integer',
            'price' => 'numeric',
        ]);

        $orderItem->update($validated);

        return response()->json([
            'message' => "Order item #$orderItemId updated successfully",
            'data' => $orderItem,
        ]);
    }

    // DELETE /api/orderItems/{orderItemId}
    public function deleteOrderItem($orderItemId)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $orderItem->delete();

        return response()->json([
            'message' => "Order item #$orderItemId deleted successfully",
        ]);
    }
}
