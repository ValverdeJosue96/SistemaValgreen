@extends('layouts.app')

@section('title', 'Registrar movimiento de stock - Valgreen')

@section('content')

<div class="container-fluid">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Registrar movimiento de stock</h1>
            <p class="text-muted mb-0">
                Registra entradas, salidas o ajustes de productos.
            </p>
        </div>

        <a href="/stock" class="btn btn-outline-secondary">
            ← Volver al stock
        </a>
    </div>


    {{-- Tarjeta principal --}}
    <div class="section-card">

        <div class="section-card-header">
            <div>
                <h5 class="mb-1">Nuevo movimiento</h5>
                <small class="text-muted">
                    Complete los datos del movimiento.
                </small>
            </div>
        </div>


        <div class="section-card-body">

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Se encontraron algunos errores:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form method="POST" action="/stock">

                @csrf

                <div class="row g-4">

                    {{-- Producto --}}
                    <div class="col-md-6">

                        <label for="producto_id" class="form-label fw-semibold">
                            Producto
                        </label>

                        <select
                            name="producto_id"
                            id="producto_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Seleccione un producto
                            </option>

                            @foreach($productos as $producto)

                                <option
                                    value="{{ $producto->id }}"
                                    {{ old('producto_id') == $producto->id ? 'selected' : '' }}
                                >
                                    {{ $producto->nombre }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Tipo de movimiento --}}
                    <div class="col-md-6">

                        <label for="tipo_movimiento_id" class="form-label fw-semibold">
                            Tipo de movimiento
                        </label>

                        <select
                            name="tipo_movimiento_id"
                            id="tipo_movimiento_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Seleccione un tipo
                            </option>

                            @foreach($tipos as $tipo)

                                <option
                                    value="{{ $tipo->id }}"
                                    {{ old('tipo_movimiento_id') == $tipo->id ? 'selected' : '' }}
                                >
                                    {{ $tipo->nombre }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Cantidad --}}
                    <div class="col-md-6">

                        <label for="cantidad" class="form-label fw-semibold">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            name="cantidad"
                            id="cantidad"
                            class="form-control"
                            min="1"
                            value="{{ old('cantidad') }}"
                            required
                        >

                        <small class="text-muted">
                            Ingrese una cantidad mayor a 0.
                        </small>

                    </div>


                    {{-- Motivo --}}
                    <div class="col-12">

                        <label for="motivo" class="form-label fw-semibold">
                            Motivo
                        </label>

                        <textarea
                            name="motivo"
                            id="motivo"
                            class="form-control"
                            rows="4"
                            placeholder="Ejemplo: Reposición de productos, ajuste de inventario, etc."
                        >{{ old('motivo') }}</textarea>

                    </div>

                </div>


                {{-- Botones --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">

                    <a href="/stock" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-success px-4">
                        Registrar movimiento
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection