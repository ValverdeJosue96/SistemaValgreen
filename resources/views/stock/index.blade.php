@extends('layouts.app')

@section('title', 'Stock - Valgreen')

@section('content')

    <h1>Control de Stock</h1>

    <div class="card">

        <a href="/stock/create" class="btn">
            + Registrar movimiento
        </a>

        <table>

            <thead>

                <tr>
                    <th>Producto</th>
                    <th>Stock actual</th>
                </tr>

            </thead>

            <tbody>

                @forelse($productos as $producto)

                    <tr>

                        <td>
                            {{ $producto->nombre }}
                        </td>

                        <td>
                            {{ $producto->stock->cantidad ?? 0 }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="2">
                            No existen productos registrados.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

@endsection