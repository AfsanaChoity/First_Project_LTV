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
use Illuminate\Auth\Events\Verified;


class UserAuthController extends Controller
{
    // Singup for users
    public function signup(SignupRequest $request)
    {

        $inputs = $request->validated();
        $inputs['password'] = Hash::make($inputs['password']);

        $user = User::create($inputs);

        // Dispatch the Registered event to send email verification
        // event(new Registered($user));

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Account created successfully.',
        ], 201);

    }



    // Verify email (API-friendly: verify by id & hash without requiring auth)
    public function verifyEmail(Request $request, $id, $hash)
    {
        // If signature is invalid or expired, redirect to frontend expired page
        if (! $request->hasValidSignature()) {
            return redirect()->away(config('app.frontend_url').'/email-verify?status=expired');
        }

        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification link.',
            ], 403);

            // return redirect()->away(
            //     config('app.frontend_url').'/email-verify?status=expired'
                
            // );
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->away(config('app.frontend_url').'/profile?verified=1');
    }

    // Resend verification email
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();
        // if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid or expired verification link.',
        //     ], 403);
        // }

        if( $user->hasVerifiedEmail()){
            return response()->json([
                'success' => false,
                'message' => 'Email already verified.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success'=> true,
            'message'=> 'Verification email has been sent again.',
        ]);
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
