<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use App\Models\Persona;
use App\Models\Usuario;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Aseguramos que el área exista (aunque AreaSeeder ya debió crearla)
        $areaAdmin = Area::firstOrCreate(['nombre_area' => 'Administración']);

        $persona = Persona::updateOrCreate(
            ['dni' => '00000000'], // Buscamos por DNI único
            [
                'nombre_completo' => 'Super Administrador',
                'telefono' => '999000999',
                'correo' => 'admin@4scan.com',
                'cargo_grado' => 'Administrador del Sistema',
                'tipo_persona' => 'empleado',
                'id_area' => $areaAdmin->id_area,
                'estado' => 'activo'
            ]
        );

        Usuario::updateOrCreate(
            ['email' => 'admin@4scan.com'],
            [
                'id_persona' => $persona->id_persona,
                'password_hash' => Hash::make('12345678'), // Contraseña segura
                'rol' => 'administrador',
                'estado' => 'activo'
            ]
        );
    }
}