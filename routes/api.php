<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'createOrder']);
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{order_number}', [OrderController::class, 'show']);
});

Route::get('/', [OrderController::class, 'externalApi'])->name('order.externalApi');
