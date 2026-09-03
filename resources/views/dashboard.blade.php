@extends('layouts.app')

@section('title', 'Dashboard - Valgreen')

@section('content')

    <h1>Dashboard</h1>

    <div class="card">

        <h2>Bienvenido a Valgreen</h2>

        <br>

        <p>
            Has iniciado sesión correctamente.
        </p>

        <br>

        <p>
            Usuario:
            <strong>
                {{ auth()->user()->nombres }}
                {{ auth()->user()->primer_apellido }}
            </strong>
        </p>

    </div>

@endsection