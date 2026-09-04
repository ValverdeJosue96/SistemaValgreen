@extends('layouts.app')

@section('title', 'Stock - Valgreen')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Control de Stock
            </h1>

            <p class="text-muted mb-0">
                Inventario de productos terminados
            </p>

        </div>

        <a href="/stock/create"
           class="btn btn-valgreen">

            + Registrar movimiento

        </a>

    </div>


    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Producto</th>
                            <th>Stock actual</th>
                            <th>Estado</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($productos as $producto)

                            @php
                                $cantidad = $producto->stock->cantidad ?? 0;
                            @endphp

                            <tr>

                                <td class="fw-semibold">
                                    {{ $producto->nombre }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $cantidad }}
                                    </strong>

                                    unidades

                                </td>

                                <td>

                                    @if($cantidad > 0)

                                        <span class="badge text-bg-success">
                                            Disponible
                                        </span>

                                    @else

                                        <span class="badge text-bg-danger">
                                            Agotado
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center text-muted py-4">

                                    No existen productos registrados.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection