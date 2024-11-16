<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\AuthorTicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class)->middleware('auth:sanctum');
    Route::apiResource('authors', AuthorController::class)->middleware('auth:sanctum');
    Route::apiResource('authors.tickets', AuthorTicketController::class)->middleware('auth:sanctum');
});
