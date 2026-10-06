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
        /*
         * Cargamos los clientes para que el formulario
         * pueda buscar rápidamente por carnet.
         */
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
        /*
         * Obtenemos el carnet enviado.
         */
        $carnet = trim(
            (string) $request->input('cliente.carnet')
        );

        /*
         * Primero comprobamos si el cliente ya existe.
         *
         * Esto nos permite saber qué campos son obligatorios:
         *
         * - Si existe: solamente necesitamos el carnet.
         * - Si no existe: necesitamos los datos para registrarlo.
         */
        $clienteExistente = null;

        if ($carnet !== '') {
            $clienteExistente = Cliente::where(
                'carnet',
                $carnet
            )->first();
        }


        /*
         * Reglas básicas del formulario.
         */
        $reglas = [

            'cliente.carnet' =>
                'required|string|max:20',

            'cliente.nombres' =>
                'nullable|string|max:100',

            'cliente.primer_apellido' =>
                'nullable|string|max:50',

            /*
             * El segundo apellido es OPCIONAL.
             */
            'cliente.segundo_apellido' =>
                'nullable|string|max:50',

            'cliente.telefono' =>
                'nullable|string|max:20',

            'fecha_entrega' =>
                'required|date',

            'anticipo' =>
                'required|numeric|min:0',

            'productos' =>
                'nullable|array',

            'torta.porciones' =>
                'nullable|integer|min:1',

            'torta.sabor' =>
                'nullable|string|max:100',

            'torta.relleno' =>
                'nullable|string|max:100',

            'torta.cobertura' =>
                'nullable|string|max:100',

            'torta.decoracion' =>
                'nullable|string|max:255',

            'torta.mensaje' =>
                'nullable|string|max:255',

            'torta.observaciones' =>
                'nullable|string',

            'torta.precio' =>
                'nullable|numeric|min:0',
        ];


        /*
         * Si el cliente NO existe, los datos personales
         * son necesarios para poder registrarlo.
         *
         * Segundo apellido NO se agrega como required.
         */
        if (!$clienteExistente) {

            $reglas['cliente.nombres'] =
                'required|string|max:100';

            $reglas['cliente.primer_apellido'] =
                'required|string|max:50';

            $reglas['cliente.telefono'] =
                'required|string|max:20';
        }


        /*
         * Validar formulario.
         */
        $request->validate($reglas);


        try {

            DB::transaction(function () use (
                $request,
                $carnet
            ) {

                /*
                 * ==========================================================
                 * CLIENTE
                 * ==========================================================
                 *
                 * Volvemos a buscar el cliente dentro de la transacción.
                 *
                 * Si existe:
                 *     usamos el cliente existente.
                 *
                 * Si no existe:
                 *     creamos uno nuevo.
                 *
                 * De esta manera nunca creamos otro cliente
                 * simplemente por registrar otro pedido.
                 */

                $cliente = Cliente::where(
                    'carnet',
                    $carnet
                )->first();


                if (!$cliente) {

                    $cliente = Cliente::create([

                        'nombres' =>
                            $request->input(
                                'cliente.nombres'
                            ),

                        'primer_apellido' =>
                            $request->input(
                                'cliente.primer_apellido'
                            ),

                        /*
                         * Puede ser NULL.
                         */
                        'segundo_apellido' =>
                            $request->input(
                                'cliente.segundo_apellido'
                            ) ?: null,

                        'carnet' =>
                            $carnet,

                        'telefono' =>
                            $request->input(
                                'cliente.telefono'
                            ),
                    ]);
                }


                /*
                 * ==========================================================
                 * PRODUCTOS DEL PEDIDO
                 * ==========================================================
                 *
                 * IMPORTANTE:
                 * Aquí NO revisamos stock.
                 *
                 * Un cliente puede pedir 5 productos aunque actualmente
                 * solamente existan 2 disponibles en stock.
                 *
                 * El pedido representa productos que serán preparados.
                 */

                $total = 0;

                $detalles = [];


                if ($request->has('productos')) {

                    foreach (
                        $request->productos
                        as $productoId => $cantidad
                    ) {

                        $cantidad = (int) $cantidad;


                        if ($cantidad <= 0) {
                            continue;
                        }


                        $producto = Producto::findOrFail(
                            $productoId
                        );


                        $precio = (float) $producto->precio;

                        $subtotal = $precio * $cantidad;

                        $total += $subtotal;


                        $detalles[] = [

                            'producto_id' =>
                                $producto->id,

                            'cantidad' =>
                                $cantidad,

                            'precio_unitario' =>
                                $precio,

                            'subtotal' =>
                                $subtotal,
                        ];
                    }
                }


                /*
                 * ==========================================================
                 * TORTA PERSONALIZADA
                 * ==========================================================
                 */

                $torta = $request->input('torta');


                $tieneTorta =
                    $torta &&
                    !empty($torta['sabor']) &&
                    isset($torta['precio']) &&
                    (float) $torta['precio'] > 0;


                if ($tieneTorta) {

                    $total += (float) $torta['precio'];
                }


                /*
                 * ==========================================================
                 * VALIDAR QUE EXISTAN PRODUCTOS O TORTA
                 * ==========================================================
                 */

                if ($total <= 0) {

                    throw new \Exception(
                        'Debe agregar al menos un producto o una torta personalizada.'
                    );
                }


                /*
                 * ==========================================================
                 * ANTICIPO
                 * ==========================================================
                 */

                $anticipo =
                    (float) $request->anticipo;


                if ($anticipo > $total) {

                    throw new \Exception(
                        'El anticipo no puede ser mayor que el total del pedido.'
                    );
                }


                $saldo =
                    $total - $anticipo;


                /*
                 * ==========================================================
                 * CREAR PEDIDO
                 * ==========================================================
                 */

                $pedido = Pedido::create([

                    /*
                     * Ahora utilizamos el cliente encontrado
                     * o recién creado.
                     */
                    'cliente_id' =>
                        $cliente->id,

                    'created_by' =>
                        auth()->id() ?? 1,

                    'updated_by' =>
                        null,

                    /*
                     * Estado 1 = Pendiente
                     */
                    'estado_pedido_id' =>
                        1,

                    'fecha_pedido' =>
                        now(),

                    'fecha_entrega' =>
                        $request->fecha_entrega,

                    'anticipo' =>
                        $anticipo,

                    'pago_final' =>
                        0,

                    'saldo' =>
                        $saldo,

                    'total' =>
                        $total,

                    'observaciones' =>
                        $request->observaciones,
                ]);


                /*
                 * ==========================================================
                 * GUARDAR DETALLES DE PRODUCTOS
                 * ==========================================================
                 */

                foreach ($detalles as $detalle) {

                    DetallePedido::create([

                        'pedido_id' =>
                            $pedido->id,

                        'producto_id' =>
                            $detalle['producto_id'],

                        'cantidad' =>
                            $detalle['cantidad'],

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
                 * ==========================================================
                 * GUARDAR TORTA PERSONALIZADA
                 * ==========================================================
                 */

                if ($tieneTorta) {

                    DetalleTortaPersonalizada::create([

                        'pedido_id' =>
                            $pedido->id,

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


    /**
     * Mostrar el detalle de un pedido.
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'cliente',
            'estadoPedido',
            'detalles.producto',
            'tortasPersonalizadas'
        ])->findOrFail($id);


        $estados = EstadoPedido::orderBy('id')->get();


        return view('pedidos.show', compact(
            'pedido',
            'estados'
        ));
    }


    /**
     * Actualizar el estado de un pedido.
     */
    public function actualizarEstado(
        Request $request,
        $id
    ) {

        $request->validate([
            'estado_pedido_id' =>
                'required|exists:estados_pedido,id'
        ]);


        $pedido = Pedido::findOrFail($id);


        $pedido->update([

            'estado_pedido_id' =>
                $request->estado_pedido_id,

            'updated_by' =>
                auth()->id() ?? 1
        ]);


        return redirect(
            '/pedidos/' . $pedido->id
        )->with(
            'success',
            'Estado del pedido actualizado correctamente.'
        );
    }


    /**
     * Registrar el pago final del pedido.
     */
    public function registrarPago(
        Request $request,
        $id
    ) {

        $request->validate([
            'pago' =>
                'required|numeric|min:0.01'
        ]);


        $pedido = Pedido::findOrFail($id);


        $pago =
            (float) $request->pago;


        if ($pago > $pedido->saldo) {

            return back()->withErrors([
                'pago' =>
                    'El pago no puede ser mayor al saldo pendiente.'
            ]);
        }


        $nuevoPagoFinal =
            $pedido->pago_final + $pago;


        $nuevoSaldo =
            $pedido->saldo - $pago;


        $pedido->update([

            'pago_final' =>
                $nuevoPagoFinal,

            'saldo' =>
                $nuevoSaldo,

            'updated_by' =>
                auth()->id() ?? 1
        ]);


        return redirect(
            '/pedidos/' . $pedido->id
        )->with(
            'success',
            'Pago registrado correctamente.'
        );
    }
}