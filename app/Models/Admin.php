<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'token',
        'last_login',
        'is_active',
    ];

    protected $hidden = ['password', 'token'];

    protected $casts = [
        'last_login' => 'datetime',
        'is_active'  => 'boolean',
    ];

    // Verificar contraseña
    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    // Generar nuevo token de sesión
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(40));
        $this->update([
            'token'      => hash('sha256', $token),
            'last_login' => now(),
        ]);
        return $token; // Se devuelve el token plano, se guarda el hash
    }

    // Revocar token
    public function revokeToken(): void
    {
        $this->update(['token' => null]);
    }
}