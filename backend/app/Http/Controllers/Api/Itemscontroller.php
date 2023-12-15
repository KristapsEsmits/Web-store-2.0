<?php

namespace App\Http\Controllers\Api;

use App\Models\Items;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ItemsController extends Controller
{
    public function index()
    {
        $items = Items::all();
        if ($items->count() > 0) {
            return response()->json([
                'status' => 200,
                'items' => $items,
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
            'name' => 'required|string|max:40',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric',
            'brand_id' => 'required|exists:brands,id', // Ensure brand_id exists in the 'brands' table
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request!',
                'errors' => $validator->messages(),
            ], 422);
        } else {
            $randomString = Str::random(10);
            $imgPath = $request->file('img')->storeAs('uploads', $randomString . '.' . $request->file('img')->getClientOriginalExtension(), 'public');

            $item = Items::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'brand_id' => $request->brand_id, // Add this line to store brand_id
                'img' => $randomString . '.' . $request->file('img')->getClientOriginalExtension(),
            ]);

            if ($item) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Data created successfully!',
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Internal server error!',
                ], 500);
            }
        }
    }


    public function show($id)
    {
        $items = Items::find($id);
        if ($items) {
            return response()->json([
                'status' => 200,
                'items' => $items,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No listing found',
            ], 404);
        }
    }

    public function destroy($id)
    {
        $item = Items::find($id);

        if (!$item) {
            return response()->json([
                'status' => 404,
                'message' => 'Item not found',
            ], 404);
        }

        $imagePath = public_path("/storage/uploads/{$item->img}");

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Item deleted successfully',
        ], 200);
    }
}