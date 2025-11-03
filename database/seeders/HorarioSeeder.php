<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all();

        if ($areas->isEmpty()) {
            $this->command->info('¡No hay áreas en la BD! Ejecuta AreaSeeder primero.');
            return;
        }

        foreach ($areas as $area) {
            switch ($area->nombre_area) {

                // Horarios para alumnos
                case 'Alumnos de Inicial':
                    // Turno mañana y tarde
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '07:45:00',
                        'hora_salida' => '13:45:00',
                        'turno' => 'Mañana',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '13:00:00',
                        'hora_salida' => '18:00:00',
                        'turno' => 'Tarde',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                case 'Alumnos de Primaria':
                case 'Alumnos de Secundaria':
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '07:45:00',
                        'hora_salida' => '13:45:00',
                        'turno' => 'Mañana',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '13:00:00',
                        'hora_salida' => '18:00:00',
                        'turno' => 'Tarde',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                // Horarios para docentes
                case 'Docentes de Primaria':
                case 'Docentes de Secundaria':
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '07:30:00',
                        'hora_salida' => '14:00:00',
                        'turno' => 'Mañana',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '12:30:00',
                        'hora_salida' => '18:30:00',
                        'turno' => 'Tarde',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                // Horarios administrativos
                case 'Dirección':
                case 'Administración':
                case 'Tutoría y Psicología':
                case 'Biblioteca':
                case 'Laboratorio':
                case 'Coordinación Académica':
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '08:00:00',
                        'hora_salida' => '17:00:00',
                        'turno' => 'Día',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                // Personal de servicios
                case 'Mantenimiento y Limpieza':
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '06:00:00',
                        'hora_salida' => '14:00:00',
                        'turno' => 'Mañana',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '14:00:00',
                        'hora_salida' => '22:00:00',
                        'turno' => 'Tarde',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                case 'Seguridad':
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '00:00:00',
                        'hora_salida' => '08:00:00',
                        'turno' => 'Noche',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '08:00:00',
                        'hora_salida' => '16:00:00',
                        'turno' => 'Mañana',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '16:00:00',
                        'hora_salida' => '00:00:00',
                        'turno' => 'Tarde',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                // Servicio médico
                case 'Servicio Médico':
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '08:00:00',
                        'hora_salida' => '18:00:00',
                        'turno' => 'Día',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;

                default:
                    DB::table('horarios')->insert([
                        'id_area' => $area->id_area,
                        'hora_entrada' => '08:00:00',
                        'hora_salida' => '17:00:00',
                        'turno' => 'Día',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;
            }
        }
    }
}
