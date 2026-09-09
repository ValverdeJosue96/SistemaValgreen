@extends('layouts.app')

@section('title', 'Nuevo cliente - Valgreen')

@section('content')

<div class="mb-4">

    <h1 class="fw-bold">
        Nuevo cliente
    </h1>

    <p class="text-muted">
        Registra los datos del cliente.
    </p>

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

        <form method="POST"
              action="/clientes">

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


                <div class="col-md-6">

                    <label class="form-label">
                        Carnet de identidad
                    </label>

                    <input type="text"
                           name="carnet"
                           class="form-control"
                           value="{{ old('carnet') }}"
                           required>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Teléfono
                    </label>

                    <input type="text"
                           name="telefono"
                           class="form-control"
                           value="{{ old('telefono') }}"
                           required>

                </div>

            </div>


            <div class="d-flex justify-content-end gap-2 mt-4">

                <a href="/clientes"
                   class="btn btn-secondary">

                    Cancelar

                </a>

                <button type="submit"
                        class="btn btn-valgreen">

                    Registrar cliente

                </button>

            </div>

        </form>

    </div>

</div>

@endsection