<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\EstadoPedido;
use App\Models\DetallePedido;
use App\Models\DetalleTortaPersonalizada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PedidoApiController extends Controller
{
    /**
     * Listar pedidos del sistema.
     */
    public function index()
    {
        $pedidos = Pedido::with([
            'cliente',
            'estadoPedido',
            'detalles.producto',
            'tortasPersonalizadas',
        ])->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'data' => $pedidos,
        ]);
    }

    /**
     * Consultar un pedido con sus detalles.
     */
    public function show($id)
    {
        $pedido = Pedido::with([
            'cliente',
            'estadoPedido',
            'detalles.producto',
            'tortasPersonalizadas',
        ])->find($id);

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'El pedido no existe.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pedido,
        ]);
    }

    /**
     * Registrar un pedido desde la aplicación móvil.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente.carnet' => 'required|string|max:20',
            'cliente.nombres' => 'nullable|string|max:100',
            'cliente.primer_apellido' => 'nullable|string|max:50',
            'cliente.segundo_apellido' => 'nullable|string|max:50',
            'cliente.telefono' => 'nullable|string|max:20',

            'fecha_entrega' => 'required|date',
            'anticipo' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',

            'productos' => 'nullable|array',
            'productos.*' => 'required|integer|min:0',

            'torta' => 'nullable|array',
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
            $pedido = DB::transaction(function () use ($request) {
                $datosCliente = $request->input('cliente');
                $carnet = trim($datosCliente['carnet']);

                $cliente = Cliente::where('carnet', $carnet)->first();

                if (!$cliente) {
                    $datosNuevos = validator($datosCliente, [
                        'nombres' => 'required|string|max:100',
                        'primer_apellido' => 'required|string|max:50',
                        'segundo_apellido' => 'nullable|string|max:50',
                        'telefono' => 'required|string|max:20',
                    ])->validate();

                    $cliente = Cliente::create([
                        'nombres' => $datosNuevos['nombres'],
                        'primer_apellido' => $datosNuevos['primer_apellido'],
                        'segundo_apellido' => $datosNuevos['segundo_apellido'] ?? null,
                        'carnet' => $carnet,
                        'telefono' => $datosNuevos['telefono'],
                    ]);
                }

                $total = 0;
                $detalles = [];

                foreach ($request->input('productos', []) as $productoId => $cantidad) {
                    $cantidad = (int) $cantidad;

                    if ($cantidad <= 0) {
                        continue;
                    }

                    $producto = Producto::where('estado', 1)
                        ->findOrFail($productoId);

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

                $torta = $request->input('torta', []);

                $tieneTorta = !empty($torta['sabor'])
                    && isset($torta['precio'])
                    && (float) $torta['precio'] > 0;

                if ($tieneTorta) {
                    $total += (float) $torta['precio'];
                }

                if ($total <= 0) {
                    throw ValidationException::withMessages([
                        'productos' => [
                            'Agrega al menos un producto o una torta personalizada.',
                        ],
                    ]);
                }

                $anticipo = (float) $request->anticipo;

                if ($anticipo > $total) {
                    throw ValidationException::withMessages([
                        'anticipo' => [
                            'El anticipo no puede ser mayor que el total.',
                        ],
                    ]);
                }

                $estadoPendiente = EstadoPedido::where(
                    'nombre',
                    'Pendiente'
                )->first();

                if (!$estadoPendiente) {
                    throw new \RuntimeException(
                        'No se encontró el estado de pedido Pendiente.'
                    );
                }

                $pedido = Pedido::create([
                    'cliente_id' => $cliente->id,
                    'created_by' => $request->user()->id,
                    'updated_by' => null,
                    'estado_pedido_id' => $estadoPendiente->id,
                    'fecha_pedido' => now(),
                    'fecha_entrega' => $request->fecha_entrega,
                    'anticipo' => $anticipo,
                    'pago_final' => 0,
                    'saldo' => $total - $anticipo,
                    'total' => $total,
                    'observaciones' => $request->observaciones,
                ]);

                foreach ($detalles as $detalle) {
                    $pedido->detalles()->create($detalle);
                }

                if ($tieneTorta) {
                    $pedido->tortasPersonalizadas()->create([
                        'porciones' => $torta['porciones'] ?? 1,
                        'sabor' => $torta['sabor'],
                        'relleno' => $torta['relleno'] ?? null,
                        'cobertura' => $torta['cobertura'] ?? null,
                        'decoracion' => $torta['decoracion'] ?? null,
                        'mensaje' => $torta['mensaje'] ?? null,
                        'observaciones' => $torta['observaciones'] ?? null,
                        'precio' => $torta['precio'],
                    ]);
                }

                return $pedido;
            });

            $pedido->load([
                'cliente',
                'estadoPedido',
                'detalles.producto',
                'tortasPersonalizadas',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pedido registrado correctamente.',
                'data' => $pedido,
            ], 201);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo registrar el pedido. Verifica los datos e inténtalo nuevamente.',
            ], 500);
        }
    }
}
