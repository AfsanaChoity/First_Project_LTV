<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    // Singup for users
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',

        ]);

        $inputs = $request->all();
        $inputs['password'] = Hash::make($inputs['password']);

        $user = User::create($inputs);

        // Dispatch the Registered event to send email verification
        event(new Registered($user));

        $success['token'] = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $success['token'],
            'message' => 'User created successfully. Please check your email to verify your account.'
        ], 201);

    }
}