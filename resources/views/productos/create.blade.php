
@extends('layouts.app')

@section('title', 'Registrar producto - Valgreen')

@section('content')

    <div class="mb-4">
        <h1 class="fw-bold">Registrar producto</h1>
        <p class="text-muted">Agrega un nuevo producto al catálogo.</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form method="POST"
                  action="/productos"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Categoría</label>

                        <select name="categoria_id"
                                id="categoria_id"
                                class="form-select @error('categoria_id') is-invalid @enderror"
                                required>

                            <option value="">Seleccione una categoría</option>

                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach

                            <option value="otro"
                                {{ old('categoria_id') == 'otro' ? 'selected' : '' }}>
                                Otro
                            </option>

                        </select>

                        @error('categoria_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3"
                         id="contenedor_nueva_categoria"
                         style="{{ old('categoria_id') == 'otro' ? '' : 'display:none;' }}">

                        <label class="form-label">Nombre de la nueva categoría</label>

                        <input type="text"
                               name="nueva_categoria"
                               id="nueva_categoria"
                               class="form-control @error('nueva_categoria') is-invalid @enderror"
                               placeholder="Ej. Tortas personalizadas"
                               value="{{ old('nueva_categoria') }}">

                        @error('nueva_categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del producto</label>

                        <input type="text"
                               name="nombre"
                               class="form-control"
                               placeholder="Ej. Torta de chocolate"
                               value="{{ old('nombre') }}"
                               required>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>

                    <textarea name="descripcion"
                              class="form-control"
                              rows="3"
                              placeholder="Descripción del producto">{{ old('descripcion') }}</textarea>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label">Precio</label>

                    <div class="input-group">
                        <span class="input-group-text">Bs</span>

                        <input type="number"
                               name="precio"
                               class="form-control"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               value="{{ old('precio') }}"
                               required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Imagen del producto</label>

                    <input type="file"
                           name="imagen"
                           class="form-control"
                           accept="image/jpeg,image/png,image/jpg,image/webp">

                    <div class="form-text">
                        Formatos permitidos: JPG, JPEG, PNG y WEBP.
                        Tamaño máximo: 2 MB.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-valgreen">
                        Guardar producto
                    </button>

                    <a href="/productos" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = document.getElementById('categoria_id');
            const contenedor = document.getElementById('contenedor_nueva_categoria');
            const campo = document.getElementById('nueva_categoria');

            function actualizarCampoCategoria() {
                const esOtra = selector.value === 'otro';

                contenedor.style.display = esOtra ? 'block' : 'none';
                campo.required = esOtra;

                if (!esOtra) {
                    campo.value = '';
                }
            }

            selector.addEventListener('change', actualizarCampoCategoria);
            actualizarCampoCategoria();
        });
    </script>

@endsection
