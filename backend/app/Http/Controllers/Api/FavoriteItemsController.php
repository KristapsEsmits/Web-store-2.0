<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorites;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FavoriteItemsController extends Controller
{

    public function index()
    {
        $categories = Favorites::all();
        if ($categories->count() > 0) {
            return response()->json([
                'status' => 200,
                'categories' => $categories,
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
        $favoriteItem = new Favorites();
        $favoriteItem->user_id = $request->user_id;
        $favoriteItem->item_id = $request->item_id;
        $favoriteItem->save();

        // Optionally, return a response indicating success
        return response()->json(['message' => 'Favorite item saved successfully']);
    }


    public function show($id)
    {
        $categories = Favorites::find($id);
        if ($categories) {
            return response()->json([
                'status' => 200,
                'categories' => $categories,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No listing found',
            ], 404);
        }
    }
}