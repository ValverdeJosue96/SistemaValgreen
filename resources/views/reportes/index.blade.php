
@extends('layouts.app')

@section('title', 'Reportes - Valgreen')

@section('content')

<div class="mb-4">
    <h1 class="fw-bold">Reportes</h1>
    <p class="text-muted mb-0">
        Consulta información y estadísticas de ventas y pedidos.
    </p>
</div>

<div class="row g-4">

    {{-- Reporte de ventas --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">

                <div class="stat-icon mb-3">
                    💰
                </div>

                <h4 class="fw-bold">Reporte de ventas</h4>

                <p class="text-muted">
                    Consulta las ventas realizadas, los ingresos
                    obtenidos, los vendedores y el detalle de cada venta.
                </p>

                <a href="/reportes/ventas"
                   class="btn btn-valgreen">
                    Consultar ventas
                </a>

            </div>
        </div>
    </div>

    {{-- Reporte de pedidos --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">

                <div class="stat-icon mb-3">
                    📝
                </div>

                <h4 class="fw-bold">Reporte de pedidos</h4>

                <p class="text-muted">
                    Consulta los pedidos registrados, sus estados,
                    anticipos, pagos finales y saldos pendientes.
                </p>

                <a href="/reportes/pedidos"
                   class="btn btn-valgreen">
                    Consultar pedidos
                </a>

            </div>
        </div>
    </div>

</div>

@endsection
