<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;



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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:4,16',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Checks if email or phone number has changed
        if ($request->email !== $user->email || $request->phone !== $user->phone) {
            // Checks if the new email or phone number is already used by other users
            if (
                User::where('email', $request->email)->where('id', '!=', $user->id)->exists() ||
                User::where('phone', $request->phone)->where('id', '!=', $user->id)->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Email or phone number is already in use by other users.',
                ], 422);
            }
        }

        $user->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'User updated successfully!',
        ], 200);
    }


    public function changePassword(Request $request, int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|max:255|confirmed', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Bad Request data wrong!',
                'errors' => $validator->messages(),
            ], 422);
        }

        // Check if the provided current password matches the stored password
        if (password_verify($request->current_password, $user->password)) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Password updated successfully!',
            ], 200);
        } else {
            return response()->json([
                'status' => 422,
                'message' => 'Current password is incorrect!',
            ], 422);
        }
    }
}
