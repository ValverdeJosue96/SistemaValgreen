@extends('layouts.app')

@section('title', 'Usuarios - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold mb-1">Usuarios</h1>
        <p class="text-muted mb-0">
            Gestión de usuarios del sistema
        </p>
    </div>

    <a href="/usuarios/create" class="btn btn-valgreen">
        + Nuevo usuario
    </a>

</div>

@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif

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

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>N.º</th>
                        <th>Nombre</th>
                        <th>CI</th>
                        <th>Teléfono</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($usuarios as $usuario)

                        <tr>

                            <td>
                                {{ $usuario->id }}
                            </td>

                            <td>
                                {{ $usuario->nombres }}
                                {{ $usuario->primer_apellido }}
                                {{ $usuario->segundo_apellido }}
                            </td>

                            <td>
                                {{ $usuario->carnet }}
                            </td>

                            <td>
                                {{ $usuario->telefono ?? '—' }}
                            </td>

                            <td>
                                {{ $usuario->usuario }}
                            </td>

                            <td>
                                {{ $usuario->rol->nombre }}
                            </td>

                            <td>

                                @if($usuario->estado)

                                    <span class="badge text-bg-success">
                                        Activo
                                    </span>

                                @else

                                    <span class="badge text-bg-secondary">
                                        Inactivo
                                    </span>

                                @endif

                            </td>

                            <td>

                                <a href="/usuarios/{{ $usuario->id }}/edit"
                                   class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST"
                                      action="/usuarios/{{ $usuario->id }}/estado"
                                      class="d-inline">

                                    @csrf
                                    @method('PUT')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-secondary">

                                        {{ $usuario->estado ? 'Desactivar' : 'Activar' }}

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center text-muted py-4">

                                No hay usuarios registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection