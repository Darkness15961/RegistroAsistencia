<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Ahora proporcionamos ambos campos: nombre_area y descripcion
        $areas = [
            ['nombre_area' => 'Recursos Humanos', 'descripcion' => 'Departamento de gestión de personal y contratación.'],
            ['nombre_area' => 'Desarrollo de Software', 'descripcion' => 'Equipo de ingeniería y desarrollo de aplicaciones.'],
            ['nombre_area' => 'Diseño Gráfico', 'descripcion' => 'Departamento de creatividad, branding e identidad visual.'],
            ['nombre_area' => 'Marketing Digital', 'descripcion' => 'Equipo de promoción, redes sociales y SEO.'],
            ['nombre_area' => 'Contabilidad', 'descripcion' => 'Departamento financiero y de administración.'],
            ['nombre_area' => 'Soporte Técnico', 'descripcion' => 'Equipo de ayuda al usuario y mantenimiento de TI.'],
            ['nombre_area' => 'Ventas', 'descripcion' => 'Equipo comercial y de relaciones con clientes.'],
        ];

        foreach ($areas as $area) {
            DB::table('areas')->insert($area);
        }
    }
}