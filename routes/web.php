<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Serve API documentation
Route::get('/docs', function() {
    return file_get_contents(public_path('docs/index.html'));
});