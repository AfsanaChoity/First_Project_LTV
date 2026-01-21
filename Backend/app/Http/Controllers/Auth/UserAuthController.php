<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class UserAuthController extends Controller
{
    // Singup for users
    public function signup(SignupRequest $request)
    {

        $inputs = $request->validated();
        $inputs['password'] = Hash::make($inputs['password']);

        $user = User::create($inputs);

        // Dispatch the Registered event to send email verification
        event(new Registered($user));

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Account created successfully. Please verify your email address.',
        ], 201);

    }

    // Verify email
    public function verifyEmail(EmailVerificationRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->fulfill();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
        ], 200);
    }

    // Login for users
    public function login(LoginRequest $request) {
        $inputs = $request->validated();

        $user = app()->make(AuthService::class)->login(
            $inputs['email'], 
            $inputs['password']
            );

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user['user'],
                'token' => $user['token'],
                'token_type'   => 'Bearer',

            ],
            'message' => 'Login successful.',
        ], 200);
    }

}
