<?php

namespace App\Http\Controllers\Api;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\SpecificationTitle;

class CategoriesContoller extends Controller
{
    public function index()
    {
        $categories = Categories::withCount('items')->get();
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
            'category_name' => 'required|string|max:40|unique:categories,category_name',
            'specification_titles' => 'sometimes|array',
            'specification_titles.*.title' => 'required_with:specification_titles|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request!',
                'errors' => $validator->messages(),
            ], 422);
        }

        if ($request->has('specification_titles')) {
            $titles = array_column($request->specification_titles, 'title');
            if (count($titles) !== count(array_unique($titles))) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Each specification title must be unique within a category.',
                    'errors' => ['specification_titles' => ['Each specification title must be unique within a category.']],
                ], 422);
            }
        }

        $category = Categories::create([
            'category_name' => $request->category_name,
        ]);

        if ($request->has('specification_titles')) {
            foreach ($request->specification_titles as $specTitle) {
                SpecificationTitle::create([
                    'category_id' => $category->id,
                    'specification_title' => $specTitle['title'],
                ]);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully!',
        ], 200);
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

    public function getCategories()
    {
        $categories = Categories::all();
        return response()->json(['categories' => $categories], 200);
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