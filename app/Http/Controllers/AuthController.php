<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function mostrarLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'usuario' => 'required',
            'password' => 'required',
        ]);

        $usuario = User::where('usuario', $credenciales['usuario'])
            ->where('estado', 1)
            ->first();

        if (!$usuario || !Hash::check($credenciales['password'], $usuario->password)) {
            return back()->withErrors([
                'usuario' => 'Usuario o contraseña incorrectos.'
            ])->withInput();
        }

        Auth::login($usuario);

        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
