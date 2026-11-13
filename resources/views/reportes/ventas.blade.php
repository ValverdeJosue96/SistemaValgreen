
@extends('layouts.app')

@section('title', 'Informe de ventas - Valgreen')

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
        font-size: 18px;
    }

    .informe-valgreen table {
        width: 100%;
        border-collapse: collapse;
    }

    .informe-valgreen th,
    .informe-valgreen td {
        border: 1px solid #dee2e6;
        padding: 10px;
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
        font-size: 9px !important;
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

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h1 class="fw-bold">Informe de ventas</h1>
        <p class="text-muted mb-0">
            Consulta y genera informes de las ventas realizadas.
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

        <form action="/reportes/ventas" method="GET">
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

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-valgreen">
                        Generar informe
                    </button>

                    <a href="/reportes/ventas"
                       class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- BOTÓN DE IMPRESIÓN --}}
<div class="d-flex justify-content-end gap-2 mb-3 no-print">

    <a href="{{ url('/reportes/ventas/pdf') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}"
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
            <h4>INFORME DE VENTAS</h4>

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

            <p class="text-muted mb-0">
                Fecha de generación: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>

        {{-- RESUMEN --}}
        <div class="informe-resumen">
            <div>
                <p>Ventas registradas</p>
                <strong>{{ $totalVentas }}</strong>
            </div>

            <div>
                <p>Ingresos totales</p>
                <strong>Bs {{ number_format($ingresosTotales, 2) }}</strong>
            </div>
        </div>

        {{-- LISTADO --}}
        <h5 class="fw-bold mb-3">Detalle de ventas realizadas</h5>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Fecha</th>
                        <th>Vendedor</th>
                        <th>Productos vendidos</th>
                        <th class="text-center">Unidades</th>
                        <th class="text-end">Total</th>
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
                                    <div>
                                        {{ $detalle->producto->nombre ?? 'Producto no disponible' }}
                                        ({{ $detalle->cantidad }})
                                    </div>
                                @endforeach
                            </td>

                            <td class="text-center">
                                {{ $venta->detalles->sum('cantidad') }}
                            </td>

                            <td class="text-end fw-bold">
                                Bs {{ number_format($venta->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No existen ventas para el periodo seleccionado.
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
