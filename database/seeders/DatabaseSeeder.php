<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Descomenta la línea de UserFactory si existía, o simplemente añade la nueva:
        // \App\Models\User::factory(10)->create();

        // ¡AÑADE ESTA LÍNEA!
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}