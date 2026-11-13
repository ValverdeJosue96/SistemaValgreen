<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            'categoria_id' => 'required',
            'nueva_categoria' => 'nullable|string|max:100|required_if:categoria_id,otro',
            'nombre' => 'required|max:100',
            'descripcion' => 'nullable',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        return DB::transaction(function () use ($request, $datos) {

            // Si seleccionó "Otro", registrar la nueva categoría.
            if ($datos['categoria_id'] === 'otro') {

                $nombreCategoria = trim($datos['nueva_categoria']);

                // Evitar categorías duplicadas, incluso si están inactivas
                // o tienen borrado lógico.
                $categoriaExiste = Categoria::withTrashed()
                    ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombreCategoria)])
                    ->exists();

                if ($categoriaExiste) {
                    throw ValidationException::withMessages([
                        'nueva_categoria' => 'Esta categoría ya existe. Selecciónala en la lista o revisa su estado.',
                    ]);
                }

                $categoria = Categoria::create([
                    'nombre' => $nombreCategoria,
                    'estado' => 1,
                ]);

                $datos['categoria_id'] = $categoria->id;

            } else {

                // Comprobar que la categoría seleccionada esté activa.
                $categoria = Categoria::where('estado', 1)
                    ->find($datos['categoria_id']);

                if (!$categoria) {
                    throw ValidationException::withMessages([
                        'categoria_id' => 'La categoría seleccionada no existe o está inactiva.',
                    ]);
                }
            }

            // El campo nueva_categoria no pertenece a la tabla productos.
            unset($datos['nueva_categoria']);

            // Guardar imagen si se seleccionó una.
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
        });
    }
}
