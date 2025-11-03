<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
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

        // 🔹 Definir los turnos disponibles
        $turnos = [
            'mañana' => [
                'hora_entrada' => '07:30:00',
                'hora_salida'  => '13:00:00',
            ],
            'tarde' => [
                'hora_entrada' => '13:30:00',
                'hora_salida'  => '18:30:00',
            ],
        ];

        // 🔹 Obtener todas las personas
        $personas = Persona::all();

        foreach ($personas as $persona) {
            // 20% de probabilidad de no registrar asistencia
            if ($faker->boolean(20)) {
                continue;
            }

            // 🔹 Determinar el turno
            if ($persona->tipo_persona === 'estudiante') {
                // Aleatorio entre mañana y tarde
                $horario = $faker->boolean() ? $turnos['mañana'] : $turnos['tarde'];
            } else {
                // Para docentes o administrativos
                $horario = $turnos['mañana'];
            }

            // 🔹 Calcular hora de entrada y salida con variaciones realistas
            $horaEntradaTeorica = Carbon::parse($horario['hora_entrada']);
            $horaSalidaTeorica  = Carbon::parse($horario['hora_salida']);

            // 30% de probabilidad de llegar tarde
            $minutosTarde = $faker->boolean(30) ? $faker->numberBetween(5, 60) : 0;
            $horaEntrada = $horaEntradaTeorica->copy()->addMinutes($minutosTarde)->format('H:i:s');

            // 20% de probabilidad de salir antes
            $minutosAdelantoSalida = $faker->boolean(20) ? $faker->numberBetween(5, 30) : 0;
            $horaSalida = $horaSalidaTeorica->copy()->subMinutes($minutosAdelantoSalida)->format('H:i:s');

            // 🔹 Insertar asistencia en la base de datos
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
