@extends('layouts.app')

@section('title', 'Dashboard Administrador - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold mb-1">
            👑 Dashboard Administrador
        </h2>

        <p class="text-muted mb-0">
            Bienvenido, {{ auth()->user()->nombres }}
        </p>
    </div>

    <span class="badge bg-success fs-6">
        Administrador
    </span>

</div>


<!-- TARJETAS PRINCIPALES -->

<div class="row g-4 mb-4">

    <div class="col-md-3">

        <div class="card dashboard-card shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>
                        <p class="text-muted mb-1">
                            Ventas de hoy
                        </p>

                        <h3 class="fw-bold">
                            Bs. {{ number_format($ventasHoy, 2) }}
                        </h3>
                    </div>

                    <div class="fs-1">
                        💰
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card dashboard-card shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <p class="text-muted mb-1">
                            Pedidos activos
                        </p>

                        <h3 class="fw-bold">
                            {{ $pedidosActivos }}
                        </h3>

                    </div>

                    <div class="fs-1">
                        📝
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card dashboard-card shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <p class="text-muted mb-1">
                            Usuarios activos
                        </p>

                        <h3 class="fw-bold">
                            {{ $usuariosActivos }}
                        </h3>

                    </div>

                    <div class="fs-1">
                        👥
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card dashboard-card shadow-sm h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <p class="text-muted mb-1">
                            Stock bajo
                        </p>

                        <h3 class="fw-bold">
                            {{ $stockBajo }}
                        </h3>

                    </div>

                    <div class="fs-1">
                        ⚠️
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- SEGUNDA SECCIÓN -->

<div class="row g-4">


    <!-- PEDIDOS DE HOY -->

    <div class="col-lg-7">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <h5 class="mb-0 fw-bold">
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
                    <th>Entrega</th>
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

    <div class="text-center text-muted py-5">

        <div class="fs-1 mb-2">
            📋
        </div>

        <p class="mb-0">
            No hay pedidos para hoy.
        </p>

    </div>

@endif

            </div>

        </div>

    </div>


    <!-- ACCIONES -->

    <div class="col-lg-5">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <h5 class="mb-0 fw-bold">
                    ⚡ Acciones rápidas
                </h5>

            </div>

            <div class="card-body">

                <div class="d-grid gap-2">

                    <a href="/ventas/create"
                       class="btn btn-valgreen">
                        💰 Registrar venta
                    </a>

                    <a href="/pedidos/create"
                       class="btn btn-outline-success">
                        📝 Registrar pedido
                    </a>

                    <a href="/productos/create"
                       class="btn btn-outline-success">
                        📦 Registrar producto
                    </a>

                    <a href="/usuarios/create"
                       class="btn btn-outline-success">
                        👥 Registrar usuario
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- PRODUCTOS CON STOCK BAJO -->

<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-white">

        <h5 class="mb-0 fw-bold">
            ⚠️ Productos con stock bajo
        </h5>

    </div>

    <div class="card-body">

        @if($productosStockBajo->count() > 0)

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Estado</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($productosStockBajo as $item)

                            <tr>

                                <td>
                                    {{ $item->producto->nombre }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $item->cantidad }}
                                    </strong>

                                </td>

                                <td>

                                    @if($item->cantidad == 0)

                                        <span class="badge bg-danger">
                                            Agotado
                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">
                                            Stock bajo
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center text-muted py-4">

                <div class="fs-2">
                    ✅
                </div>

                <p class="mb-0">
                    No hay productos con stock bajo.
                </p>

            </div>

        @endif

    </div>

</div>

<!-- INFORMACIÓN -->

<div class="card shadow-sm border-0 mt-4">

    <div class="card-body">

        <h5 class="fw-bold">
            📊 Resumen del sistema
        </h5>

        <p class="text-muted mb-0">
            Desde este panel puedes supervisar las ventas,
            pedidos, productos, stock y usuarios de Valgreen.
        </p>

    </div>

</div>

@endsection