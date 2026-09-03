<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Valgreen</title>
</head>
<body>

    <h1>Bienvenido a Valgreen</h1>

    <p>
        Usuario:
        {{ auth()->user()->nombres }}
        {{ auth()->user()->primer_apellido }}
    </p>

    <p>
        Has iniciado sesión correctamente.
    </p>

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>

</body>
</html>