@extends('layouts.app')

@section('title', 'Pedidos - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold mb-1">
            Pedidos
        </h1>

        <p class="text-muted mb-0">
            Gestión de pedidos de clientes
        </p>
    </div>

    <a href="/pedidos/create"
       class="btn btn-valgreen">

        + Nuevo pedido

    </a>

</div>


@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

@endif


@if($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>N.º</th>

                        <th>Cliente</th>

                        <th>Fecha de pedido</th>

                        <th>Fecha de entrega</th>

                        <th>Total</th>

                        <th>Anticipo</th>

                        <th>Saldo</th>

                        <th>Estado</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($pedidos as $pedido)

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

                                {{ $pedido->fecha_pedido->format('d/m/Y H:i') }}

                            </td>


                            <td>

                                {{ $pedido->fecha_entrega->format('d/m/Y H:i') }}

                            </td>


                            <td>

                                <strong>
                                    Bs {{ number_format($pedido->total, 2) }}
                                </strong>

                            </td>


                            <td>

                                Bs {{ number_format($pedido->anticipo, 2) }}

                            </td>


                            <td>

                                <strong>
                                    Bs {{ number_format($pedido->saldo, 2) }}
                                </strong>

                            </td>


                            <td>

                                @if($pedido->estadoPedido)

                                    <span class="badge text-bg-secondary">

                                        {{ $pedido->estadoPedido->nombre }}

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center text-muted py-5">

                                No existen pedidos registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection