<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Horario;
use Carbon\Carbon;
use Faker\Factory as Faker;

class AsistenciaSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Traer todas las personas
        $personas = Persona::all();

        foreach ($personas as $persona) {

            // 20% de probabilidad de no registrar asistencia
            if ($faker->boolean(20)) {
                continue;
            }

            // Obtener el horario asignado, o uno aleatorio si no tiene
            $horario = $persona->horario ?? Horario::inRandomOrder()->first();

            if (!$horario) {
                // Si no hay horarios en la DB, saltar persona
                continue;
            }

            $horaEntradaTeorica = Carbon::parse($horario->hora_entrada);
            $horaSalidaTeorica  = Carbon::parse($horario->hora_salida);

            // Calcular minutos de tardanza (30% de probabilidad)
            $minutosTarde = $faker->boolean(30) ? $faker->numberBetween(5, 60) : 0;
            $horaEntrada = $horaEntradaTeorica->copy()->addMinutes($minutosTarde)->format('H:i:s');

            // Calcular hora de salida (puede ser adelantada o normal)
            $minutosAdelantoSalida = $faker->boolean(20) ? $faker->numberBetween(5, 30) : 0;
            $horaSalida = $horaSalidaTeorica->copy()->subMinutes($minutosAdelantoSalida)->format('H:i:s');

            DB::table('asistencias')->insert([
                'id_persona' => $persona->id_persona,
                'fecha' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'hora_entrada' => $horaEntrada,
                'hora_salida'  => $horaSalida,
                'estado_asistencia' => $faker->randomElement(['presente', 'ausente', 'tarde']),
                'metodo_registro' => $faker->randomElement(['manual', 'facial']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
