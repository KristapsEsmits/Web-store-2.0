<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|regex:/^[A-Za-z]+$/',
            'surname' => 'required|regex:/^[A-Za-z]+$/',
            'password' => 'required|string|max:255|confirmed',
            'phone' => 'required|numeric|digits_between:4,16|unique:users',
            'email' => 'required|email|unique:users',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'User registered successfully!',
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully!',
                'access_token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);

        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function user()
    {
        return auth()->user();
    }
}
