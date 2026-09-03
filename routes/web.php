<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'mostrarLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {

    // Productos
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/productos/create', [ProductoController::class, 'create']);
    Route::post('/productos', [ProductoController::class, 'store']);

    // Stock
    Route::get('/stock', [StockController::class, 'index']);
    Route::get('/stock/create', [StockController::class, 'create']);
    Route::post('/stock', [StockController::class, 'store']);

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');