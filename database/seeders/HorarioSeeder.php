<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $horarios = [
            ['id_area' => 1, 'hora_entrada' => '08:00:00', 'hora_salida' => '17:00:00', 'turno' => 'Mañana'],
            ['id_area' => 1, 'hora_entrada' => '09:00:00', 'hora_salida' => '13:00:00', 'turno' => 'Mañana'],
            ['id_area' => 2, 'hora_entrada' => '14:00:00', 'hora_salida' => '22:00:00', 'turno' => 'Tarde'],
            ['id_area' => 2, 'hora_entrada' => '18:00:00', 'hora_salida' => '22:00:00', 'turno' => 'Tarde'],
            ['id_area' => 3, 'hora_entrada' => '07:00:00', 'hora_salida' => '15:00:00', 'turno' => 'Rotativo'],
        ];

        foreach ($horarios as $horario) {
            DB::table('horarios')->insert($horario);
        }
    }
}
