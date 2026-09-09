<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;

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

    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas/create', [VentaController::class, 'create']);
    Route::post('/ventas', [VentaController::class, 'store']);

    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::get('/pedidos/create', [PedidoController::class, 'create']);
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    Route::put('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);
    Route::put('/pedidos/{id}/pago', [PedidoController::class, 'registrarPago']);
    
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/create', [ClienteController::class, 'create']);
    Route::post('/clientes', [ClienteController::class, 'store']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');