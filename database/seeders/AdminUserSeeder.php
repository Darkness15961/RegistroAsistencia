<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use App\Models\Persona;
use App\Models\Usuario;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            
            // 1. Encontrar o crear el Área (esto estaba bien)
            $areaAdmin = Area::firstOrCreate(
                ['nombre_area' => 'Administración'],
                ['descripcion' => 'Gestión del sistema y panel de control']
            );

            // 2. Encontrar o Preparar la Persona (MÉTODO CORREGIDO)
            $personaAdmin = Persona::firstOrNew(
                ['correo' => 'admin@4scan.com'] // Buscar por este correo
            );

            // Si la persona NO existe, la llenamos con datos y la guardamos
            if (!$personaAdmin->exists) {
                $personaAdmin->nombre_completo = 'Administrador del Sistema';
                $personaAdmin->dni = '00000000';
                $personaAdmin->telefono = '999888777';
                $personaAdmin->cargo_grado = 'Administrador';
                $personaAdmin->tipo_persona = 'empleado';
                $personaAdmin->id_area = $areaAdmin->id_area;
                $personaAdmin->estado = 'activo';
                $personaAdmin->save(); // <-- Forzamos el guardado
            }

            // 3. Encontrar o Preparar el Usuario (MÉTODO CORREGIDO)
            $usuarioAdmin = Usuario::firstOrNew(
                ['email' => 'admin@4scan.com'] // Buscar por este email
            );
            
            // Si el usuario NO existe, lo llenamos con datos y lo guardamos
            if (!$usuarioAdmin->exists) {
                $usuarioAdmin->id_persona = $personaAdmin->id_persona; // <-- Ahora SÍ tenemos un ID
                $usuarioAdmin->password_hash = Hash::make('123456'); // Tu contraseña
                $usuarioAdmin->rol = 'admin';
                $usuarioAdmin->estado = 'activo';
                $usuarioAdmin->save(); // <-- Forzamos el guardado
            }
        });
    }
}