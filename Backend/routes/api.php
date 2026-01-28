<?php

use App\Http\Controllers\Auth\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json(['message' => 'Server is running']);
});

// Auth routes
Route::prefix('auth')->group(function () {

    // User Signup
    Route::middleware(['auth:sanctum', 'admin'])->group(function (){
        Route::post('signup', action: [UserAuthController::class, 'signup']);
    });

    // User Login
    Route::post('login', action: [UserAuthController::class, 'login']);


    // Email Verification Route
    // Route::get('email/verify/{id}/{hash}', [UserAuthController::class, 'verifyEmail'])
    //     // ->middleware(['signed'])
    //     ->name('verification.verify');

    // Resend Verification Email
    // Route::post('/email/resend', [UserAuthController::class, 'resendVerificationEmail'])
    // ->middleware(['auth:sanctum', 'throttle:6,1']);
});
