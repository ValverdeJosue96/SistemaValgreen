@extends('layouts.app')

@section('title', 'Productos - Valgreen')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Productos
            </h1>

            <p class="text-muted mb-0">
                Administración de productos de la repostería
            </p>

        </div>

        <a href="/productos/create"
           class="btn btn-valgreen">

            + Registrar producto

        </a>

    </div>


    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($productos as $producto)

                            <tr>

                                <td class="fw-semibold">
                                    {{ $producto->nombre }}
                                </td>

                                <td>
                                    <span class="badge text-bg-light">
                                        {{ $producto->categoria->nombre }}
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        Bs {{ $producto->precio }}
                                    </strong>
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