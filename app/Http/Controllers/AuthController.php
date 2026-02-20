<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class AuthController extends Controller
{
    // ── REGISTRO ──────────────────────────────────────────────
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|min:3|max:100',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
            'birth_date'       => 'required|date|before:today',
            'phone'            => 'nullable|string|max:20',
            'gender'           => 'nullable|in:male,female,other',
            'experience_years' => 'nullable|integer|min:0|max:70',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'name.min'           => 'El nombre debe tener al menos 3 caracteres.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no tiene un formato válido.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'birth_date.required'=> 'La fecha de nacimiento es obligatoria.',
            'birth_date.before'  => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name'                    => $request->name,
            'email'                   => $request->email,
            'password'                => Hash::make($request->password),
            'birth_date'              => $request->birth_date,
            'phone'                   => $request->phone,
            'gender'                  => $request->gender,
            'experience_years'        => $request->experience_years,
            'verification_code'       => $code,
            'verification_expires_at' => now()->addMinutes(15),
            'is_verified'             => false,
        ]);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
        } catch (\Exception $e) {
            Log::error('Error enviando correo de verificación: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso. Revisa tu correo para verificar tu cuenta.',
            'user_id' => $user->id,
            'email'   => $user->email,
        ], 201);
    }

    // ── VERIFICAR CÓDIGO ──────────────────────────────────────
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'code'    => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($request->user_id);

        if ($user->is_verified) {
            return response()->json(['success' => true, 'message' => 'La cuenta ya estaba verificada.', 'user' => $user]);
        }

        if (now()->isAfter($user->verification_expires_at)) {
            return response()->json(['success' => false, 'message' => 'El código ha expirado. Solicita uno nuevo.', 'expired' => true], 422);
        }

        if ($user->verification_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'El código ingresado es incorrecto.'], 422);
        }

        $user->update([
            'is_verified'             => true,
            'verification_code'       => null,
            'verification_expires_at' => null,
        ]);

        return response()->json(['success' => true, 'message' => '¡Cuenta verificada exitosamente!', 'user' => $user]);
    }

    // ── REENVIAR CÓDIGO ───────────────────────────────────────
    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($request->user_id);

        if ($user->is_verified) {
            return response()->json(['success' => false, 'message' => 'Esta cuenta ya está verificada.'], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'verification_code'       => $code,
            'verification_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
        } catch (\Exception $e) {
            Log::error('Error reenviando código: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo enviar el correo.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Código reenviado exitosamente.']);
    }

    // ── ENVIAR CÓDIGO PARA ELIMINAR CUENTA (solo verificados) ─
    public function sendDeleteCode(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_verified) {
            return response()->json(['success' => false, 'message' => 'Esta cuenta no está verificada.'], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'verification_code'       => $code,
            'verification_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($code, $user->name));
        } catch (\Exception $e) {
            Log::error('Error enviando código de eliminación: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo enviar el correo.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Código enviado a tu correo para confirmar la eliminación.']);
    }

    // ── ELIMINAR CUENTA ───────────────────────────────────────
    public function deleteAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cuenta NO verificada → eliminar directo sin código
        if (!$user->is_verified) {
            // Los comentarios quedan como anónimos (set null en user_id)
            $user->delete();
            return response()->json(['success' => true, 'message' => 'Cuenta eliminada.']);
        }

        // Cuenta verificada → requiere código de confirmación
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'El código de confirmación es obligatorio.',
            'code.size'     => 'El código debe tener 6 dígitos.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }

        // Verificar expiración
        if (now()->isAfter($user->verification_expires_at)) {
            return response()->json(['success' => false, 'message' => 'El código expiró. Solicita uno nuevo.', 'expired' => true], 422);
        }

        // Verificar código
        if ($user->verification_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'El código es incorrecto.'], 422);
        }

        // ✅ Eliminar — comentarios quedan con user_id = null (anónimos)
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Cuenta eliminada correctamente.']);
    }

    // ── LOGIN ─────────────────────────────────────────────────
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Formato de correo inválido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Credenciales inválidas.'], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'success'      => false,
                'message'      => 'Debes verificar tu cuenta antes de ingresar.',
                'not_verified' => true,
                'user_id'      => $user->id,
                'email'        => $user->email,
            ], 403);
        }

        return response()->json(['success' => true, 'message' => 'Login exitoso', 'user' => $user]);
    }

    // ── ACTUALIZAR PERFIL ─────────────────────────────────────
    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'             => 'nullable|string|min:3|max:100',
            'email'            => 'nullable|email|unique:users,email,' . $id,
            'birth_date'       => 'nullable|date|before:today',
            'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'phone'            => 'nullable|string|max:20',
            'gender'           => 'nullable|in:male,female,other',
            'experience_years' => 'nullable|integer|min:0|max:70',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('profile_photo')) {
            try {
                $file = $request->file('profile_photo');
                if (!$file->isValid()) {
                    return response()->json(['success' => false, 'message' => 'Archivo inválido.'], 422);
                }
                $cloudinary = new Cloudinary(Configuration::instance([
                    'cloud' => ['cloud_name' => env('CLOUDINARY_CLOUD_NAME'), 'api_key' => env('CLOUDINARY_API_KEY'), 'api_secret' => env('CLOUDINARY_API_SECRET')],
                    'url'   => ['secure' => true],
                ]));
                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'AgroFinanzas/profile_photos',
                    'transformation' => ['width' => 300, 'height' => 300, 'crop' => 'fill'],
                ]);
                $user->profile_photo = $result['secure_url'];
            } catch (\Exception $e) {
                Log::error('Cloudinary error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error al subir imagen: ' . $e->getMessage()], 500);
            }
        }

        $campos = ['name', 'email', 'birth_date', 'phone', 'gender', 'experience_years'];
        foreach ($campos as $campo) {
            $valor = $request->input($campo);
            if (!is_null($valor) && $valor !== '') {
                $user->$campo = $valor;
            }
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Perfil actualizado', 'user' => $user]);
    }
}