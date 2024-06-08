<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpecificationTitle;
use Illuminate\Http\Request;

class SpecificationTitleController extends Controller
{
    public function index()
    {
        $specificationTitles = SpecificationTitle::all();
        return response()->json([
            'status' => 200,
            'specification_titles' => $specificationTitles,
        ], 200);
    }

    public function show($id)
    {
        $specificationTitle = SpecificationTitle::findOrFail($id);
        return response()->json([
            'status' => 200,
            'specification_title' => $specificationTitle,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'specification_titles' => 'required|array',
            'specification_titles.*' => 'required|string',
        ]);

        try {
            foreach ($request->specification_titles as $title) {
                SpecificationTitle::create([
                    'category_id' => $request->category_id,
                    'specification_title' => $title,
                ]);
            }

            return response()->json(['message' => 'Specification Titles added successfully'], 201);
        } catch (\Exception $e) {
            \Log::error('Error adding specification titles: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to add specification titles'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'specification_title' => 'required|string|max:255',
            ]);

            $specificationTitle = SpecificationTitle::findOrFail($id);
            $specificationTitle->update([
                'specification_title' => $request->specification_title,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Specification title updated successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating specification title: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $specificationTitle = SpecificationTitle::findOrFail($id);
            $specificationTitle->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Specification title deleted successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting specification title: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
