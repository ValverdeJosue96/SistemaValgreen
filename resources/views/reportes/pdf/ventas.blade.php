
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de ventas - Valgreen</title>

    <style>
        @page {
            margin: 35px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .encabezado {
            text-align: center;
            margin-bottom: 20px;
        }

        .encabezado h1 {
            font-size: 21px;
            margin-bottom: 5px;
            color: #263b2b;
        }

        .encabezado h2 {
            font-size: 15px;
            margin-top: 5px;
        }

        .resumen {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .resumen td {
            width: 50%;
            padding: 12px;
            border: 1px solid #ccc;
            background-color: #f5f7f5;
        }

        .resumen strong {
            font-size: 15px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .tabla th {
            background-color: #e9f0e9;
            color: #263b2b;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #bbb;
            padding: 7px;
            text-align: left;
        }

        .derecha {
            text-align: right !important;
        }

        .centro {
            text-align: center !important;
        }

        .pie {
            margin-top: 25px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="encabezado">
        <h1>REPOSTERÍA VALGREEN</h1>
        <h2>INFORME DE VENTAS</h2>

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
            Fecha de generación: {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <table class="resumen">
        <tr>
            <td>
                Ventas registradas<br>
                <strong>{{ $totalVentas }}</strong>
            </td>

            <td>
                Ingresos totales<br>
                <strong>Bs {{ number_format($ingresosTotales, 2) }}</strong>
            </td>
        </tr>
    </table>

    <h3>Detalle de ventas realizadas</h3>

    <table class="tabla">
        <thead>
            <tr>
                <th>N.º</th>
                <th>Fecha</th>
                <th>Vendedor</th>
                <th>Productos vendidos</th>
                <th class="centro">Unidades</th>
                <th class="derecha">Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($ventas as $venta)
                <tr>
                    <td>#{{ $venta->id }}</td>

                    <td>
                        {{ $venta->fecha->format('d/m/Y H:i') }}
                    </td>

                    <td>
                        {{ $venta->usuario->nombres ?? 'N/A' }}
                    </td>

                    <td>
                        @foreach($venta->detalles as $detalle)
                            {{ $detalle->producto->nombre ?? 'Producto no disponible' }}
                            ({{ $detalle->cantidad }})<br>
                        @endforeach
                    </td>

                    <td class="centro">
                        {{ $venta->detalles->sum('cantidad') }}
                    </td>

                    <td class="derecha">
                        Bs {{ number_format($venta->total, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="centro">
                        No existen ventas para el periodo seleccionado.
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
