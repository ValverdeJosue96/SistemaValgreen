
@extends('layouts.app')

@section('title', 'Informe de pedidos - Valgreen')

@section('content')

<style>
    .informe-valgreen {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
    }

    .informe-encabezado {
        text-align: center;
        margin-bottom: 25px;
    }

    .informe-encabezado h2 {
        font-weight: 800;
        letter-spacing: 1px;
    }

    .informe-resumen {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        margin: 20px 0;
        padding: 15px;
        background: #f5f7f5;
        border: 1px solid #dee5de;
        border-radius: 8px;
    }

    .informe-resumen p {
        margin-bottom: 4px;
        color: #666;
    }

    .informe-resumen strong {
        font-size: 16px;
    }

    .informe-valgreen table {
        width: 100%;
        border-collapse: collapse;
    }

    .informe-valgreen th,
    .informe-valgreen td {
        border: 1px solid #dee2e6;
        padding: 8px;
        vertical-align: middle;
    }

    .informe-valgreen thead th {
        background: #e9f0e9;
        color: #263b2b;
    }

    .informe-pie {
        margin-top: 25px;
        font-size: 12px;
        color: #666;
        text-align: right;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        html,
        body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body * {
            visibility: hidden !important;
        }

        .print-area,
        .print-area * {
            visibility: visible !important;
        }

        .print-area {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
        }

        .no-print {
            display: none !important;
        }

        .card,
        .card-body {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
        }

        .informe-valgreen table {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
        }

        .informe-valgreen th,
        .informe-valgreen td {
            padding: 5px !important;
            font-size: 8px !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
        }

        .informe-valgreen thead th {
            background: #e9f0e9 !important;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .informe-valgreen tr {
            page-break-inside: avoid;
        }

        .informe-resumen {
            background: #fff !important;
            border: 1px solid #999 !important;
        }
    }
</style>

{{-- ENCABEZADO DE LA PÁGINA --}}
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h1 class="fw-bold">Informe de pedidos</h1>
        <p class="text-muted mb-0">
            Consulta pedidos, pagos y saldos pendientes.
        </p>
    </div>

    <a href="/reportes" class="btn btn-outline-secondary">
        ← Volver a reportes
    </a>
</div>

{{-- FILTROS --}}
<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body">

        <h5 class="fw-bold mb-3">Parámetros del informe</h5>

        <form action="/reportes/pedidos" method="GET">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Fecha inicial</label>
                    <input
                        type="date"
                        name="fecha_inicio"
                        class="form-control"
                        value="{{ request('fecha_inicio') }}"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha final</label>
                    <input
                        type="date"
                        name="fecha_fin"
                        class="form-control"
                        value="{{ request('fecha_fin') }}"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado del pedido</label>

                    <select name="estado_pedido_id" class="form-select">
                        <option value="">Todos los estados</option>

                        @foreach($estados as $estado)
                            <option
                                value="{{ $estado->id }}"
                                @selected(request('estado_pedido_id') == $estado->id)
                            >
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-valgreen">
                        Generar informe
                    </button>

                    <a href="/reportes/pedidos"
                       class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>

            </div>
        </form>

    </div>
</div>

{{-- BOTONES --}}
<div class="d-flex justify-content-end gap-2 mb-3 no-print">

    <a href="{{ url('/reportes/pedidos/pdf') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}"
       class="btn btn-danger">
        📄 Descargar PDF
    </a>

    <button type="button"
            onclick="window.print()"
            class="btn btn-success">
        🖨 Imprimir
    </button>

</div>

{{-- INFORME --}}
<div class="card shadow-sm border-0">
    <div class="card-body informe-valgreen print-area">

        <div class="informe-encabezado">
            <h2>REPOSTERÍA VALGREEN</h2>
            <h4>INFORME DE PEDIDOS</h4>

            <p class="mb-1">
                Periodo consultado:

                <strong>
                    {{ request('fecha_inicio')
                        ? \Carbon\Carbon::parse(request('fecha_inicio'))->format('d/m/Y')
                        : 'Sin fecha inicial' }}

                    -

                    {{ request('fecha_fin')
                        ? \Carbon\Carbon::parse(request('fecha_fin'))->format('d/m/Y')
                        : 'Sin fecha final' }}
                </strong>
            </p>

            <p class="mb-1">
                Estado:
                <strong>
                    @if(request('estado_pedido_id'))
                        {{ $estados->firstWhere('id', request('estado_pedido_id'))->nombre ?? 'Todos' }}
                    @else
                        Todos los estados
                    @endif
                </strong>
            </p>

            <p class="text-muted mb-0">
                Fecha de generación: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- RESUMEN GENERAL --}}
        <div class="informe-resumen">

            <div>
                <p>Pedidos registrados</p>
                <strong>{{ $totalPedidos }}</strong>
            </div>

            <div>
                <p>Importe total</p>
                <strong>Bs {{ number_format($totalImportePedidos, 2) }}</strong>
            </div>

            <div>
                <p>Total pagado</p>
                <strong>
                    Bs {{ number_format($totalAnticipos + $totalPagosFinales, 2) }}
                </strong>
            </div>

            <div>
                <p>Saldos pendientes</p>
                <strong>Bs {{ number_format($totalSaldosPendientes, 2) }}</strong>
            </div>

        </div>

        {{-- DESGLOSE DE PAGOS --}}
        <h5 class="fw-bold mb-3">Resumen de pagos</h5>

        <table class="table mb-4">
            <tbody>
                <tr>
                    <td>Total de anticipos</td>
                    <td class="text-end fw-bold">
                        Bs {{ number_format($totalAnticipos, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Total de pagos finales</td>
                    <td class="text-end fw-bold">
                        Bs {{ number_format($totalPagosFinales, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Total de saldos pendientes</td>
                    <td class="text-end fw-bold">
                        Bs {{ number_format($totalSaldosPendientes, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- PEDIDOS POR ESTADO --}}
        <h5 class="fw-bold mb-3">Pedidos por estado</h5>

        <table class="table mb-4">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th class="text-end">Cantidad</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pedidosPorEstado as $estado => $cantidad)
                    <tr>
                        <td>{{ $estado }}</td>
                        <td class="text-end">{{ $cantidad }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">
                            No hay pedidos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- LISTADO DE PEDIDOS --}}
        <h5 class="fw-bold mb-3">Detalle de pedidos registrados</h5>

        <div class="table-responsive">
            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Cliente</th>
                        <th>Fecha entrega</th>
                        <th>Estado</th>
                        <th>Registrado por</th>
                        <th>Total</th>
                        <th>Pagado</th>
                        <th>Saldo</th>
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
                                    (float)$pedido->anticipo + (float)$pedido->pago_final,
                                    2
                                ) }}
                            </td>

                            <td>
                                Bs {{ number_format($pedido->saldo, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No existen pedidos para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="informe-pie">
            <p class="mb-0">
                Repostería Valgreen - Sistema de gestión de ventas y pedidos
            </p>
        </div>

    </div>
</div>

@endsection
