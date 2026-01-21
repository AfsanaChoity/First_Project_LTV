<?php

use App\Http\Controllers\Auth\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function(){
    return response()->json(['message'=> 'Server is running']);
});

// Auth routes
Route::prefix('auth')->group(function (){

    // User Signup
    Route::post('signup', action: [UserAuthController::class, 'signup']);

    // User Login
    Route::post('login', action: [UserAuthController::class, 'login'] );



    // Email Verification
    Route::get('email/verify/{id}/{hash}', [UserAuthController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify'); 
});
