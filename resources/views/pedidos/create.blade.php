@extends('layouts.app')

@section('title', 'Nuevo pedido - Valgreen')

@section('content')

<div class="mb-4">

    <h1 class="fw-bold mb-1">
        Nuevo pedido
    </h1>

    <p class="text-muted">
        Registra los productos que el cliente desea recibir.
    </p>

</div>


@if($errors->any())

    <div class="alert alert-danger">

        <strong>Hay un problema:</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<form method="POST"
      action="/pedidos">

    @csrf


    {{-- INFORMACIÓN DEL CLIENTE --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                Información del pedido
            </h5>


            <div class="row g-3">

                <div class="col-md-8">

                    <label class="form-label">
                        Cliente
                    </label>

                    <select name="cliente_id"
                            class="form-select"
                            required>

                        <option value="">
                            Seleccione un cliente
                        </option>

                        @foreach($clientes as $cliente)

                            <option value="{{ $cliente->id }}"
                                {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>

                                {{ $cliente->nombres }}
                                {{ $cliente->primerApellido }}
                                {{ $cliente->segundoApellido }}
                                - CI: {{ $cliente->carnet }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-4 d-flex align-items-end">

                    <a href="/clientes/create"
                        class="btn btn-outline-success w-100">

                        + Registrar nuevo cliente

                    </a>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Fecha y hora de entrega
                    </label>

                    <input type="datetime-local"
                           name="fecha_entrega"
                           class="form-control"
                           value="{{ old('fecha_entrega') }}"
                           required>

                </div>

            </div>

        </div>

    </div>



    {{-- PRODUCTOS --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-1">
                Productos del pedido
            </h5>

            <p class="text-muted small mb-3">

                Estos productos serán preparados para el pedido.
                El stock actual no limita la cantidad solicitada.

            </p>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Producto</th>

                            <th>Precio</th>

                            <th style="width: 150px;">
                                Cantidad
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($productos as $producto)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $producto->nombre }}
                                    </strong>

                                    @if($producto->descripcion)

                                        <br>

                                        <small class="text-muted">

                                            {{ $producto->descripcion }}

                                        </small>

                                    @endif

                                </td>


                                <td>

                                    Bs {{ number_format($producto->precio, 2) }}

                                </td>


                                <td>

                                    <input type="number"
                                           name="productos[{{ $producto->id }}]"
                                           class="form-control"
                                           min="0"
                                           value="{{ old('productos.' . $producto->id, 0) }}">

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center text-muted">

                                    No hay productos disponibles.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    {{-- TORTA PERSONALIZADA --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-1">
                Torta personalizada
            </h5>

            <p class="text-muted small mb-3">

                Completa esta sección únicamente si el cliente
                solicita una torta personalizada.

            </p>


            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Porciones
                    </label>

                    <input type="number"
                           name="torta[porciones]"
                           class="form-control"
                           min="1"
                           value="{{ old('torta.porciones') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Sabor
                    </label>

                    <input type="text"
                           name="torta[sabor]"
                           class="form-control"
                           value="{{ old('torta.sabor') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Precio
                    </label>

                    <input type="number"
                           name="torta[precio]"
                           class="form-control"
                           min="0"
                           step="0.01"
                           value="{{ old('torta.precio') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Relleno
                    </label>

                    <input type="text"
                           name="torta[relleno]"
                           class="form-control"
                           value="{{ old('torta.relleno') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Cobertura
                    </label>

                    <input type="text"
                           name="torta[cobertura]"
                           class="form-control"
                           value="{{ old('torta.cobertura') }}">

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Decoración
                    </label>

                    <input type="text"
                           name="torta[decoracion]"
                           class="form-control"
                           value="{{ old('torta.decoracion') }}">

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Mensaje
                    </label>

                    <input type="text"
                           name="torta[mensaje]"
                           class="form-control"
                           value="{{ old('torta.mensaje') }}">

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Observaciones de la torta
                    </label>

                    <input type="text"
                           name="torta[observaciones]"
                           class="form-control"
                           value="{{ old('torta.observaciones') }}">

                </div>

            </div>

        </div>

    </div>



    {{-- PAGO --}}

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                Pago
            </h5>


            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Anticipo
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            Bs
                        </span>

                        <input type="number"
                               name="anticipo"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="{{ old('anticipo', 0) }}"
                               required>

                    </div>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Observaciones del pedido
                    </label>

                    <textarea name="observaciones"
                              class="form-control"
                              rows="2">{{ old('observaciones') }}</textarea>

                </div>

            </div>

        </div>

    </div>



    {{-- BOTONES --}}

    <div class="d-flex justify-content-end gap-2">

        <a href="/pedidos"
           class="btn btn-secondary">

            Cancelar

        </a>


        <button type="submit"
                class="btn btn-valgreen">

            Registrar pedido

        </button>

    </div>

</form>

@endsection