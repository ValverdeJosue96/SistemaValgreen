@extends('layouts.app')

@section('title', 'Ventas - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold">
            Ventas
        </h1>

        <p class="text-muted mb-0">
            Registro de ventas realizadas
        </p>
    </div>

    <a href="/ventas/create"
       class="btn btn-valgreen">
        + Nueva venta
    </a>

</div>


<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>N.º</th>
                        <th>Vendedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($ventas as $venta)

                        <tr>

                            <td>
                                #{{ $venta->id }}
                            </td>

                            <td>
                                {{ $venta->usuario->nombres ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $venta->fecha->format('d/m/Y H:i') }}
                            </td>

                            <td class="fw-bold">
                                Bs {{ number_format($venta->total, 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted py-4">

                                Todavía no existen ventas registradas.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection