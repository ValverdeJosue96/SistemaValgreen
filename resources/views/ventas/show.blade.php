@extends('layouts.app')

@section('title', 'Detalle de venta - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold mb-1">
            Detalle de venta #{{ $venta->id }}
        </h1>

        <p class="text-muted mb-0">
            Información de la venta realizada
        </p>
    </div>

    <a href="/ventas"
       class="btn btn-outline-secondary">

        ← Volver a ventas

    </a>

</div>


{{-- Información general de la venta --}}
<div class="card shadow-sm border-0 mb-4">

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-4">

                <small class="text-muted d-block">
                    Número de venta
                </small>

                <strong>
                    #{{ $venta->id }}
                </strong>

            </div>


            <div class="col-md-4">

                <small class="text-muted d-block">
                    Vendedor
                </small>

                <strong>
                    {{ $venta->usuario->nombres ?? 'N/A' }}
                </strong>

            </div>


            <div class="col-md-4">

                <small class="text-muted d-block">
                    Fecha y hora
                </small>

                <strong>
                    {{ $venta->fecha->format('d/m/Y H:i') }}
                </strong>

            </div>

        </div>

    </div>

</div>


{{-- Productos vendidos --}}
<div class="card shadow-sm border-0">

    <div class="card-body">

        <h5 class="fw-bold mb-4">
            Productos vendidos
        </h5>


        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio unitario</th>
                        <th class="text-end">Subtotal</th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($venta->detalles as $detalle)

                        <tr>

                            <td>

                                <div class="fw-semibold">
                                    {{ $detalle->producto->nombre ?? 'Producto eliminado' }}
                                </div>

                            </td>


                            <td class="text-center">

                                {{ $detalle->cantidad }}

                            </td>


                            <td class="text-end">

                                Bs {{ number_format($detalle->precio_unitario, 2) }}

                            </td>


                            <td class="text-end fw-semibold">

                                Bs {{ number_format($detalle->subtotal, 2) }}

                            </td>

                        </tr>

                    @endforeach

                </tbody>


                <tfoot>

                    <tr>

                        <td colspan="3"
                            class="text-end fw-bold">

                            TOTAL

                        </td>

                        <td class="text-end">

                            <span class="fs-5 fw-bold">

                                Bs {{ number_format($venta->total, 2) }}

                            </span>

                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>

@endsection