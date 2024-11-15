<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// https://tickets-please.test/api/
// tickets
// users


Route::post('/login', [AuthController::class,'login'])->name('login');
Route::post('/register', [AuthController::class,'register']);

// Only authenticated users can access this route
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
