<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function mostrarFormulario()
    {
        return view('auth.forgot-password');
    }

    public function enviarEnlace(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Te enviamos un enlace para restablecer tu contraseña.');
        }

        return back()->withErrors([
            'email' => 'No encontramos un usuario con ese correo electrónico.'
        ]);
    }

    public function mostrarFormularioReset(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function restablecer(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('/login')
                ->with('status', 'Tu contraseña fue restablecida correctamente.');
        }

        return back()->withErrors([
            'email' => 'No se pudo restablecer la contraseña. El enlace puede haber expirado.'
        ]);
    }
}