<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    // POST /api/admin/login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)
                      ->where('is_active', true)
                      ->first();

        if (!$admin || !$admin->verifyPassword($request->password)) {
            Log::warning('Admin login fallido', ['email' => $request->email, 'ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Credenciales incorrectas.'], 401);
        }

        $token = $admin->generateToken();

        Log::info('Admin login exitoso', ['admin_id' => $admin->id, 'email' => $admin->email]);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'token'   => $token,
            'admin'   => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    // POST /api/admin/logout
    public function logout(Request $request)
    {
        $admin = $request->get('_admin');
        if ($admin) {
            $admin->revokeToken();
            Log::info('Admin logout', ['admin_id' => $admin->id]);
        }

        return response()->json(['success' => true, 'message' => 'Sesión cerrada.']);
    }

    // GET /api/admin/me
    public function me(Request $request)
    {
        $admin = $request->get('_admin');
        return response()->json(['success' => true, 'admin' => $admin]);
    }
}