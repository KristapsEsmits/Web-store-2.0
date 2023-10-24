<?php

namespace App\Http\Controllers\Api;

use App\Models\Test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function index()
    {
        $test = Test::all();
        if ($test->count() > 0) {
            return response()->json([
                'status' => 200,
                'test' => $test,
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
            'desc' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request!',
                'errors' => $validator->messages(),
            ], 422);
        } else {
            $test = Test::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'price' => $request->price,
            ]);

            if ($test) {
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
        $test = Test::find($id);
        if ($test) {
            return response()->json([
                'status' => 200,
                'test' => $test,
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
        $test = Test::find($id);
        if ($test) {
            return response()->json([
                'status' => 200,
                'test' => $test,
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
        $test = Test::find($id);
        if ($test) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:40',
                'desc' => 'required|string|max:255',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Bad Request data wrong!',
                    'errors' => $validator->messages(),
                ], 422);
            } else {
                $test->update([
                    'name' => $request->name,
                    'desc' => $request->desc,
                    'price' => $request->price,
                ]);

                if ($test) {
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
        $test = Test::find($id);
        if ($test) {
            $test->delete();
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