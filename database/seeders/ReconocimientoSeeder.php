<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Persona; // Importar el modelo Persona

class ReconocimientoSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $personas = Persona::all();

        if ($personas->isEmpty()) {
            $this->command->info('¡No hay personas en la BD! Ejecuta PersonaSeeder primero.');
            return;
        }

        // Generar algunos reconocimientos faciales para algunas personas
        foreach ($personas->take(20) as $persona) { // 20 personas tendrán reconocimientos
            for ($i = 0; $i < $faker->numberBetween(1, 3); $i++) { // Cada una tendrá entre 1 y 3 descriptores
                DB::table('reconocimientos')->insert([
                    'id_persona' => $persona->id_persona,
                    'face_descriptor' => json_encode($faker->randomElements(range(0.01, 0.99, 0.01), 128)), // Un array JSON de floats
                    'imagen_url' => $faker->imageUrl(640, 480, 'face', true), // URL de imagen ficticia
                    'fecha_registro' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}