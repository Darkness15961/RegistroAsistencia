<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Persona;
use App\Models\Grupo;
use App\Models\Horario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $personas = Persona::all();

        if ($personas->isEmpty()) {
            $this->command->info('¡No hay personas en la BD! Ejecuta PersonaSeeder primero.');
            return;
        }

        // 1. Usuario administrador fijo
        $adminPersona = Persona::firstWhere('nombre_completo', 'Admin Sistema');

        if (!$adminPersona) {
            $adminPersona = DB::table('personas')->insertGetId([
                'nombre_completo' => 'Admin Sistema',
                'dni' => '00000000',
                'cargo_grado' => 'Administrador',
                'tipo_persona' => 'empleado',
                'id_area' => null,
                'id_grupo' => null,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminPersona = $adminPersona->id_persona;
        }

        DB::table('usuarios')->insert([
            'id_persona' => $adminPersona,
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Usuarios para líderes de grupo y jefes de área
        // Obtener docentes o empleados que sean responsables de grupos o áreas
        $personasLideres = Persona::whereIn('tipo_persona', ['empleado'])
            ->where(function($q) {
                $q->whereHas('grupo') // Lideres de grupo (docentes)
                  ->orWhereNotNull('id_area'); // Jefes de área
            })
            ->get();

        foreach ($personasLideres as $persona) {
            // Evitar duplicados si ya tiene usuario
            $existe = DB::table('usuarios')->where('id_persona', $persona->id_persona)->first();
            if ($existe) continue;

            DB::table('usuarios')->insert([
                'id_persona' => $persona->id_persona,
                'email' => strtolower(str_replace(' ', '.', $persona->nombre_completo)) . '@colegio.com',
                'password_hash' => Hash::make('password'),
                'rol' => 'empleado', // Todos los líderes son "empleado" para fines de login
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Usuarios líderes creados para docentes y jefes de área.');
    }
}
