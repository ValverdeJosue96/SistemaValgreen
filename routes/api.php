<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PedidoApiController;

Route::get('/productos', [ProductoApiController::class, 'index']);

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);

    Route::get('/pedidos', [PedidoApiController::class, 'index']);
    Route::post('/pedidos', [PedidoApiController::class, 'store']);
    Route::get('/pedidos/{id}', [PedidoApiController::class, 'show']);
});
