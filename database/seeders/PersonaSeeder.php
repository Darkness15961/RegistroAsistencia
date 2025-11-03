<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Grupo;
use App\Models\Area;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all();
        $grupos = Grupo::all();

        if ($areas->isEmpty()) {
            $this->command->info('¡No hay áreas! Ejecuta AreaSeeder primero.');
            return;
        }

        $personasData = [
            [
                'nombre_completo' => 'Juan Pérez',
                'dni' => '12345678',
                'telefono' => '987654321',
                'correo' => 'juan@example.com',
                'cargo_grado' => 'Empleado',
                'tipo_persona' => 'empleado',
                'id_area' => $areas->random()->id_area,
                'id_grupo' => null,
            ],
            [
                'nombre_completo' => 'María López',
                'dni' => '87654321',
                'telefono' => '912345678',
                'correo' => 'maria@example.com',
                'cargo_grado' => 'Estudiante',
                'tipo_persona' => 'estudiante',
                'id_area' => $areas->random()->id_area,
                'id_grupo' => $grupos->isNotEmpty() ? $grupos->random()->id_grupo : null,
            ],
            // Puedes añadir más registros aquí
        ];

        foreach ($personasData as $persona) {
            $persona['estado'] = 'activo';
            $persona['created_at'] = now();
            $persona['updated_at'] = now();
            DB::table('personas')->insert($persona);
        }
    }
}
