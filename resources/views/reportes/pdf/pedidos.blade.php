
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de pedidos - Valgreen</title>

    <style>
        @page {
            margin: 30px 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
        }

        .encabezado {
            text-align: center;
            margin-bottom: 18px;
        }

        .encabezado h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #263b2b;
        }

        .encabezado h2 {
            font-size: 14px;
            margin-top: 5px;
        }

        .resumen {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .resumen td {
            width: 25%;
            padding: 9px 6px;
            border: 1px solid #ccc;
            background-color: #f5f7f5;
        }

        .resumen strong {
            font-size: 12px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        .tabla th {
            background-color: #e9f0e9;
            color: #263b2b;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #bbb;
            padding: 5px 4px;
            text-align: left;
            overflow-wrap: break-word;
        }

        .tabla th {
            font-size: 8px;
        }

        .tabla td {
            font-size: 7px;
        }

        .centro {
            text-align: center !important;
        }

        .derecha {
            text-align: right !important;
        }

        .seccion {
            font-size: 11px;
            margin-top: 18px;
            margin-bottom: 7px;
        }

        .pie {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #666;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <div class="encabezado">
        <h1>REPOSTERÍA VALGREEN</h1>
        <h2>INFORME DE PEDIDOS</h2>

        <p>
            Periodo consultado:
            {{ request('fecha_inicio')
                ? \Carbon\Carbon::parse(request('fecha_inicio'))->format('d/m/Y')
                : 'Sin fecha inicial' }}
            -
            {{ request('fecha_fin')
                ? \Carbon\Carbon::parse(request('fecha_fin'))->format('d/m/Y')
                : 'Sin fecha final' }}
        </p>

        <p>
            Estado:
            {{ $estadoSeleccionado->nombre ?? 'Todos los estados' }}
        </p>

        <p>
            Fecha de generación: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <table class="resumen">
        <tr>
            <td>
                Pedidos registrados<br>
                <strong>{{ $totalPedidos }}</strong>
            </td>

            <td>
                Importe total<br>
                <strong>Bs {{ number_format($totalImportePedidos, 2) }}</strong>
            </td>

            <td>
                Total pagado<br>
                <strong>
                    Bs {{ number_format($totalAnticipos + $totalPagosFinales, 2) }}
                </strong>
            </td>

            <td>
                Saldos pendientes<br>
                <strong>Bs {{ number_format($totalSaldosPendientes, 2) }}</strong>
            </td>
        </tr>
    </table>

    <h3 class="seccion">Resumen de pagos</h3>

    <table class="tabla">
        <tr>
            <th>Total de anticipos</th>
            <td class="derecha">
                Bs {{ number_format($totalAnticipos, 2) }}
            </td>
        </tr>

        <tr>
            <th>Total de pagos finales</th>
            <td class="derecha">
                Bs {{ number_format($totalPagosFinales, 2) }}
            </td>
        </tr>

        <tr>
            <th>Total de saldos pendientes</th>
            <td class="derecha">
                Bs {{ number_format($totalSaldosPendientes, 2) }}
            </td>
        </tr>
    </table>

    <h3 class="seccion">Pedidos por estado</h3>

    <table class="tabla">
        <thead>
            <tr>
                <th>Estado</th>
                <th class="centro">Cantidad</th>
            </tr>
        </thead>

        <tbody>
            @forelse($pedidosPorEstado as $estado => $cantidad)
                <tr>
                    <td>{{ $estado }}</td>
                    <td class="centro">{{ $cantidad }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="centro">
                        No hay pedidos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="seccion">Detalle de pedidos registrados</h3>

    <table class="tabla">
        <thead>
            <tr>
                <th style="width: 5%;">N.º</th>
                <th style="width: 15%;">Cliente</th>
                <th style="width: 14%;">Fecha entrega</th>
                <th style="width: 12%;">Estado</th>
                <th style="width: 14%;">Registrado por</th>
                <th style="width: 13%;">Total</th>
                <th style="width: 13%;">Pagado</th>
                <th style="width: 14%;">Saldo</th>
            </tr>
        </thead>

        <tbody>
            @forelse($pedidos as $pedido)
                <tr>
                    <td>#{{ $pedido->id }}</td>

                    <td>
                        {{ $pedido->cliente->nombres ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $pedido->fecha_entrega
                            ? $pedido->fecha_entrega->format('d/m/Y H:i')
                            : 'No definida' }}
                    </td>

                    <td>
                        {{ $pedido->estadoPedido->nombre ?? 'Sin estado' }}
                    </td>

                    <td>
                        {{ $pedido->creador->nombres ?? 'N/A' }}
                    </td>

                    <td>
                        Bs {{ number_format($pedido->total, 2) }}
                    </td>

                    <td>
                        Bs {{ number_format(
                            (float) $pedido->anticipo + (float) $pedido->pago_final,
                            2
                        ) }}
                    </td>

                    <td>
                        Bs {{ number_format($pedido->saldo, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="centro">
                        No existen pedidos para los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pie">
        Repostería Valgreen - Sistema de gestión de ventas y pedidos
    </div>

</body>
</html>
