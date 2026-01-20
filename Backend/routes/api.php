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

Route::post('signup', [UserAuthController::class, 'signup']);

// Include Auth routes
// require __DIR__.'/api/v1/authApi.php';