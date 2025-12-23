<?php

use Illuminate\Support\Facades\Route;

// Catch all routes and let Vue Router handle them
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
