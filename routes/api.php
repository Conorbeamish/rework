<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//TODO add some real auth, this will just default to true
Route::middleware('can:viewAny,App\Models\Product')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

Route::middleware('can:create,App\Models\Order')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});
