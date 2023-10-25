<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:40|unique:users',
            'password' => 'required|string|max:255',
            'password_repeat' => 'required|string|max:255|same:password',
            'name' => 'required|string',
            'surname' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation error',
                'errors' => $validator->messages(),
            ], 422);
        }

        $user = new User;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User registered successfully!',
        ], 200);
    }
}
