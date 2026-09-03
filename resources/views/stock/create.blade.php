<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimiento de Stock</title>
</head>
<body>

<h1>Registrar movimiento de stock</h1>

<form method="POST" action="/stock">

    @csrf

    <label>Producto:</label>

    <select name="producto_id" required>

        <option value="">Seleccione</option>

        @foreach($productos as $producto)

            <option value="{{ $producto->id }}">
                {{ $producto->nombre }}
            </option>

        @endforeach

    </select>

    <br><br>

    <label>Tipo de movimiento:</label>

    <select name="tipo_movimiento_id" required>

        <option value="">Seleccione</option>

        @foreach($tipos as $tipo)

            <option value="{{ $tipo->id }}">
                {{ $tipo->nombre }}
            </option>

        @endforeach

    </select>

    <br><br>

    <label>Cantidad:</label>

    <input type="number"
           name="cantidad"
           min="1"
           required>

    <br><br>

    <label>Motivo:</label>

    <textarea name="motivo"></textarea>

    <br><br>

    <button type="submit">
        Registrar movimiento
    </button>

</form>

</body>
</html>