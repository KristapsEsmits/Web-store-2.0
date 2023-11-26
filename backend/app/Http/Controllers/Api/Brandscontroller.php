<?php

namespace App\Http\Controllers\Api;

use App\Models\Brands;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brands::all();
        if ($brands->count() > 0) {
            return response()->json([
                'status' => 200,
                'brands' => $brands,
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
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request!',
                'errors' => $validator->messages(),
            ], 422);
        } else {

            $imgPath = $request->file('img')->store('uploads', 'public');

            $brands = Brands::create([
                'name' => $request->name,
                'img' => $imgPath,
            ]);

            if ($brands) {
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
        $brands = Brands::find($id);
        if ($brands) {
            return response()->json([
                'status' => 200,
                'brands' => $brands,
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
        $brands = Brands::find($id);
        if ($brands) {
            return response()->json([
                'status' => 200,
                'brands' => $brands,
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
        $brands = Brands::find($id);
        if ($brands) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:40',
                'img' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Bad Request data wrong!',
                    'errors' => $validator->messages(),
                ], 422);
            } else {
                $brands->update([
                    'name' => $request->name,
                    'img' => $request->img,
                ]);

                if ($brands) {
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
        $brands = Brands::find($id);
        if ($brands) {
            $brands->delete();
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