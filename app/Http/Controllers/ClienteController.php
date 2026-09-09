<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('id', 'desc')->get();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'required|string|max:50',
            'carnet' => 'required|string|max:20|unique:clientes,carnet',
            'telefono' => 'required|string|max:20',
        ]);

        Cliente::create([
            'nombres' => $request->nombres,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'carnet' => $request->carnet,
            'telefono' => $request->telefono,
        ]);

        return redirect('/clientes')
            ->with('success', 'Cliente registrado correctamente.');
    }
}