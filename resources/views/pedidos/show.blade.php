@extends('layouts.app')

@section('title', 'Detalle del pedido - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="fw-bold mb-1">
            Pedido #{{ $pedido->id }}
        </h1>

        <p class="text-muted mb-0">
            Detalle completo del pedido
        </p>

    </div>

    <a href="/pedidos"
       class="btn btn-secondary">

        Volver

    </a>

</div>


@if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

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


<div class="row g-4">

    {{-- DATOS DEL CLIENTE --}}
    <div class="col-md-6">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Cliente
                </h5>

                <p class="mb-1">

                    <strong>Nombre:</strong>

                    {{ $pedido->cliente->nombres }}
                    {{ $pedido->cliente->primerApellido }}
                    {{ $pedido->cliente->segundoApellido }}

                </p>

                <p class="mb-1">

                    <strong>Carnet:</strong>

                    {{ $pedido->cliente->carnet }}

                </p>

                <p class="mb-0">

                    <strong>Teléfono:</strong>

                    {{ $pedido->cliente->telefono }}

                </p>

            </div>

        </div>

    </div>


    {{-- INFORMACIÓN DEL PEDIDO --}}
    <div class="col-md-6">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Información del pedido
                </h5>

                <p class="mb-1">

                    <strong>Fecha del pedido:</strong>

                    {{ $pedido->fecha_pedido->format('d/m/Y H:i') }}

                </p>

                <p class="mb-1">

                    <strong>Fecha de entrega:</strong>

                    {{ $pedido->fecha_entrega->format('d/m/Y H:i') }}

                </p>

                <p class="mb-0">

                    <strong>Estado actual:</strong>

                    <span class="badge text-bg-secondary">

                        {{ $pedido->estadoPedido->nombre }}

                    </span>

                </p>

            </div>

        </div>

    </div>

</div>


{{-- PRODUCTOS --}}

<div class="card shadow-sm border-0 mt-4">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Productos solicitados
        </h5>

        @if($pedido->detalles->count() > 0)

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Subtotal</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($pedido->detalles as $detalle)

                            <tr>

                                <td>

                                    {{ $detalle->producto->nombre }}

                                </td>

                                <td>

                                    {{ $detalle->cantidad }}

                                </td>

                                <td>

                                    Bs {{ number_format($detalle->precio_unitario, 2) }}

                                </td>

                                <td>

                                    <strong>

                                        Bs {{ number_format($detalle->subtotal, 2) }}

                                    </strong>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <p class="text-muted mb-0">

                Este pedido no tiene productos registrados.

            </p>

        @endif

    </div>

</div>


{{-- TORTA PERSONALIZADA --}}

@if($pedido->tortasPersonalizadas->count() > 0)

<div class="card shadow-sm border-0 mt-4">

    <div class="card-body">

        <h5 class="fw-bold mb-3">
            Torta personalizada
        </h5>

        @foreach($pedido->tortasPersonalizadas as $torta)

            <div class="row">

                <div class="col-md-4 mb-2">

                    <strong>Porciones:</strong>

                    {{ $torta->porciones }}

                </div>

                <div class="col-md-4 mb-2">

                    <strong>Sabor:</strong>

                    {{ $torta->sabor }}

                </div>

                <div class="col-md-4 mb-2">

                    <strong>Precio:</strong>

                    Bs {{ number_format($torta->precio, 2) }}

                </div>

                <div class="col-md-4 mb-2">

                    <strong>Relleno:</strong>

                    {{ $torta->relleno ?? 'No especificado' }}

                </div>

                <div class="col-md-4 mb-2">

                    <strong>Cobertura:</strong>

                    {{ $torta->cobertura ?? 'No especificada' }}

                </div>

                <div class="col-md-4 mb-2">

                    <strong>Decoración:</strong>

                    {{ $torta->decoracion ?? 'No especificada' }}

                </div>

                <div class="col-md-6 mb-2">

                    <strong>Mensaje:</strong>

                    {{ $torta->mensaje ?? 'Ninguno' }}

                </div>

                <div class="col-md-6 mb-2">

                    <strong>Observaciones:</strong>

                    {{ $torta->observaciones ?? 'Ninguna' }}

                </div>

            </div>

        @endforeach

    </div>

</div>

@endif


{{-- PAGO Y ESTADO --}}

<div class="row g-4 mt-1">

    {{-- RESUMEN DEL PAGO --}}

    <div class="col-md-6">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Resumen de pago
                </h5>

                <p>

                    <strong>Total:</strong>

                    Bs {{ number_format($pedido->total, 2) }}

                </p>

                <p>

                    <strong>Anticipo:</strong>

                    Bs {{ number_format($pedido->anticipo, 2) }}

                </p>

                <p>

                    <strong>Pago final:</strong>

                    Bs {{ number_format($pedido->pago_final, 2) }}

                </p>

                <hr>

                <h5>

                    Saldo:

                    <strong>

                        Bs {{ number_format($pedido->saldo, 2) }}

                    </strong>

                </h5>


                @if($pedido->saldo > 0)

                    <form method="POST"
                          action="/pedidos/{{ $pedido->id }}/pago"
                          class="mt-3">

                        @csrf

                        @method('PUT')

                        <label class="form-label">

                            Registrar pago

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">

                                Bs

                            </span>

                            <input type="number"
                                   name="pago"
                                   class="form-control"
                                   min="0.01"
                                   max="{{ $pedido->saldo }}"
                                   step="0.01"
                                   required>

                            <button class="btn btn-success"
                                    type="submit">

                                Registrar

                            </button>

                        </div>

                    </form>

                @else

                    <div class="alert alert-success mt-3 mb-0">

                        Pedido completamente pagado.

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- CAMBIAR ESTADO --}}

    <div class="col-md-6">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Estado del pedido
                </h5>


                <form method="POST"
                      action="/pedidos/{{ $pedido->id }}/estado">

                    @csrf

                    @method('PUT')


                    <label class="form-label">

                        Estado

                    </label>


                    <select name="estado_pedido_id"
                            class="form-select mb-3"
                            required>

                        @foreach($estados as $estado)

                            <option value="{{ $estado->id }}"

                                {{ $pedido->estado_pedido_id == $estado->id
                                    ? 'selected'
                                    : '' }}>

                                {{ $estado->nombre }}

                            </option>

                        @endforeach

                    </select>


                    <button type="submit"
                            class="btn btn-valgreen">

                        Actualizar estado

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection