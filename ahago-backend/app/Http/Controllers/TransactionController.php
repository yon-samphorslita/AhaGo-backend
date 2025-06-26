<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    // GET /api/transactions
    public function getTransactions()
    {
        return Transaction::with(['restaurant', 'order', 'customer.user'])->get();
    }

    // GET /api/transactions/revenue
    public function getRevenue()
    {
        return Transaction::sum('amount');
    }

    // POST /api/transactions
    public function createTransaction(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'integer',
            'restaurant_id' => 'integer',
            'order_id' => 'integer',
            'payment' => 'string',
            'amount' => 'integer'
        ]);

        $transaction = Transaction::create($validated);

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], 201);
    }

    // GET /api/transactions/{tId}
    public function getTransaction($tId)
    {
        $transaction = Transaction::find($tId);

        if (!$transaction) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'message' => "Transaction #$tId fetched successfully",
            'data' => $transaction
        ]);
    }

     // GET /api/transactions/rest/{restId}
     public function getAllByRestId($restId)
     {
         return Transaction::with(['customer', 'restaurant', 'order', 'customer.user', 'order.foodItems', 'restaurant.user'])
         ->where('restaurant_id', $restId)
         ->get();
     }

    // GET /api/transactions/recent/{restId}
    public function getRecentTransactions($restId)
    {
       // 1. Generate last 7 dates (oldest to newest)
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = Carbon::today()->subDays($i)->toDateString();
        }

        // 2. Query DB for summed amount per day
        $queryResults = Transaction::where('restaurant_id', $restId)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy(Transaction::raw('DATE(created_at)'))
            ->pluck('total', 'date'); // associative: [date => total]

        // 3. Build totals array matching date order
        $totals = [];
        foreach ($dates as $date) {
            $totals[] = $queryResults[$date] ?? 0; // fill 0 if date not found
        }

        // 4. Return both arrays
        return response()->json([
            'dates' => $dates,
            'totals' => $totals
        ]);
    }

    // DELETE /api/transactions/{tId}
    public function deleteTransaction($tId)
    {
        $transaction = Transaction::find($tId);

        if (!$transaction) {
            return response()->json(['message' => 'OrderItem not found'], 404);
        }

        $transaction->delete();

        return response()->json([
            'message' => "Transaction #$tId deleted successfully"
        ]);
    }
}
