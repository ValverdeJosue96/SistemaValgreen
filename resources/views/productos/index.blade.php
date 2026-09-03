<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Valgreen</title>
</head>
<body>

<h1>Productos</h1>

<a href="/dashboard">Inicio</a>

<br><br>

<a href="/productos/create">
    Registrar producto
</a>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
        </tr>
    </thead>

    <tbody>

    @foreach($productos as $producto)

        <tr>
            <td>{{ $producto->nombre }}</td>

            <td>
                {{ $producto->categoria->nombre }}
            </td>

            <td>
                Bs {{ $producto->precio }}
            </td>
        </tr>

    @endforeach

    </tbody>
</table>

</body>
</html>