@extends('layouts.app')

@section('title', 'Dashboard Vendedor - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            🧑‍💼 Dashboard Vendedor
        </h2>

        <p class="text-muted mb-0">
            Bienvenido, {{ auth()->user()->nombres }}
        </p>

    </div>

    <span class="badge bg-success fs-6">
        Vendedor
    </span>

</div>


<!-- RESUMEN DEL DÍA -->

<div class="row g-4 mb-4">

    <div class="col-md-6">

        <div class="card dashboard-card shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <p class="text-muted mb-1">
                            Ventas de hoy
                        </p>

                        <h2 class="fw-bold">
                            Bs. {{ number_format($ventasHoy, 2) }}
                        </h2>

                    </div>

                    <div class="fs-1">
                        💰
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6">

        <div class="card dashboard-card shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <p class="text-muted mb-1">
                            Pedidos pendientes
                        </p>

                        <h2 class="fw-bold">
                            {{ $pedidosPendientes }}
                        </h2>

                    </div>

                    <div class="fs-1">
                        📝
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ACCIONES PRINCIPALES -->

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="fw-bold mb-0">
            ⚡ Acciones rápidas
        </h5>

    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-6">

                <a href="/ventas/create"
                   class="btn btn-valgreen w-100 py-3">

                    <span class="fs-4">
                        🛒
                    </span>

                    <br>

                    <strong>
                        Nueva venta
                    </strong>

                </a>

            </div>


            <div class="col-md-6">

                <a href="/pedidos/create"
                   class="btn btn-outline-success w-100 py-3">

                    <span class="fs-4">
                        📝
                    </span>

                    <br>

                    <strong>
                        Nuevo pedido
                    </strong>

                </a>

            </div>


            <div class="col-md-6">

                <a href="/clientes/create"
                   class="btn btn-outline-success w-100 py-3">

                    <span class="fs-4">
                        👤
                    </span>

                    <br>

                    <strong>
                        Nuevo cliente
                    </strong>

                </a>

            </div>


            <div class="col-md-6">

                <a href="/pedidos"
                   class="btn btn-outline-success w-100 py-3">

                    <span class="fs-4">
                        🔎
                    </span>

                    <br>

                    <strong>
                        Buscar pedido
                    </strong>

                </a>

            </div>

        </div>

    </div>

</div>


<!-- PEDIDOS DE HOY -->

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-white">

        <h5 class="fw-bold mb-0">
            📅 Pedidos para hoy
        </h5>

    </div>

    <div class="card-body">

        @if($pedidosHoy->count() > 0)

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th></th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($pedidosHoy as $pedido)

                            <tr>

                                <td>
                                    <strong>
                                        #{{ $pedido->id }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $pedido->cliente->nombres }}
                                    {{ $pedido->cliente->primerApellido }}
                                </td>

                                <td>
                                    {{ $pedido->fecha_entrega->format('H:i') }}
                                </td>

                                <td>

                                    <span class="badge bg-success">

                                        {{ $pedido->estadoPedido->nombre }}

                                    </span>

                                </td>

                                <td>

                                    <a href="/pedidos/{{ $pedido->id }}"
                                       class="btn btn-sm btn-outline-success">

                                        Ver

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center text-muted py-4">

                <div class="fs-2">
                    📋
                </div>

                <p class="mb-0">
                    No hay pedidos para hoy.
                </p>

            </div>

        @endif

    </div>

</div>


<!-- CONSULTAR STOCK -->

<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="fw-bold mb-1">
                    📦 Consultar stock
                </h5>

                <p class="text-muted mb-0">
                    Consulta la disponibilidad actual de los productos.
                </p>

            </div>

            <a href="/stock"
               class="btn btn-valgreen">

                Ver stock

            </a>

        </div>

    </div>

</div>

@endsection