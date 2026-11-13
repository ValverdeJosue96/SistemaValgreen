<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Pedido;
use App\Models\EstadoPedido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Página principal de reportes.
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Reporte de ventas.
     */
    public function ventas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $consulta = Venta::with('usuario', 'detalles.producto');

        if ($request->filled('fecha_inicio')) {
            $consulta->whereDate('fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $consulta->whereDate('fecha', '<=', $request->fecha_fin);
        }

        $ventas = $consulta->orderBy('id', 'desc')->get();

        $totalVentas = $ventas->count();

        $ingresosTotales = $ventas->sum(function ($venta) {
            return (float) $venta->total;
        });

        return view('reportes.ventas', compact(
            'ventas',
            'totalVentas',
            'ingresosTotales'
        ));
    }

    /**
 * Descargar informe de ventas en PDF.
 */
public function ventasPdf(Request $request)
{
    $request->validate([
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
    ]);

    $consulta = Venta::with('usuario', 'detalles.producto');

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate('fecha', '>=', $request->fecha_inicio);
    }

    if ($request->filled('fecha_fin')) {
        $consulta->whereDate('fecha', '<=', $request->fecha_fin);
    }

    $ventas = $consulta->orderBy('id', 'desc')->get();

    $totalVentas = $ventas->count();

    $ingresosTotales = $ventas->sum(function ($venta) {
        return (float) $venta->total;
    });

    $pdf = Pdf::loadView('reportes.pdf.ventas', compact(
        'ventas',
        'totalVentas',
        'ingresosTotales'
    ));

    $pdf->setPaper('a4', 'portrait');

    return $pdf->download('informe_ventas_valgreen.pdf');
}
    

    /**
     * Reporte de pedidos.
     */
    public function pedidos(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado_pedido_id' => 'nullable|integer|exists:estados_pedido,id',
        ]);

        $consulta = Pedido::with([
            'cliente',
            'estadoPedido',
            'creador',
            'actualizador',
            'detalles.producto',
            'tortasPersonalizadas',
        ]);

        if ($request->filled('fecha_inicio')) {
            $consulta->whereDate('fecha_pedido', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $consulta->whereDate('fecha_pedido', '<=', $request->fecha_fin);
        }

        if ($request->filled('estado_pedido_id')) {
            $consulta->where(
                'estado_pedido_id',
                $request->estado_pedido_id
            );
        }

        $pedidos = $consulta->orderBy('id', 'desc')->get();

        // Resumen general de los pedidos filtrados
        $totalPedidos = $pedidos->count();

        $totalAnticipos = $pedidos->sum(function ($pedido) {
            return (float) $pedido->anticipo;
        });

        $totalPagosFinales = $pedidos->sum(function ($pedido) {
            return (float) $pedido->pago_final;
        });

        $totalSaldosPendientes = $pedidos->sum(function ($pedido) {
            return (float) $pedido->saldo;
        });

        $totalImportePedidos = $pedidos->sum(function ($pedido) {
            return (float) $pedido->total;
        });

        // Cantidad de pedidos agrupados por estado
        $pedidosPorEstado = $pedidos->groupBy(function ($pedido) {
            return $pedido->estadoPedido->nombre ?? 'Sin estado';
        })->map(function ($grupo) {
            return $grupo->count();
        });

        $estados = EstadoPedido::orderBy('id')->get();

        return view('reportes.pedidos', compact(
            'pedidos',
            'totalPedidos',
            'totalAnticipos',
            'totalPagosFinales',
            'totalSaldosPendientes',
            'totalImportePedidos',
            'pedidosPorEstado',
            'estados'
        ));
    }

    /**
 * Descargar informe de pedidos en PDF.
 */
public function pedidosPdf(Request $request)
{
    $request->validate([
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'estado_pedido_id' => 'nullable|integer|exists:estados_pedido,id',
    ]);

    $consulta = Pedido::with([
        'cliente',
        'estadoPedido',
        'creador',
        'actualizador',
        'detalles.producto',
        'tortasPersonalizadas',
    ]);

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate('fecha_pedido', '>=', $request->fecha_inicio);
    }

    if ($request->filled('fecha_fin')) {
        $consulta->whereDate('fecha_pedido', '<=', $request->fecha_fin);
    }

    if ($request->filled('estado_pedido_id')) {
        $consulta->where('estado_pedido_id', $request->estado_pedido_id);
    }

    $pedidos = $consulta->orderBy('id', 'desc')->get();

    $totalPedidos = $pedidos->count();

    $totalAnticipos = $pedidos->sum(fn($pedido) => (float) $pedido->anticipo);

    $totalPagosFinales = $pedidos->sum(fn($pedido) => (float) $pedido->pago_final);

    $totalSaldosPendientes = $pedidos->sum(fn($pedido) => (float) $pedido->saldo);

    $totalImportePedidos = $pedidos->sum(fn($pedido) => (float) $pedido->total);

    $pedidosPorEstado = $pedidos->groupBy(function ($pedido) {
        return $pedido->estadoPedido->nombre ?? 'Sin estado';
    })->map(fn($grupo) => $grupo->count());

    $estadoSeleccionado = $request->filled('estado_pedido_id')
        ? EstadoPedido::find($request->estado_pedido_id)
        : null;

    $pdf = Pdf::loadView('reportes.pdf.pedidos', compact(
        'pedidos',
        'totalPedidos',
        'totalAnticipos',
        'totalPagosFinales',
        'totalSaldosPendientes',
        'totalImportePedidos',
        'pedidosPorEstado',
        'estadoSeleccionado'
    ));

    $pdf->setPaper('a4', 'portrait');

    return $pdf->download('informe_pedidos_valgreen.pdf');
    }
}
