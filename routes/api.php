<?php

use App\Http\Controllers\AuthController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// https://tickets-please.test/api/
// tickets
// users

Route::post('/check', function (){
   return response()->json(['message' => 'API is working'], 200);
});

Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class,'register']);


Route::get('/tickets', function (){
    return Ticket::all();
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
