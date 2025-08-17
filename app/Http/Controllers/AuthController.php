<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User_app;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User_app::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'birth_date' => $request->birth_date,
        ]);

        return response()->json([
            'message' => 'Registro exitoso',
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User_app::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Login exitoso',
                'user'    => $user,
            ]);
        }

        return response()->json([
            'message' => 'Credenciales inválidas',
        ], 401);
    }

}
