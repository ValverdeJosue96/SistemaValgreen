@extends('layouts.app')

@section('title', 'Dashboard Vendedor - Valgreen')

@section('content')


<!-- ==========================================
     ENCABEZADO
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="page-title mb-1">
            🧑‍💼 Dashboard Vendedor
        </h2>

        <p class="text-muted mb-0">

            Bienvenido, {{ auth()->user()->nombres }} 👋

        </p>

    </div>


    <span class="badge bg-success px-3 py-2">

        Vendedor

    </span>

</div>



<!-- ==========================================
     INDICADORES
========================================== -->

<div class="row g-4 mb-4">


    <!-- VENTAS -->

    <div class="col-md-6">

        <div class="dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="stat-title">
                            Ventas de hoy
                        </div>

                        <div class="stat-number">

                            Bs. {{ number_format($ventasHoy, 2) }}

                        </div>

                    </div>


                    <div class="stat-icon">
                        💰
                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- PEDIDOS -->

    <div class="col-md-6">

        <div class="dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="stat-title">
                            Pedidos pendientes
                        </div>

                        <div class="stat-number">

                            {{ $pedidosPendientes }}

                        </div>

                    </div>


                    <div class="stat-icon">
                        📝
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- ==========================================
     ACCIONES RÁPIDAS
========================================== -->

<div class="section-card mb-4">


    <div class="section-header">

        <h5>
            ⚡ Acciones rápidas
        </h5>

    </div>


    <div class="card-body">

        <div class="row g-3">


            <!-- NUEVA VENTA -->

            <div class="col-md-6">

                <a href="/ventas/create"
                   class="quick-action text-center">

                    <div class="quick-action-icon">
                        🛒
                    </div>

                    <div class="quick-action-title">
                        Nueva venta
                    </div>

                    <div class="quick-action-text">
                        Registrar una venta
                    </div>

                </a>

            </div>


            <!-- NUEVO PEDIDO -->

            <div class="col-md-6">

                <a href="/pedidos/create"
                   class="quick-action text-center">

                    <div class="quick-action-icon">
                        📝
                    </div>

                    <div class="quick-action-title">
                        Nuevo pedido
                    </div>

                    <div class="quick-action-text">
                        Registrar un pedido
                    </div>

                </a>

            </div>


            <!-- NUEVO CLIENTE -->

            <div class="col-md-6">

                <a href="/clientes/create"
                   class="quick-action text-center">

                    <div class="quick-action-icon">
                        👤
                    </div>

                    <div class="quick-action-title">
                        Nuevo cliente
                    </div>

                    <div class="quick-action-text">
                        Registrar cliente
                    </div>

                </a>

            </div>


            <!-- BUSCAR PEDIDO -->

            <div class="col-md-6">

                <a href="/pedidos"
                   class="quick-action text-center">

                    <div class="quick-action-icon">
                        🔎
                    </div>

                    <div class="quick-action-title">
                        Buscar pedido
                    </div>

                    <div class="quick-action-text">
                        Consultar pedidos
                    </div>

                </a>

            </div>


        </div>

    </div>

</div>



<!-- ==========================================
     PEDIDOS DE HOY
========================================== -->

<div class="section-card mb-4">


    <div class="section-header d-flex justify-content-between align-items-center">

        <h5>
            📅 Pedidos para hoy
        </h5>

        <a href="/pedidos"
           class="btn btn-sm btn-outline-success">

            Ver todos

        </a>

    </div>


    <div class="card-body p-0">


        @if($pedidosHoy->count() > 0)


            <div class="table-responsive">

                <table class="table mb-0">

                    <thead>

                        <tr>

                            <th class="ps-4">
                                Pedido
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Hora
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @foreach($pedidosHoy as $pedido)


                            <tr>

                                <td class="ps-4">

                                    <strong>
                                        #{{ $pedido->id }}
                                    </strong>

                                </td>


                                <td>

                                    {{ $pedido->cliente->nombres }}

                                    {{ $pedido->cliente->primer_apellido }}

                                </td>


                                <td>

                                    {{ $pedido->fecha_entrega->format('H:i') }}

                                </td>


                                <td>


                                    @if($pedido->estadoPedido->nombre === 'Pendiente')

                                        <span class="badge estado-pendiente">
                                            Pendiente
                                        </span>


                                    @elseif($pedido->estadoPedido->nombre === 'En preparación')

                                        <span class="badge estado-preparacion">
                                            En preparación
                                        </span>


                                    @elseif($pedido->estadoPedido->nombre === 'Listo')

                                        <span class="badge estado-listo">
                                            Listo
                                        </span>


                                    @elseif($pedido->estadoPedido->nombre === 'Entregado')

                                        <span class="badge estado-entregado">
                                            Entregado
                                        </span>


                                    @else

                                        <span class="badge estado-cancelado">
                                            Cancelado
                                        </span>

                                    @endif


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



<!-- ==========================================
     CONSULTAR STOCK
========================================== -->

<div class="section-card">


    <div class="card-body p-4">


        <div class="d-flex justify-content-between align-items-center">


            <div>

                <h5 class="fw-bold mb-1">

                    📦 Consultar stock

                </h5>

                <p class="text-muted mb-0">

                    Consulta la disponibilidad actual
                    de los productos.

                </p>

            </div>


            <a href="/stock"
               class="btn btn-valgreen px-4">

                Ver stock

            </a>


        </div>


    </div>

</div>


@endsection