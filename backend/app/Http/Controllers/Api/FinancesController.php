<?php

namespace App\Http\Controllers\Api;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class FinancesController extends Controller
{
    public function getFinances(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $query = Purchase::where('status', 'closed');

            if ($startDate) {
                $query->whereDate('created_at', '>=', Carbon::parse($startDate));
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', Carbon::parse($endDate));
            }

            $totalAmount = $query->sum('total_price');
            $amountWithVAT = $totalAmount * 1.21; // Assuming 21% VAT rate
            $amountWithoutVAT = $totalAmount;

            return response()->json([
                'totalAmount' => round($totalAmount, 2),
                'amountWithVAT' => round($amountWithVAT, 2),
                'amountWithoutVAT' => round($amountWithoutVAT, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching finance data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
