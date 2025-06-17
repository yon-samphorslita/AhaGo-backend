<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
class OrderController extends Controller
{
    public function getOrders(){
        $orders = Order::with(['customer', 'restaurant', 'customerProfile'])->get();
        return response()->json($orders);
    }

    public function createOrder(Request $request){
        $validated = $request->validate([
            'restaurant_id' => 'required',
            'customer_id' => 'required',
            'driver_id' => 'nullable',
            'status' => 'nullable',
            'total_amount' => 'nullable',
            'remark' => 'nullable',
            'payment_status' => 'nullable',
            'order_type' => 'nullable'    
        ]);

        $order = Order::create($validated);

        return response()->json($order, 201);
    }
}
