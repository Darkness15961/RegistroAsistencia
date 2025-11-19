<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Persona;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios para TODOS los empleados
        $empleados = Persona::where('tipo_persona', 'empleado')
            ->whereDoesntHave('usuario') // Evitar duplicados si AdminUserSeeder ya corrió
            ->get();

        $usuariosData = [];
        $password = Hash::make('password'); // Misma pass para pruebas rápidas

        foreach ($empleados as $emp) {
            $rol = str_contains($emp->cargo_grado, 'Docente') ? 'docente' : 'empleado';
            
            $usuariosData[] = [
                'id_persona' => $emp->id_persona,
                'email' => strtolower(str_replace(' ', '.', $emp->nombre_completo)) . '@colegio.com', // juan.perez@...
                'password_hash' => $password,
                'rol' => $rol,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('usuarios')->insert($usuariosData);
    }
}