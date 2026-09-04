@extends('layouts.app')

@section('title', 'Registrar producto - Valgreen')

@section('content')

    <div class="mb-4">

        <h1 class="fw-bold">
            Registrar producto
        </h1>

        <p class="text-muted">
            Agrega un nuevo producto al catálogo.
        </p>

    </div>


    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form method="POST"
            action="/productos"
            enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Categoría
                        </label>

                        <select name="categoria_id"
                                class="form-select"
                                required>

                            <option value="">
                                Seleccione una categoría
                            </option>

                            @foreach($categorias as $categoria)

                                <option value="{{ $categoria->id }}">

                                    {{ $categoria->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nombre del producto
                        </label>

                        <input type="text"
                               name="nombre"
                               class="form-control"
                               placeholder="Ej. Torta de chocolate"
                               required>

                    </div>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Descripción
                    </label>

                    <textarea name="descripcion"
                              class="form-control"
                              rows="3"
                              placeholder="Descripción del producto"></textarea>

                </div>


                <div class="col-md-4 mb-4">

                    <label class="form-label">
                        Precio
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            Bs
                        </span>

                        <input type="number"
                               name="precio"
                               class="form-control"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               required>

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Imagen del producto
                    </label>

                    <input
                        type="file"
                        name="imagen"
                        class="form-control"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                    >

                    <div class="form-text">
                        Formatos permitidos: JPG, JPEG, PNG y WEBP.
                        Tamaño máximo: 2 MB.
                    </div>

                </div>


                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-valgreen">

                        Guardar producto

                    </button>

                    <a href="/productos"
                       class="btn btn-outline-secondary">

                        Cancelar

                    </a>

                </div>

            </form>

        </div>

    </div>

@endsection