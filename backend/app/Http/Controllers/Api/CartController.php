<?php
namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Items;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::all();
        if ($cart->count() > 0) {
            return response()->json([
                'status' => 200,
                'cart' => $cart,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No data found!',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ], 422);
        }

        $existingCart = Cart::where('user_id', $request->user_id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($existingCart) {
            return response()->json([
                'status' => 409,
                'message' => 'Item already in cart',
            ], 409);
        }

        $cart = new Cart();
        $cart->user_id = $request->user_id;
        $cart->item_id = $request->item_id;
        $cart->save();

        return response()->json(['message' => 'Item added to cart successfully'], 200);
    }

    public function show($id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            return response()->json([
                'status' => 200,
                'cart' => $cart,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No item found',
            ], 404);
        }
    }

    public function getUserCart($userId)
    {
        $cartItems = Cart::with('item')->where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No items found in the cart',
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'cartItems' => $cartItems,
        ], 200);
    }

    public function destroyByCartId(Request $request)
    {
        try {
            $userId = $request->route('user_id');
            $itemId = $request->route('item_id');

            $favorite = Cart::where('user_id', $userId)
                ->where('item_id', $itemId)
                ->first();

            if ($favorite) {
                $favorite->delete();
                return response()->json(['message' => 'Favorite item removed successfully'], 200);
            } else {
                return response()->json(['message' => 'Favorite item not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error while removing favorite item', 'error' => $e->getMessage()], 500);
        }
    }

    // CartController.php

    public function clearCartByUserId($userId)
    {
        $cartItems = Cart::where('user_id', $userId)->delete();

        if ($cartItems) {
            return response()->json(['status' => 200, 'message' => 'Cart cleared successfully'], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'No items found in cart'], 404);
        }
    }


    public function getUserCartCount($userId)
    {
        $cartCount = Cart::where('user_id', $userId)->count();

        return response()->json([
            'status' => 200,
            'count' => $cartCount,
        ], 200);
    }

    public function isItemInCart($userId, $itemId)
    {
        $isInCart = Cart::where('user_id', $userId)->where('item_id', $itemId)->exists();
        return response()->json([
            'status' => 200,
            'isInCart' => $isInCart,
        ], 200);
    }

    public function getCartStatus(Request $request)
    {
        $userId = $request->input('userId');
        $itemIds = $request->input('itemIds');

        $cartItems = Cart::where('user_id', $userId)
            ->whereIn('item_id', $itemIds)
            ->pluck('item_id')
            ->toArray();

        $cartStatus = array_fill_keys($itemIds, false);
        foreach ($cartItems as $itemId) {
            $cartStatus[$itemId] = true;
        }

        return response()->json($cartStatus);
    }
}
