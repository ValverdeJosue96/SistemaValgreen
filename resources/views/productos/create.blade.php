<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar producto</title>
</head>
<body>

<h1>Registrar producto</h1>

<form method="POST" action="/productos">
    @csrf

    <label>Categoría:</label>

    <select name="categoria_id" required>

        <option value="">Seleccione</option>

        @foreach($categorias as $categoria)

            <option value="{{ $categoria->id }}">
                {{ $categoria->nombre }}
            </option>

        @endforeach

    </select>

    <br><br>

    <label>Nombre:</label>

    <input type="text" name="nombre" required>

    <br><br>

    <label>Descripción:</label>

    <textarea name="descripcion"></textarea>

    <br><br>

    <label>Precio:</label>

    <input type="number"
           name="precio"
           step="0.01"
           min="0"
           required>

    <br><br>

    <button type="submit">
        Guardar producto
    </button>

</form>

</body>
</html>