<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('rol')
            ->orderBy('id', 'desc')
            ->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::orderBy('id')->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'nombres' => 'required|string|max:100',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'required|string|max:50',
            'carnet' => 'required|string|max:20|unique:usuarios,carnet',
            'telefono' => 'nullable|string|max:20',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'rol_id' => $request->rol_id,
            'nombres' => $request->nombres,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'carnet' => $request->carnet,
            'telefono' => $request->telefono,
            'usuario' => $request->usuario,
            'password' => Hash::make($request->password),
            'estado' => 1,
        ]);

        return redirect('/usuarios')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles = Rol::orderBy('id')->get();

        return view('usuarios.edit', compact(
            'usuario',
            'roles'
        ));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'rol_id' => 'required|exists:roles,id',
            'nombres' => 'required|string|max:100',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'required|string|max:50',
            'carnet' => 'required|string|max:20|unique:usuarios,carnet,' . $id,
            'telefono' => 'nullable|string|max:20',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $datos = [
            'rol_id' => $request->rol_id,
            'nombres' => $request->nombres,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'carnet' => $request->carnet,
            'telefono' => $request->telefono,
            'usuario' => $request->usuario,
        ];

        // Solo cambia la contraseña si se escribió una nueva.
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect('/usuarios')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function cambiarEstado($id)
    {
        $usuario = User::findOrFail($id);

        $usuario->update([
            'estado' => $usuario->estado ? 0 : 1,
        ]);

        return redirect('/usuarios')
            ->with('success', 'Estado del usuario actualizado correctamente.');
    }
}