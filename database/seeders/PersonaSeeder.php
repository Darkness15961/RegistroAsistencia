<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Grupo;
use App\Models\Area;
use Faker\Factory as Faker;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $areas = Area::all();
        $grupos = Grupo::all();

        if ($areas->isEmpty()) {
            $this->command->info('¡No hay áreas! Ejecuta AreaSeeder primero.');
            return;
        }

        $personasData = [];

        // 1. Dirección (solo 1 persona)
        $direccion = $areas->firstWhere('nombre_area', 'Dirección');
        $personasData[] = [
            'nombre_completo' => 'Carlos Gómez',
            'dni' => $faker->unique()->numerify('1#######'),
            'telefono' => $faker->phoneNumber,
            'correo' => 'direccion@colegio.com',
            'cargo_grado' => 'Director',
            'tipo_persona' => 'empleado',
            'id_area' => $direccion->id_area,
            'id_grupo' => null,
        ];

        // 2. Administración (3 empleados)
        $admin = $areas->firstWhere('nombre_area', 'Administración');
        for ($i=1; $i<=3; $i++) {
            $personasData[] = [
                'nombre_completo' => $faker->name,
                'dni' => $faker->unique()->numerify('2#######'),
                'telefono' => $faker->phoneNumber,
                'correo' => $faker->unique()->safeEmail,
                'cargo_grado' => 'Empleado Administrativo',
                'tipo_persona' => 'empleado',
                'id_area' => $admin->id_area,
                'id_grupo' => null,
            ];
        }

        // 3. Docentes de Primaria (5 docentes)
        $docentesPrimaria = $areas->firstWhere('nombre_area', 'Docentes de Primaria');
        for ($i=1; $i<=5; $i++) {
            $personasData[] = [
                'nombre_completo' => $faker->name,
                'dni' => $faker->unique()->numerify('3#######'),
                'telefono' => $faker->phoneNumber,
                'correo' => $faker->unique()->safeEmail,
                'cargo_grado' => 'Docente Primaria',
                'tipo_persona' => 'empleado',
                'id_area' => $docentesPrimaria->id_area,
                'id_grupo' => null,
            ];
        }

        // 4. Docentes de Secundaria (5 docentes)
        $docentesSec = $areas->firstWhere('nombre_area', 'Docentes de Secundaria');
        for ($i=1; $i<=5; $i++) {
            $personasData[] = [
                'nombre_completo' => $faker->name,
                'dni' => $faker->unique()->numerify('4#######'),
                'telefono' => $faker->phoneNumber,
                'correo' => $faker->unique()->safeEmail,
                'cargo_grado' => 'Docente Secundaria',
                'tipo_persona' => 'empleado',
                'id_area' => $docentesSec->id_area,
                'id_grupo' => null,
            ];
        }

        // 5. Alumnos de Inicial (10 por grupo)
        $alumnosInicial = $areas->firstWhere('nombre_area', 'Alumnos de Inicial');
        $gruposInicial = $grupos->where('id_area', $alumnosInicial->id_area);
        foreach ($gruposInicial as $grupo) {
            for ($i=1; $i<=10; $i++) {
                $personasData[] = [
                    'nombre_completo' => $faker->name,
                    'dni' => $faker->unique()->numerify('5#######'),
                    'telefono' => $faker->phoneNumber,
                    'correo' => $faker->unique()->safeEmail,
                    'cargo_grado' => 'Estudiante',
                    'tipo_persona' => 'estudiante',
                    'id_area' => $alumnosInicial->id_area,
                    'id_grupo' => $grupo->id_grupo,
                ];
            }
        }

        // 6. Alumnos de Primaria (10 por grupo)
        $alumnosPrim = $areas->firstWhere('nombre_area', 'Alumnos de Primaria');
        $gruposPrim = $grupos->where('id_area', $alumnosPrim->id_area);
        foreach ($gruposPrim as $grupo) {
            for ($i=1; $i<=10; $i++) {
                $personasData[] = [
                    'nombre_completo' => $faker->name,
                    'dni' => $faker->unique()->numerify('6#######'),
                    'telefono' => $faker->phoneNumber,
                    'correo' => $faker->unique()->safeEmail,
                    'cargo_grado' => 'Estudiante',
                    'tipo_persona' => 'estudiante',
                    'id_area' => $alumnosPrim->id_area,
                    'id_grupo' => $grupo->id_grupo,
                ];
            }
        }

        // 7. Alumnos de Secundaria (10 por grupo)
        $alumnosSec = $areas->firstWhere('nombre_area', 'Alumnos de Secundaria');
        $gruposSec = $grupos->where('id_area', $alumnosSec->id_area);
        foreach ($gruposSec as $grupo) {
            for ($i=1; $i<=10; $i++) {
                $personasData[] = [
                    'nombre_completo' => $faker->name,
                    'dni' => $faker->unique()->numerify('7#######'),
                    'telefono' => $faker->phoneNumber,
                    'correo' => $faker->unique()->safeEmail,
                    'cargo_grado' => 'Estudiante',
                    'tipo_persona' => 'estudiante',
                    'id_area' => $alumnosSec->id_area,
                    'id_grupo' => $grupo->id_grupo,
                ];
            }
        }

        // 8. Otros empleados (Tutoría, Biblioteca, Laboratorio, Coordinación Académica, Servicio Médico)
        $otrasAreas = ['Tutoría y Psicología', 'Biblioteca', 'Laboratorio', 'Coordinación Académica', 'Servicio Médico'];
        foreach ($otrasAreas as $nombreArea) {
            $area = $areas->firstWhere('nombre_area', $nombreArea);
            for ($i=1; $i<=3; $i++) { // 3 empleados por área
                $personasData[] = [
                    'nombre_completo' => $faker->name,
                    'dni' => $faker->unique()->numerify('8#######'),
                    'telefono' => $faker->phoneNumber,
                    'correo' => $faker->unique()->safeEmail,
                    'cargo_grado' => 'Empleado',
                    'tipo_persona' => 'empleado',
                    'id_area' => $area->id_area,
                    'id_grupo' => null,
                ];
            }
        }

        // Insertar todos los registros
        foreach ($personasData as $persona) {
            $persona['estado'] = 'activo';
            $persona['created_at'] = now();
            $persona['updated_at'] = now();
            DB::table('personas')->insert($persona);
        }

        $this->command->info('Se han creado '.count($personasData).' personas.');
    }
}
