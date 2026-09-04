<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')
            ->where('estado', 1)
            ->get();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 1)->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
{
    $datos = $request->validate([
        'categoria_id' => 'required|exists:categorias,id',
        'nombre' => 'required|max:100',
        'descripcion' => 'nullable',
        'precio' => 'required|numeric|min:0',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // Guardar la imagen si se seleccionó una
    if ($request->hasFile('imagen')) {

        $ruta = $request->file('imagen')
            ->store('productos', 'public');

        $datos['imagen'] = $ruta;
    }

    $datos['estado_producto_id'] = 1;
    $datos['estado'] = 1;

    Producto::create($datos);

    return redirect('/productos')
        ->with('success', 'Producto registrado correctamente.');
}
}