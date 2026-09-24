<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $usuario = auth()->user();

        // Si no hay usuario autenticado
        if (!$usuario) {
            return redirect('/login');
        }

        // Obtener el nombre del rol
        $rol = $usuario->rol ? $usuario->rol->nombre : null;

        // Verificar si el rol está permitido
        if (!in_array($rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}