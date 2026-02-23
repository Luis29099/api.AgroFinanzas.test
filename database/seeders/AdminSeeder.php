<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ Cambia estos datos antes de ejecutar en producción
        Admin::create([
            'name'      => 'Administrador',
            'email'     => 'admin@agrofinanzas.com',
            'password'  => Hash::make('Admin2024$Seguro'),
            'is_active' => true,
        ]);

        $this->command->info('✅ Admin creado: admin@agrofinanzas.com');
        $this->command->warn('⚠️  Cambia la contraseña después del primer login.');
    }
}