<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Para encriptar contraseñas
use Faker\Factory as Faker;
use App\Models\Persona; // Importar el modelo Persona

class UsuarioSeeder extends Seeder
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

        // Crear un usuario administrador fijo
        $adminPersona = Persona::where('nombre_completo', 'like', 'Juan%')->first(); // O crea uno nuevo si prefieres
        if (!$adminPersona) {
            $adminPersona = Persona::factory()->create([
                'nombre_completo' => 'Admin Sistema',
                'dni' => '12345678',
                'id_grupo' => Grupo::all()->random()->id_grupo,
                'id_horario' => Horario::all()->random()->id_horario,
            ]);
        }
        
        DB::table('usuarios')->insert([
            'id_persona' => $adminPersona->id_persona,
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'), // Contraseña: password
            'rol' => 'administrador',
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear usuarios adicionales para algunas personas (no todas)
        $personasSinUsuario = Persona::doesntHave('usuario')->inRandomOrder()->take(10)->get();

        foreach ($personasSinUsuario as $persona) {
            DB::table('usuarios')->insert([
                'id_persona' => $persona->id_persona,
                'email' => $faker->unique()->safeEmail(),
                'password_hash' => Hash::make('password'), // Contraseña por defecto
                'rol' => $faker->randomElement(['empleado', 'gerente']),
                'estado' => $faker->randomElement(['activo', 'inactivo']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}