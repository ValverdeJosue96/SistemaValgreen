@extends('layouts.app')

@section('title', 'Nueva venta - Valgreen')

@section('content')

<div class="mb-4">

    <h1 class="fw-bold">
        Nueva venta
    </h1>

    <p class="text-muted">
        Selecciona los productos vendidos.
    </p>

</div>


<form method="POST"
      action="/ventas">

    @csrf

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Cantidad</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($productos as $producto)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $producto->nombre }}
                                    </strong>

                                </td>

                                <td>
                                    Bs {{ number_format($producto->precio, 2) }}
                                </td>

                                <td>

                                    {{ $producto->stock->cantidad ?? 0 }}

                                </td>

                                <td style="width: 150px;">

                                    <input
                                        type="number"
                                        name="productos[{{ $producto->id }}]"
                                        class="form-control"
                                        min="0"
                                        max="{{ $producto->stock->cantidad ?? 0 }}"
                                        value="0"
                                    >

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="mt-4 d-flex justify-content-end">

        <button type="submit"
                class="btn btn-valgreen">

            Registrar venta

        </button>

    </div>

</form>

@endsection