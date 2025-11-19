<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AreaSeeder::class,          // 1. Áreas base
            HorarioSeeder::class,       // 2. Horarios de esas áreas
            GrupoSeeder::class,         // 3. Grupos (Alumnos y Personal)
            AdminUserSeeder::class,     // 4. El Super Admin (Usuario Fijo)
            PersonaSeeder::class,       // 5. Población masiva (Alumnos/Personal)
            UsuarioSeeder::class,       // 6. Cuentas de acceso para la población
            ReconocimientoSeeder::class,// 7. Datos biométricos simulados
            AsistenciaSeeder::class,    // 8. Historial de asistencias
        ]);
    }
}