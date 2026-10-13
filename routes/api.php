<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\AuthApiController;

// Productos: se conserva la ruta que ya funciona.
Route::get('/productos', [ProductoApiController::class, 'index']);

// Inicio de sesión móvil.
Route::post('/login', [AuthApiController::class, 'login']);

// Rutas que requieren autenticación.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
});