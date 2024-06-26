<?php

namespace App\Http\Controllers\Api;

use App\Models\Purchase;
use App\Models\Items;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FinancesController extends Controller
{
    public function getFinances(Request $request)
    {
        $purchases = Purchase::with(['item', 'item.category'])
            ->where('status', 'closed')
            ->get();

        $categoryFinances = $purchases->groupBy('item.category.category_name')->map(function ($categoryPurchases) {
            $category_id = $categoryPurchases->first()->item->category->id;
            $totalEarnedWithVat = $categoryPurchases->sum('total_price');
            $totalVat = $categoryPurchases->sum(function ($purchase) {
                return $purchase->total_price * ($purchase->vat / (100 + $purchase->vat));
            });
            $totalEarnedWithoutVat = $totalEarnedWithVat - $totalVat;

            return [
                'category_id' => $category_id,
                'total_earned_with_vat' => $totalEarnedWithVat,
                'total_vat' => $totalVat,
                'total_earned_without_vat' => $totalEarnedWithoutVat
            ];
        });

        $itemFinances = $purchases->groupBy('item.name')->map(function ($itemPurchases) {
            $item_id = $itemPurchases->first()->item->id;
            $totalEarnedWithVat = $itemPurchases->sum('total_price');
            $totalVat = $itemPurchases->sum(function ($purchase) {
                return $purchase->total_price * ($purchase->vat / (100 + $purchase->vat));
            });
            $totalEarnedWithoutVat = $totalEarnedWithVat - $totalVat;

            return [
                'item_id' => $item_id,
                'total_earned_with_vat' => $totalEarnedWithVat,
                'total_vat' => $totalVat,
                'total_earned_without_vat' => $totalEarnedWithoutVat
            ];
        });

        $mostSoldItem = Items::orderBy('sold', 'desc')->first();

        $activeOrders = Purchase::where('status', 'active')->count();
        $canceledOrders = Purchase::where('status', 'canceled')->count();
        $closedOrders = Purchase::where('status', 'closed')->count();

        $totalEarnedWithVat = $purchases->sum('total_price');
        $totalVat = $purchases->sum(function ($purchase) {
            return $purchase->total_price * ($purchase->vat / (100 + $purchase->vat));
        });
        $totalEarnedWithoutVat = $totalEarnedWithVat - $totalVat;

        return response()->json([
            'categories' => $categoryFinances,
            'items' => $itemFinances,
            'mostSoldItem' => [
                'name' => $mostSoldItem->name,
                'description' => $mostSoldItem->description,
                'price' => $mostSoldItem->price,
                'image' => $mostSoldItem->img
            ],
            'activeOrders' => $activeOrders,
            'canceledOrders' => $canceledOrders,
            'closedOrders' => $closedOrders,
            'totalEarnedWithVat' => $totalEarnedWithVat,
            'totalVat' => $totalVat,
            'totalEarnedWithoutVat' => $totalEarnedWithoutVat
        ]);
    }
}
