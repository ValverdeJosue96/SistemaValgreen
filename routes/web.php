<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'mostrarLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {

    Route::get('/productos', [ProductoController::class, 'index']);

    Route::get('/productos/create', [ProductoController::class, 'create']);

    Route::post('/productos', [ProductoController::class, 'store']);

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');