<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\EstadoPedido;
use App\Models\DetallePedido;
use App\Models\DetalleTortaPersonalizada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Mostrar todos los pedidos.
     */
    public function index()
    {
        $pedidos = Pedido::with([
            'cliente',
            'estadoPedido',
            'detalles.producto',
            'tortasPersonalizadas'
        ])
        ->orderBy('id', 'desc')
        ->get();

        return view('pedidos.index', compact('pedidos'));
    }


    /**
     * Mostrar formulario para registrar un pedido.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombres')->get();

        /*
         * IMPORTANTE:
         * Los pedidos NO dependen del stock.
         * Por eso no filtramos por cantidad disponible.
         */
        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $estados = EstadoPedido::orderBy('id')->get();

        return view('pedidos.create', compact(
            'clientes',
            'productos',
            'estados'
        ));
    }


    /**
     * Registrar un nuevo pedido.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_entrega' => 'required|date',
            'anticipo' => 'required|numeric|min:0',

            'productos' => 'nullable|array',

            'torta.porciones' => 'nullable|integer|min:1',
            'torta.sabor' => 'nullable|string|max:100',
            'torta.relleno' => 'nullable|string|max:100',
            'torta.cobertura' => 'nullable|string|max:100',
            'torta.decoracion' => 'nullable|string|max:255',
            'torta.mensaje' => 'nullable|string|max:255',
            'torta.observaciones' => 'nullable|string',
            'torta.precio' => 'nullable|numeric|min:0',
        ]);


        try {

            DB::transaction(function () use ($request) {

                $total = 0;

                $detalles = [];


                /*
                |--------------------------------------------------------------------------
                | PRODUCTOS DEL PEDIDO
                |--------------------------------------------------------------------------
                |
                | IMPORTANTE:
                | Aquí NO revisamos stock.
                |
                | Un cliente puede pedir 5 productos aunque actualmente
                | solamente existan 2 disponibles en stock.
                |
                */

                if ($request->has('productos')) {

                    foreach ($request->productos as $productoId => $cantidad) {

                        $cantidad = (int) $cantidad;

                        if ($cantidad <= 0) {
                            continue;
                        }

                        $producto = Producto::findOrFail($productoId);

                        $precio = (float) $producto->precio;

                        $subtotal = $precio * $cantidad;

                        $total += $subtotal;


                        $detalles[] = [
                            'producto_id' => $producto->id,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $subtotal,
                        ];
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | TORTA PERSONALIZADA
                |--------------------------------------------------------------------------
                */

                $torta = $request->input('torta');

                $tieneTorta = $torta &&
                    !empty($torta['sabor']) &&
                    isset($torta['precio']) &&
                    (float) $torta['precio'] > 0;


                if ($tieneTorta) {

                    $total += (float) $torta['precio'];
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDAR QUE EXISTA AL MENOS UN PRODUCTO
                | O UNA TORTA PERSONALIZADA
                |--------------------------------------------------------------------------
                */

                if ($total <= 0) {

                    throw new \Exception(
                        'Debe agregar al menos un producto o una torta personalizada.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ANTICIPO
                |--------------------------------------------------------------------------
                */

                $anticipo = (float) $request->anticipo;


                if ($anticipo > $total) {

                    throw new \Exception(
                        'El anticipo no puede ser mayor que el total del pedido.'
                    );
                }


                $saldo = $total - $anticipo;


                /*
                |--------------------------------------------------------------------------
                | CREAR PEDIDO
                |--------------------------------------------------------------------------
                */

                $pedido = Pedido::create([

                    'cliente_id' => $request->cliente_id,

                    'created_by' => auth()->id() ?? 1,

                    'updated_by' => null,

                    /*
                     * Estado 1 = Pendiente
                     */
                    'estado_pedido_id' => 1,

                    'fecha_pedido' => now(),

                    'fecha_entrega' => $request->fecha_entrega,

                    'anticipo' => $anticipo,

                    'pago_final' => 0,

                    'saldo' => $saldo,

                    'total' => $total,

                    'observaciones' => $request->observaciones,
                ]);


                /*
                |--------------------------------------------------------------------------
                | GUARDAR DETALLES DE PRODUCTOS
                |--------------------------------------------------------------------------
                */

                foreach ($detalles as $detalle) {

                    DetallePedido::create([

                        'pedido_id' => $pedido->id,

                        'producto_id' => $detalle['producto_id'],

                        'cantidad' => $detalle['cantidad'],

                        'precio_unitario' =>
                            $detalle['precio_unitario'],

                        'subtotal' =>
                            $detalle['subtotal'],
                    ]);

                    /*
                     * NO SE DESCUENTA STOCK.
                     *
                     * El producto será elaborado para el pedido.
                     */
                }


                /*
                |--------------------------------------------------------------------------
                | GUARDAR TORTA PERSONALIZADA
                |--------------------------------------------------------------------------
                */

                if ($tieneTorta) {

                    DetalleTortaPersonalizada::create([

                        'pedido_id' => $pedido->id,

                        'porciones' =>
                            $torta['porciones'] ?? 1,

                        'sabor' =>
                            $torta['sabor'],

                        'relleno' =>
                            $torta['relleno'] ?? null,

                        'cobertura' =>
                            $torta['cobertura'] ?? null,

                        'decoracion' =>
                            $torta['decoracion'] ?? null,

                        'mensaje' =>
                            $torta['mensaje'] ?? null,

                        'observaciones' =>
                            $torta['observaciones'] ?? null,

                        'precio' =>
                            $torta['precio'],
                    ]);
                }
            });


        } catch (\Exception $e) {

            return back()
                ->withErrors([
                    'pedido' => $e->getMessage()
                ])
                ->withInput();
        }


        return redirect('/pedidos')
            ->with(
                'success',
                'Pedido registrado correctamente.'
            );
    }
}