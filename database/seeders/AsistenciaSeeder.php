<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <--- Importar Schema
use App\Models\Persona;
use Carbon\Carbon;
use Faker\Factory as Faker;

class AsistenciaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza segura
        Schema::disableForeignKeyConstraints();
        DB::table('asistencias')->truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Faker::create();
        $personas = Persona::all(); 
        $asistenciasData = [];

        // Generar datos para los últimos 30 días
        for ($i = 0; $i < 30; $i++) {
            $fecha = Carbon::now()->subDays($i);
            
            foreach ($personas as $persona) {
                // 15% de probabilidad de FALTA (No se inserta registro)
                if ($faker->boolean(15)) continue;

                // Horario teórico
                $horaEntradaBase = $persona->tipo_persona === 'estudiante' ? '07:45:00' : '08:00:00';
                $entradaTeorica = Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaEntradaBase);

                // 20% TARDANZA
                if ($faker->boolean(20)) {
                    $horaEntrada = $entradaTeorica->copy()->addMinutes($faker->numberBetween(1, 60));
                    $estado = 'Tarde';
                } else {
                    // LLegar temprano
                    $horaEntrada = $entradaTeorica->copy()->subMinutes($faker->numberBetween(0, 30));
                    $estado = 'Presente';
                }

                $horaSalida = $horaEntrada->copy()->addHours(8);

                $asistenciasData[] = [
                    'id_persona' => $persona->id_persona,
                    'fecha' => $fecha->toDateString(),
                    'hora_entrada' => $horaEntrada->toTimeString(),
                    'hora_salida' => $horaSalida->toTimeString(),
                    'estado_asistencia' => $estado,
                    'metodo_registro' => $faker->randomElement(['IA', 'IA', 'Manual']),
                    'created_at' => $horaEntrada,
                    'updated_at' => $horaSalida,
                ];
            }
        }

        // Insertar en lotes
        foreach (array_chunk($asistenciasData, 200) as $chunk) {
            DB::table('asistencias')->insert($chunk);
        }
    }
}