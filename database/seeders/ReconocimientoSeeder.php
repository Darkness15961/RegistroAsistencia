<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Persona;

class ReconocimientoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $personas = Persona::all();

        if ($personas->isEmpty()) {
            $this->command->info('¡No hay personas en la BD! Ejecuta PersonaSeeder primero.');
            return;
        }

        foreach ($personas->take(20) as $persona) {
            for ($i = 0; $i < $faker->numberBetween(1, 3); $i++) {
                DB::table('reconocimientos')->insert([
                    'id_persona' => $persona->id_persona,
                    'face_descriptor' => json_encode(
                        $faker->randomElements(range(0.01, 0.99, 0.01), 128, true)
                    ),
                    'image_url' => $faker->imageUrl(640, 480, 'face', true),
                    'fecha_registro' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
