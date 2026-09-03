@extends('layouts.app')

@section('title', 'Movimiento de Stock - Valgreen')

@section('content')

    <h1>Registrar movimiento de stock</h1>

    <div class="card">

        <form method="POST" action="/stock">

            @csrf

            <label>
                Producto
            </label>

            <select name="producto_id" required>

                <option value="">
                    Seleccione un producto
                </option>

                @foreach($productos as $producto)

                    <option value="{{ $producto->id }}">
                        {{ $producto->nombre }}
                    </option>

                @endforeach

            </select>


            <label>
                Tipo de movimiento
            </label>

            <select name="tipo_movimiento_id" required>

                <option value="">
                    Seleccione
                </option>

                @foreach($tipos as $tipo)

                    <option value="{{ $tipo->id }}">
                        {{ $tipo->nombre }}
                    </option>

                @endforeach

            </select>


            <label>
                Cantidad
            </label>

            <input
                type="number"
                name="cantidad"
                min="1"
                required
            >


            <label>
                Motivo
            </label>

            <textarea name="motivo"></textarea>


            <button type="submit" class="btn">
                Registrar movimiento
            </button>

        </form>

    </div>

@endsection