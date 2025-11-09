<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all();

        if ($areas->isEmpty()) {
            $this->command->info('⚠️ No hay áreas en la base de datos. Ejecuta primero el AreaSeeder.');
            return;
        }

        $gruposData = [];

        foreach ($areas as $area) {
            switch ($area->nombre_area) {

                case 'Alumnos de Inicial':
                    $gruposData = array_merge($gruposData, [
                        ['id_area' => $area->id_area, 'nivel' => 'Inicial', 'grado' => '4 años', 'seccion' => 'A'],
                        ['id_area' => $area->id_area, 'nivel' => 'Inicial', 'grado' => '4 años', 'seccion' => 'B'],
                        ['id_area' => $area->id_area, 'nivel' => 'Inicial', 'grado' => '5 años', 'seccion' => 'A'],
                        ['id_area' => $area->id_area, 'nivel' => 'Inicial', 'grado' => '5 años', 'seccion' => 'B'],
                    ]);
                    break;

                case 'Alumnos de Primaria':
                    foreach (['1ro', '2do', '3ro', '4to', '5to', '6to'] as $grado) {
                        $gruposData = array_merge($gruposData, [
                            ['id_area' => $area->id_area, 'nivel' => 'Primaria', 'grado' => $grado, 'seccion' => 'A'],
                            ['id_area' => $area->id_area, 'nivel' => 'Primaria', 'grado' => $grado, 'seccion' => 'B'],
                        ]);
                    }
                    break;

                case 'Alumnos de Secundaria':
                    foreach (['1ro', '2do', '3ro', '4to', '5to'] as $grado) {
                        $gruposData = array_merge($gruposData, [
                            ['id_area' => $area->id_area, 'nivel' => 'Secundaria', 'grado' => $grado, 'seccion' => 'A'],
                            ['id_area' => $area->id_area, 'nivel' => 'Secundaria', 'grado' => $grado, 'seccion' => 'B'],
                        ]);
                    }
                    break;

                case 'Docentes de Primaria':
                    $gruposData = array_merge($gruposData, [
                        ['id_area' => $area->id_area, 'nivel' => 'Docentes Primaria', 'grado' => 'N/A', 'seccion' => 'A'],
                    ]);
                    break;

                case 'Docentes de Secundaria':
                    $gruposData = array_merge($gruposData, [
                        ['id_area' => $area->id_area, 'nivel' => 'Docentes Secundaria', 'grado' => 'N/A', 'seccion' => 'A'],
                    ]);
                    break;

                default:
                    // Otras áreas administrativas o de soporte
                    $gruposData[] = [
                        'id_area' => $area->id_area,
                        'nivel' => 'General',
                        'grado' => 'N/A',
                        'seccion' => 'A'
                    ];
                    break;
            }
        }

        DB::table('grupos')->insert($gruposData);

        $this->command->info('✅ Grupos insertados correctamente.');
    }
}
