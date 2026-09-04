<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('usuario')
            ->orderBy('id', 'desc')
            ->get();

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $productos = Producto::where('estado', 1)
            ->with('stock')
            ->get();

        return view('ventas.create', compact('productos'));
    }

    public function store(Request $request)
{
    $request->validate([
        'productos' => 'required|array',
    ]);

    $productosSeleccionados = collect($request->productos)
        ->filter(function ($cantidad) {
            return (int) $cantidad > 0;
        });

    if ($productosSeleccionados->isEmpty()) {
        return back()
            ->withErrors([
                'productos' => 'Debe seleccionar al menos un producto.'
            ])
            ->withInput();
    }

    try {

        DB::transaction(function () use ($productosSeleccionados) {

            $total = 0;
            $detalles = [];

            foreach ($productosSeleccionados as $productoId => $cantidad) {

                $cantidad = (int) $cantidad;

                $producto = Producto::with('stock')
                    ->lockForUpdate()
                    ->findOrFail($productoId);

                $stockActual = $producto->stock->cantidad ?? 0;

                if ($cantidad > $stockActual) {

                    throw new \Exception(
                        "No hay suficiente stock para: {$producto->nombre}"
                    );
                }

                $precio = $producto->precio;

                $subtotal = $precio * $cantidad;

                $total += $subtotal;

                $detalles[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal,
                ];
            }

            $venta = Venta::create([
                'usuario_id' => auth()->id() ?? 1,
                'fecha' => now(),
                'total' => $total,
            ]);

            foreach ($detalles as $detalle) {

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle['producto']->id,
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal'],
                ]);

                $detalle['producto']->stock->decrement(
                    'cantidad',
                    $detalle['cantidad']
                );
            }

        });

    } catch (\Exception $e) {

        return back()
            ->withErrors([
                'venta' => $e->getMessage()
            ])
            ->withInput();
    }

    return redirect('/ventas')
        ->with('success', 'Venta registrada correctamente.');
}
}