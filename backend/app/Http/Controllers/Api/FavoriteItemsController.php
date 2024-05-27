<?php
namespace App\Http\Controllers\Api;

use App\Models\FavoriteItems;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FavoriteItemsController extends Controller
{
    public function index()
    {
        $favorites = FavoriteItems::all();
        if ($favorites->count() > 0) {
            return response()->json([
                'status' => 200,
                'favorites' => $favorites,
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

        $existingFavorite = FavoriteItems::where('user_id', $request->user_id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'status' => 409,
                'message' => 'Item already in favorites',
            ], 409);
        }

        $favoriteItem = new FavoriteItems();
        $favoriteItem->user_id = $request->user_id;
        $favoriteItem->item_id = $request->item_id;
        $favoriteItem->save();

        return response()->json(['message' => 'Favorite item saved successfully'], 200);
    }

    public function show($id)
    {
        $favorite = FavoriteItems::find($id);
        if ($favorite) {
            return response()->json([
                'status' => 200,
                'favorite' => $favorite,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No listing found',
            ], 404);
        }
    }

    public function userFavoritesCount($userId)
    {
        $count = FavoriteItems::where('user_id', $userId)->count();
        return response()->json([
            'status' => 200,
            'count' => $count,
        ], 200);
    }

    public function isFavorite($userId, $itemId)
    {
        $isFavorite = FavoriteItems::where('user_id', $userId)->where('item_id', $itemId)->exists();
        return response()->json([
            'status' => 200,
            'isFavorite' => $isFavorite,
        ], 200);
    }

    public function destroyByItemId(Request $request)
    {
        try {
            $userId = Auth::id();
            $itemId = $request->input('item_id');
            $favorite = FavoriteItems::where('user_id', $userId)
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
}
