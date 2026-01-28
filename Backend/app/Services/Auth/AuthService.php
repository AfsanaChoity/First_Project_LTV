<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Handle user login.
     *
     * @param  array  $credentials
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    
    public function login(string $email, $password): array{
        $user = User::where('email', $email)->first();

         if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        // Email verification check 
        // if (!$user->hasVerifiedEmail()) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Please verify your email first.'],
        //     ]);
        // }

        $token = $user->createToken('API Token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}