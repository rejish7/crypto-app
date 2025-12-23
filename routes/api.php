<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
Route::get('/profile',[ProfileController::class,'index']);
Route::get('/orders',[OrderController::class,'index']);
Route::post('/orders',[OrderController::class,'store']);
Route::post('/orders/{order}/cancel',[OrderController::class,'cancel']);
});
