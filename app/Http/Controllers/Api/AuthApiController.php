<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::with('rol')
            ->where('email', $credenciales['email'])
            ->where('estado', 1)
            ->first();

        if (
            !$usuario ||
            !Hash::check($credenciales['password'], $usuario->password)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Correo electrónico o contraseña incorrectos.',
            ], 401);
        }

        $token = $usuario->createToken('valgreen-movil')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión correcto.',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombres' => $usuario->nombres,
                'primer_apellido' => $usuario->primer_apellido,
                'segundo_apellido' => $usuario->segundo_apellido,
                'email' => $usuario->email,
                'rol' => $usuario->rol?->nombre,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}