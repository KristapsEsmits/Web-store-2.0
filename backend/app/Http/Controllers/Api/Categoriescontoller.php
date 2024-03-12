<?php

namespace App\Http\Controllers\Api;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoriesContoller extends Controller
{
    public function index()
    {
        $categories = Categories::all();
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
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:40',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request!',
                'errors' => $validator->messages(),
            ], 422);
        } else {
            $categories = Categories::create([
                'category_name' => $request->category_name,
            ]);

            if ($categories) {
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
        $categories = Categories::find($id);
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

    public function edit($id)
    {
        $categories = Categories::find($id);
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

    public function update(Request $request, int $id)
    {
        $categories = Categories::find($id);
        if ($categories) {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:40',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Bad Request data wrong!',
                    'errors' => $validator->messages(),
                ], 422);
            } else {
                $categories->update([
                    'category_name' => $request->category_name,
                ]);

                if ($categories) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Data updated successfully!',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No listing found!',
                    ], 404);
                }
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
        $categories = Categories::find($id);
        if ($categories) {
            $categories->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully!',
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No listing found',
            ], 404);
        }
    }
}