<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class AdminTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // El front admin envía el token en el header Authorization
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
        }

        $plainToken  = str_replace('Bearer ', '', $authHeader);
        $hashedToken = hash('sha256', $plainToken);

        $admin = Admin::where('token', $hashedToken)
                      ->where('is_active', true)
                      ->first();

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Token inválido o expirado.'], 401);
        }

        // Inyectar el admin en el request para usarlo en controladores
        $request->merge(['_admin' => $admin]);

        return $next($request);
    }
}