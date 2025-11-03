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
            $areas = [
                ['nombre_area' => 'Dirección', 'descripcion' => 'Supervisión general y gestión institucional del colegio'],
                ['nombre_area' => 'Administración', 'descripcion' => 'Gestión administrativa, contable y de recursos del colegio'],
                ['nombre_area' => 'Docentes de Primaria', 'descripcion' => 'Profesores encargados del nivel de educación primaria'],
                ['nombre_area' => 'Docentes de Secundaria', 'descripcion' => 'Profesores encargados del nivel de educación secundaria'],
                ['nombre_area' => 'Alumnos de Inicial', 'descripcion' => 'Estudiantes pertenecientes al nivel inicial'],
                ['nombre_area' => 'Alumnos de Primaria', 'descripcion' => 'Estudiantes pertenecientes al nivel primaria'],
                ['nombre_area' => 'Alumnos de Secundaria', 'descripcion' => 'Estudiantes pertenecientes al nivel secundaria'],
                ['nombre_area' => 'Tutoría y Psicología', 'descripcion' => 'Área de orientación y apoyo emocional y académico'],
                ['nombre_area' => 'Mantenimiento y Limpieza', 'descripcion' => 'Personal encargado del mantenimiento y limpieza de las instalaciones'],
                ['nombre_area' => 'Seguridad', 'descripcion' => 'Personal de vigilancia y control de accesos del colegio'],
                ['nombre_area' => 'Biblioteca', 'descripcion' => 'Gestión de recursos bibliográficos y apoyo académico'],
                ['nombre_area' => 'Laboratorio', 'descripcion' => 'Gestión de prácticas científicas y tecnológicas'],
                ['nombre_area' => 'Coordinación Académica', 'descripcion' => 'Supervisión y coordinación de las áreas educativas'],
                ['nombre_area' => 'Servicio Médico', 'descripcion' => 'Atención médica y primeros auxilios dentro del colegio'],
            ];

            foreach ($areas as $area) {
                DB::table('areas')->insert($area);
            }
        }
}