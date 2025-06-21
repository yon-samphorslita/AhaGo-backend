<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    // GET /api/orderItems
    public function getAllOrderItems()
    {
        return OrderItem::all();
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
