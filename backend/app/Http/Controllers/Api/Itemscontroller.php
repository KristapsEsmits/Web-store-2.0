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
            'name' => 'required|string',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric',
            'brand_id' => 'required|exists:brands,id',
            'categories_id' => 'required|exists:categories,id',
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
                'brand_id' => $request->brand_id,
                'categories_id' => $request->categories_id,
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

    public function edit($id)
    {
        $item = Items::find($id);
        if ($item) {
            return response()->json([
                'status' => 200,
                'items' => $item,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No listing found',
            ], 404);
        }
    }

    public function update(Request $request, int $id)
    {
        $item = Items::find($id);

        if ($item) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string|max:1000',
                'price' => 'required|numeric',
                'brand_id' => 'required|exists:brands,id',
                'categories_id' => 'required|exists:categories,id',
                'img' => $request->hasFile('img') ? 'image|mimes:jpeg,png,jpg,gif,svg' : 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Bad Request data wrong!',
                    'errors' => $validator->messages(),
                ], 422);
            } else {
                if ($request->hasFile('img')) {
                    $randomString = Str::random(10);
                    $imgPath = $request->file('img')->storeAs('uploads', $randomString . '.' . $request->file('img')->getClientOriginalExtension(), 'public');

                    if ($item->img) {
                        $oldImagePath = public_path("/storage/uploads/{$item->img}");
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $item->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'price' => $request->price,
                        'brand_id' => $request->brand_id,
                        'categories_id' => $request->categories_id,
                        'img' => $randomString . '.' . $request->file('img')->getClientOriginalExtension(),
                    ]);
                } else {
                    $item->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'price' => $request->price,
                        'brand_id' => $request->brand_id,
                        'categories_id' => $request->categories_id,
                    ]);
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Data updated successfully!',
                ], 200);
            }
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

    public function frontPageItems()
    {
        $items = Items::latest()->take(4)->get();

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

    public function searchItems(Request $request)
    {
        $name = $request->query('name');
        $items = Items::where('name', 'LIKE', "%$name%")->get();
        return response()->json(['items' => $items]);
    }

    public function getCategoryItemCount()
    {
        $categoryCounts = Items::selectRaw('categories_id, COUNT(*) as item_count')
            ->groupBy('categories_id')
            ->with('category')
            ->get();

        $data = $categoryCounts->map(function ($item) {
            return [
                'category_name' => $item->category->category_name,
                'item_count' => $item->item_count,
            ];
        });

        return response()->json(['category_counts' => $data]);
    }

    public function getItemsByBrand($brandId)
    {
        $items = Items::where('brand_id', $brandId)->get();

        if ($items->count() > 0) {
            return response()->json([
                'status' => 200,
                'items' => $items,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No items found for this brand!',
            ], 404);
        }
    }

    public function getSimilarItems($id)
    {
        $currentItem = Items::find($id);

        if (!$currentItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Item not found',
            ], 404);
        }

        $similarItems = Items::where('categories_id', $currentItem->categories_id)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();

        if ($similarItems->count() > 0) {
            return response()->json([
                'status' => 200,
                'items' => $similarItems,
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No similar items found',
            ], 404);
        }
    }

    public function updateInventory(Request $request, int $id)
    {
        $item = Items::find($id);

        if ($item) {
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric',
                'amount' => 'required|integer',
                'vat' => 'required|numeric|in:0,5,12,21'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Bad Request data wrong!',
                    'errors' => $validator->messages(),
                ], 422);
            } else {
                $item->update([
                    'price' => $request->price,
                    'amount' => $request->amount,
                    'vat' => $request->vat
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Data updated successfully!',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No listing found',
            ], 404);
        }
    }


    public function updateItem(Request $request, $id)
    {
        $item = Items::find($id);

        if ($item) {
            $item->sold = $request->input('sold', $item->sold);
            $item->reserved = $request->input('reserved', $item->reserved);
            $item->amount = $request->input('amount', $item->amount);
            $item->save();

            return response()->json(['status' => 'success', 'message' => 'Item updated successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }
    }
}