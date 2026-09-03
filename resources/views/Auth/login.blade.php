<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Valgreen</title>
</head>
<body>

    <h1>Valgreen</h1>
    <h2>Iniciar sesión</h2>

    @if ($errors->any())
        <div>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <label>Usuario:</label>
        <input type="text" name="usuario" value="{{ old('usuario') }}" required>

        <br><br>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Ingresar</button>
    </form>

</body>
</html>