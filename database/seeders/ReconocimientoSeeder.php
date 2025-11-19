<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;

class ReconocimientoSeeder extends Seeder
{
    public function run(): void
    {
        // Solo generamos para los primeros 30 para no llenar la BD de basura pesada
        $personas = Persona::take(30)->get();

        foreach ($personas as $persona) {
            // Generar array de 128 floats aleatorios (Simulando face-api)
            $descriptor = [];
            for ($j = 0; $j < 128; $j++) {
                $descriptor[] = mt_rand() / mt_getrandmax() - 0.5; // Num entre -0.5 y 0.5
            }

            DB::table('reconocimientos')->insert([
                'id_persona' => $persona->id_persona,
                'face_descriptor' => json_encode($descriptor),
                'image_url' => 'https://ui-avatars.com/api/?name=' . urlencode($persona->nombre_completo) . '&background=random',
                'fecha_registro' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}