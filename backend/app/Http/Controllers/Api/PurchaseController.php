<?php

namespace App\Http\Controllers\Api;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'total_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        $purchase = new Purchase();
        $purchase->user_id = $request->user_id;
        $purchase->item_id = $request->item_id;
        $purchase->total_price = $request->total_price;
        $purchase->status = 'active';
        $purchase->save();

        return response()->json(['message' => 'Purchase saved successfully'], 200);
    }

    public function getUserPurchases($userId)
    {
        $purchases = Purchase::with(['item', 'user'])
            ->where('user_id', $userId)
            ->get();

        if ($purchases->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No purchases found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'purchases' => $purchases,
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json([
                'status' => 404,
                'message' => 'Purchase not found',
            ], 404);
        }

        $purchase->status = $request->status;
        $purchase->save();

        return response()->json(['message' => 'Purchase status updated successfully'], 200);
    }

    public function getTotalSpendingPerDay()
    {
        $spendingData = Purchase::selectRaw('DATE(created_at) as date, SUM(total_price) as total_spent')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $spendingData,
        ], 200);
    }
}
