<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- Importar
use App\Models\Grupo;
use Faker\Factory as Faker;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('personas')->truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Faker::create('es_PE');
        $grupos = Grupo::with('area')->get();
        $personasData = [];

        // ... (El resto del código de generación sigue igual que te pasé antes) ...
        foreach ($grupos as $grupo) {
            $nombreArea = $grupo->area->nombre_area;
            
            if (str_contains($nombreArea, 'Alumnos')) {
                $tipo = 'estudiante';
                $cargo = 'Estudiante';
                $cantidad = 15;
            } else {
                $tipo = 'empleado';
                $cargo = str_contains($nombreArea, 'Docente') ? 'Docente' : 'Personal Administrativo';
                $cantidad = 3;
            }

            for ($i = 0; $i < $cantidad; $i++) {
                $personasData[] = [
                    'nombre_completo' => $faker->firstName . ' ' . $faker->lastName . ' ' . $faker->lastName,
                    'dni' => $faker->unique()->numerify('########'),
                    'telefono' => $faker->phoneNumber,
                    'correo' => $faker->unique()->safeEmail,
                    'cargo_grado' => $cargo,
                    'tipo_persona' => $tipo,
                    'id_area' => $grupo->id_area,
                    'id_grupo' => $grupo->id_grupo,
                    'estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        foreach (array_chunk($personasData, 50) as $chunk) {
            DB::table('personas')->insert($chunk);
        }
    }
}