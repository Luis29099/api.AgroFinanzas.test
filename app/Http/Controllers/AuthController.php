<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
public function updateProfile(Request $request, $id)
{
    $user = User_app::findOrFail($id);

    $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email',
        'birth_date' => 'sometimes|date',
        'profile_photo' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Subir foto si viene
    if ($request->hasFile('profile_photo')) {
         $photoName = time() . '_' . $request->file('profile_photo')->getClientOriginalName(); if ($request->hasFile('profile_photo')) {

    $uploadedFileUrl = Cloudinary::upload(
        $request->file('profile_photo')->getRealPath(),
        [
            'folder' => 'AgroFinanzas/profile_photos',
            'transformation' => [
                'width' => 300,
                'height' => 300,
                'crop' => 'fill'
            ]
        ]
    )->getSecurePath();

    $user->profile_photo = $uploadedFileUrl;
}
 // <-- ¡Añadir subcarpeta!
 }

    // actualizar otros campos
    if ($request->name) $user->name = $request->name;
    if ($request->email) $user->email = $request->email;
    if ($request->birth_date) $user->birth_date = $request->birth_date;

    $user->save();

    return response()->json([
        'message' => 'Perfil actualizado',
        'user' => $user
    ]);
}

}
