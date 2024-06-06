<?php

namespace App\Http\Controllers\Api;

use App\Models\Purchase;
use App\Models\Cart;
use App\Models\Items;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request!',
                'errors' => $validator->messages(),
            ], 422);
        }

        $itemsData = $request->input('items');
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $userId = $user->id;

        try {
            foreach ($itemsData as $itemData) {
                $item = Items::find($itemData['id']);
                if ($item->amount < $itemData['quantity']) {
                    return response()->json([
                        'status' => 400,
                        'message' => "Insufficient amount for item {$item->name}",
                    ], 400);
                }

                $item->amount -= $itemData['quantity'];
                $item->reserved += $itemData['quantity'];
                $item->save();

                // Ensure that the total price including VAT is calculated correctly
                $totalPriceWithVat = $item->price * $itemData['quantity'];

                Purchase::create([
                    'user_id' => $userId,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'total_price' => $totalPriceWithVat,
                    'vat' => $item->vat,
                    'status' => 'active',
                ]);
            }

            Cart::where('user_id', $userId)->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Purchase successful!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred during the purchase process',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        $status = $request->input('status');
        $purchase = Purchase::find($id);

        if ($purchase) {
            $purchase->status = $status;
            $purchase->save();

            $item = Items::find($purchase->item_id);
            if ($item) {
                $item->sold = $item->sold ?? 0;
                $item->reserved = $item->reserved ?? 0;
                $item->amount = $item->amount ?? 0;

                if ($status === 'closed') {
                    $item->amount += $purchase->quantity;
                    $item->reserved -= $purchase->quantity;
                } elseif ($status === 'canceled') {
                    $item->amount += $purchase->quantity;
                    $item->reserved -= $purchase->quantity;
                }

                $item->reserved = max(0, $item->reserved);
                $item->amount = max(0, $item->amount);

                $item->save();

                return response()->json(['status' => 'success', 'message' => 'Purchase status updated successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Purchase not found'], 404);
        }
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
