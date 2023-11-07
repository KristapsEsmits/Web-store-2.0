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
            'name' => 'required|regex:/^[A-Za-z]+$/',
            'surname' => 'required|regex:/^[A-Za-z]+$/',
            'password' => 'required|string|max:255',
            'password_confirmation' => 'required|string|max:255|same:password',
            'phone' => 'required|numeric|digits_between:4,16|unique:users',
            'email' => 'required|email|unique:users',
        ], [
            'name.required' => 'The name field is required.',
            'name.regex' => 'The name field must contain only letters.',
            'surname.required' => 'The surname field is required.',
            'surname.regex' => 'The surname field must contain only letters.',
            'password.required' => 'The password field is required.',
            'password.max' => 'The password may not be greater than :max characters.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'password_confirmation.max' => 'The password confirmation may not be greater than :max characters.',
            'password_confirmation.same' => 'The password and password confirmation must match.',
            'phone.required' => 'The phone field is required.',
            'phone.numeric' => 'The phone field must be a number.',
            'phone.max' => 'The phone may not be greater than :max digits.',
            'phone.unique' => 'The phone number is already taken.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email is already taken.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation error',
                'errors' => $validator->messages(),
            ], 422);
        }

        $user = new User;
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->password = bcrypt($request->password);
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User registered successfully!',
        ], 200);
    }
}
