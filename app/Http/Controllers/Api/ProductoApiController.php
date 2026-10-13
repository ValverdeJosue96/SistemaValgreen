<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;

class ProductoApiController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::with('categoria')
            ->where('estado', 1)
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'imagen' => $producto->imagen,
                    'categoria' => $producto->categoria?->nombre,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $productos,
        ]);
    }
}