<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- Importar Schema
use App\Models\Area;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all();
        
        // Desactivar restricciones antes de limpiar
        Schema::disableForeignKeyConstraints();
        DB::table('horarios')->truncate();
        Schema::enableForeignKeyConstraints();

        foreach ($areas as $area) {
            $nombre = $area->nombre_area;

            if (str_contains($nombre, 'Alumnos')) {
                $this->crearHorario($area->id_area, '07:45:00', '13:00:00', 'Mañana');
                $this->crearHorario($area->id_area, '13:15:00', '18:30:00', 'Tarde');
            } 
            elseif (str_contains($nombre, 'Docentes')) {
                $this->crearHorario($area->id_area, '07:30:00', '14:00:00', 'Mañana');
            } 
            elseif (in_array($nombre, ['Dirección', 'Administración', 'Coordinación Académica', 'Tutoría y Psicología'])) {
                $this->crearHorario($area->id_area, '08:00:00', '17:00:00', 'Día');
            } 
            elseif ($nombre === 'Seguridad') {
                $this->crearHorario($area->id_area, '07:00:00', '19:00:00', 'Día');
                $this->crearHorario($area->id_area, '19:00:00', '07:00:00', 'Noche');
            } 
            else {
                $this->crearHorario($area->id_area, '08:00:00', '16:00:00', 'Día');
            }
        }
    }

    private function crearHorario($idArea, $entrada, $salida, $turno) {
        DB::table('horarios')->insert([
            'id_area' => $idArea,
            'hora_entrada' => $entrada,
            'hora_salida' => $salida,
            'turno' => $turno,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}