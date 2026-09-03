@extends('layouts.app')

@section('title', 'Registrar producto - Valgreen')

@section('content')

    <h1>Registrar producto</h1>

    <div class="card">

        <form method="POST" action="/productos">

            @csrf

            <label>
                Categoría
            </label>

            <select name="categoria_id" required>

                <option value="">
                    Seleccione una categoría
                </option>

                @foreach($categorias as $categoria)

                    <option value="{{ $categoria->id }}">
                        {{ $categoria->nombre }}
                    </option>

                @endforeach

            </select>


            <label>
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                required
            >


            <label>
                Descripción
            </label>

            <textarea name="descripcion"></textarea>


            <label>
                Precio
            </label>

            <input
                type="number"
                name="precio"
                step="0.01"
                min="0"
                required
            >


            <button type="submit" class="btn">
                Guardar producto
            </button>

        </form>

    </div>

@endsection