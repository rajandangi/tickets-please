<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class)->middleware('auth:sanctum');
    Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
});
