<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- IMPORTANTE: Importar Schema
use App\Models\Area;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all();

      
        Schema::disableForeignKeyConstraints();
        DB::table('grupos')->truncate();
        Schema::enableForeignKeyConstraints();

        $gruposData = [];

        foreach ($areas as $area) {
            // --- GRUPOS DE ALUMNOS ---
            if ($area->nombre_area === 'Alumnos de Inicial') {
                $this->addGrupo($gruposData, $area->id_area, 'Inicial', '4 años', 'A');
                $this->addGrupo($gruposData, $area->id_area, 'Inicial', '5 años', 'A');
            }
            elseif ($area->nombre_area === 'Alumnos de Primaria') {
                foreach (['1ro', '2do', '3ro', '4to', '5to', '6to'] as $grado) {
                    $this->addGrupo($gruposData, $area->id_area, 'Primaria', $grado, 'A');
                    $this->addGrupo($gruposData, $area->id_area, 'Primaria', $grado, 'B');
                }
            }
            elseif ($area->nombre_area === 'Alumnos de Secundaria') {
                foreach (['1ro', '2do', '3ro', '4to', '5to'] as $grado) {
                    $this->addGrupo($gruposData, $area->id_area, 'Secundaria', $grado, 'A');
                    $this->addGrupo($gruposData, $area->id_area, 'Secundaria', $grado, 'B');
                }
            }
            
            // --- GRUPOS DE PERSONAL ---
            elseif (str_contains($area->nombre_area, 'Docentes')) {
                $especialidad = str_contains($area->nombre_area, 'Primaria') ? 'General' : 'Ciencias y Letras';
                $this->addGrupo($gruposData, $area->id_area, 'Gestión de Docentes', 'Plana ' . $especialidad, 'Mañana');
            }
            elseif ($area->nombre_area === 'Mantenimiento y Limpieza') {
                $this->addGrupo($gruposData, $area->id_area, 'Administrativos', 'Equipo de Limpieza', 'Rotativo');
                $this->addGrupo($gruposData, $area->id_area, 'Administrativos', 'Equipo de Mantenimiento', 'Rotativo');
            }
            else {
                $this->addGrupo($gruposData, $area->id_area, 'Administrativos', 'Equipo ' . $area->nombre_area, 'Día');
            }
        }

        DB::table('grupos')->insert($gruposData);
    }

    private function addGrupo(&$array, $idArea, $nivel, $grado, $seccion) {
        $array[] = [
            'id_area' => $idArea,
            'nivel' => $nivel,
            'grado' => $grado,
            'seccion' => $seccion,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}