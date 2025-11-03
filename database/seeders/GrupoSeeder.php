<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all(); // Obtener todas las áreas existentes

        if ($areas->isEmpty()) {
            $this->command->info('¡No hay áreas en la BD! Ejecuta AreaSeeder primero.');
            return;
        }

        $gruposData = [
            ['nivel' => 'Senior', 'grado' => 'N/A', 'seccion' => 'A'],
            ['nivel' => 'Senior', 'grado' => 'N/A', 'seccion' => 'B'],
            ['nivel' => 'Junior', 'grado' => 'N/A', 'seccion' => 'C'],
            ['nivel' => 'Mid', 'grado' => 'N/A', 'seccion' => 'D'],
            ['nivel' => 'Junior', 'grado' => 'N/A', 'seccion' => 'E'],
            ['nivel' => 'Senior', 'grado' => 'N/A', 'seccion' => 'F'],
        ];

        foreach ($gruposData as $grupoData) {
            $grupoData['id_area'] = $areas->random()->id_area; // FK aleatoria
            DB::table('grupos')->insert($grupoData);
        }
    }
}
