<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// https://tickets-please.test/api/
// tickets
// users


Route::post('/login', [AuthController::class,'login'])->name('login');
Route::post('/register', [AuthController::class,'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
