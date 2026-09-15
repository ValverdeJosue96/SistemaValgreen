<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\User;
use App\Models\Venta;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if ($usuario->rol && $usuario->rol->nombre === 'Administrador') {

            // Ventas realizadas hoy
            $ventasHoy = Venta::whereDate('fecha', today())
                ->sum('total');


            // Pedidos activos
            // 4 = Entregado
            // 5 = Cancelado
            $pedidosActivos = Pedido::whereNotIn(
                'estado_pedido_id',
                [4, 5]
            )->count();


            // Usuarios activos
            $usuariosActivos = User::where('estado', 1)
                ->count();


            // Productos con stock bajo
            // Consideramos stock bajo cuando es 5 o menos
            $stockBajo = Stock::where('cantidad', '<=', 5)
                ->count();


            // Pedidos cuya fecha de entrega es hoy
            $pedidosHoy = Pedido::with([
                'cliente',
                'estadoPedido'
            ])
            ->whereDate('fecha_entrega', today())
            ->orderBy('fecha_entrega')
            ->limit(5)
            ->get();


            // Productos con stock bajo
            $productosStockBajo = Stock::with('producto')
                ->where('cantidad', '<=', 5)
                ->orderBy('cantidad')
                ->limit(5)
                ->get();


            return view('dashboard.admin', compact(
                'ventasHoy',
                'pedidosActivos',
                'usuariosActivos',
                'stockBajo',
                'pedidosHoy',
                'productosStockBajo'
            ));
        }


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD VENDEDOR
        |--------------------------------------------------------------------------
        */

        // Ventas realizadas hoy
        $ventasHoy = Venta::whereDate('fecha', today())
            ->sum('total');


        // Pedidos pendientes
        // 1 = Pendiente
        // 2 = En preparación
        // 3 = Listo
        $pedidosPendientes = Pedido::whereIn(
            'estado_pedido_id',
            [1, 2, 3]
        )->count();


        // Pedidos para hoy
        $pedidosHoy = Pedido::with([
            'cliente',
            'estadoPedido'
        ])
        ->whereDate('fecha_entrega', today())
        ->orderBy('fecha_entrega')
        ->limit(5)
        ->get();


        return view('dashboard.vendedor', compact(
            'ventasHoy',
            'pedidosPendientes',
            'pedidosHoy'
        ));
    }
}