@extends('layouts.app')

@section('title', 'Nuevo usuario - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold mb-1">Nuevo usuario</h1>
        <p class="text-muted mb-0">
            Registrar un usuario del sistema
        </p>
    </div>

    <a href="/usuarios" class="btn btn-secondary">
        Volver
    </a>

</div>

@if($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

        </ul>

    </div>

@endif

<div class="card shadow-sm border-0">

    <div class="card-body">

        <form method="POST" action="/usuarios">

            @csrf

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Nombres
                    </label>

                    <input type="text"
                           name="nombres"
                           class="form-control"
                           value="{{ old('nombres') }}"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">
                        Primer apellido
                    </label>

                    <input type="text"
                           name="primer_apellido"
                           class="form-control"
                           value="{{ old('primer_apellido') }}"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">
                        Segundo apellido
                    </label>

                    <input type="text"
                           name="segundo_apellido"
                           class="form-control"
                           value="{{ old('segundo_apellido') }}"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">
                        Carnet
                    </label>

                    <input type="text"
                           name="carnet"
                           class="form-control"
                           value="{{ old('carnet') }}"
                           required>

                </div>

                <div class="col-md-4">

                    <label class="form-label">
                        Teléfono
                    </label>

                    <input type="text"
                           name="telefono"
                           class="form-control"
                           value="{{ old('telefono') }}">

                </div>

                <div class="col-md-4">

                    <label class="form-label">
                        Rol
                    </label>

                    <select name="rol_id"
                            class="form-select"
                            required>

                        <option value="">
                            Seleccione un rol
                        </option>

                        @foreach($roles as $rol)

                            <option value="{{ $rol->id }}"
                                {{ old('rol_id') == $rol->id ? 'selected' : '' }}>

                                {{ $rol->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Usuario
                    </label>

                    <input type="text"
                           name="usuario"
                           class="form-control"
                           value="{{ old('usuario') }}"
                           required>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Contraseña
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control"
                           required>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Confirmar contraseña
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           required>

                </div>

            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">

                <a href="/usuarios"
                   class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit"
                        class="btn btn-valgreen">
                    Registrar usuario
                </button>

            </div>

        </form>

    </div>

</div>

@endsection