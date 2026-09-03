@extends('layouts.app')

@section('title', 'Productos - Valgreen')

@section('content')

    <h1>Productos</h1>

    <div class="card">

        <a href="/productos/create" class="btn">
            + Registrar producto
        </a>

        <table>

            <thead>

                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                </tr>

            </thead>

            <tbody>

                @forelse($productos as $producto)

                    <tr>

                        <td>
                            {{ $producto->nombre }}
                        </td>

                        <td>
                            {{ $producto->categoria->nombre }}
                        </td>

                        <td>
                            Bs {{ $producto->precio }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3">
                            No existen productos registrados.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection