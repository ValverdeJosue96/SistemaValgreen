@extends('layouts.app')

@section('title', 'Clientes - Valgreen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold mb-1">Clientes</h1>
        <p class="text-muted mb-0">
            Registro y gestión de clientes
        </p>
    </div>

    <a href="/clientes/create"
       class="btn btn-valgreen">

        + Nuevo cliente

    </a>

</div>


@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif


<div class="card shadow-sm border-0">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>N.º</th>
                        <th>Nombre completo</th>
                        <th>Carnet</th>
                        <th>Teléfono</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($clientes as $cliente)

                        <tr>

                            <td>
                                #{{ $cliente->id }}
                            </td>

                            <td>

                                <strong>

                                    {{ $cliente->nombres }}
                                    {{ $cliente->primer_apellido }}
                                    {{ $cliente->segundo_apellido }}

                                </strong>

                            </td>

                            <td>
                                {{ $cliente->carnet }}
                            </td>

                            <td>
                                {{ $cliente->telefono }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted py-4">

                                No existen clientes registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection