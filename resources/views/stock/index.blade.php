<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock - Valgreen</title>
</head>
<body>

<h1>Control de Stock</h1>

<a href="/dashboard">Inicio</a>

<br><br>

<a href="/stock/create">
    Registrar movimiento
</a>

<br><br>

@if(session('success'))
    <p>{{ session('success') }}</p>
@endif

<table border="1" cellpadding="8">

    <thead>
        <tr>
            <th>Producto</th>
            <th>Stock actual</th>
        </tr>
    </thead>

    <tbody>

    @foreach($productos as $producto)

        <tr>
            <td>{{ $producto->nombre }}</td>

            <td>
                {{ $producto->stock->cantidad ?? 0 }}
            </td>
        </tr>

    @endforeach

    </tbody>

</table>

</body>
</html>