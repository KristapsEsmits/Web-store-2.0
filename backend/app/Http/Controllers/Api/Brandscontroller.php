<?php

namespace App\Http\Controllers\Api;

use App\Models\Brands;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            $randomString = Str::random(10);
            $imgPath = $request->file('img')->storeAs('uploads', $randomString . '.' . $request->file('img')->getClientOriginalExtension(), 'public');

            $brands = Brands::create([
                'name' => $request->name,
                'img' => $randomString . '.' . $request->file('img')->getClientOriginalExtension(),
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
                'img' => $request->hasFile('img') ? 'image|mimes:jpeg,png,jpg,gif,svg' : '',
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

                    // Delete old image if it exists
                    if ($brands->img) {
                        $oldImagePath = public_path("/storage/uploads/{$brands->img}");
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $brands->update([
                        'name' => $request->name,
                        'img' => $randomString . '.' . $request->file('img')->getClientOriginalExtension(),
                    ]);
                } else {
                    // No new image is being uploaded
                    $brands->update([
                        'name' => $request->name,
                    ]);
                }

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

    // Delete brand and its image
    public function destroy($id)
    {
        $brand = Brands::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found',
            ], 404);
        }

        $imagePath = public_path("/storage/uploads/{$brand->img}");

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Brand deleted successfully',
        ], 200);
    }

}