<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Stock;
use App\Models\MovimientoStock;
use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $productos = Producto::with('stock')
            ->where('estado', 1)
            ->get();

        return view('stock.index', compact('productos'));
    }

    public function create()
    {
        $productos = Producto::where('estado', 1)->get();

        $tipos = TipoMovimiento::all();

        return view('stock.create', compact('productos', 'tipos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo_movimiento_id' => 'required|exists:tipos_movimiento,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|max:255',
        ]);

        DB::transaction(function () use ($datos) {

            $stock = Stock::firstOrCreate(
                ['producto_id' => $datos['producto_id']],
                ['cantidad' => 0]
            );

            $tipo = TipoMovimiento::findOrFail(
                $datos['tipo_movimiento_id']
            );

            if ($tipo->nombre === 'Entrada') {

                $stock->cantidad += $datos['cantidad'];

            } elseif ($tipo->nombre === 'Salida') {

                if ($stock->cantidad < $datos['cantidad']) {
                    abort(422, 'No hay suficiente stock disponible.');
                }

                $stock->cantidad -= $datos['cantidad'];

            } elseif ($tipo->nombre === 'Ajuste') {

                $stock->cantidad = $datos['cantidad'];
            }

            $stock->save();

            MovimientoStock::create([
                'producto_id' => $datos['producto_id'],
                'tipo_movimiento_id' => $datos['tipo_movimiento_id'],
                'created_by' => Auth::id(),
                'cantidad' => $datos['cantidad'],
                'motivo' => $datos['motivo'] ?? null,
                'created_at' => now(),
            ]);
        });

        return redirect('/stock')
            ->with('success', 'Movimiento registrado correctamente.');
    }
}