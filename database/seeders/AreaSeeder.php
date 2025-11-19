<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['nombre_area' => 'Dirección', 'descripcion' => 'Gestión estratégica institucional'],
            ['nombre_area' => 'Administración', 'descripcion' => 'Gestión de recursos y contabilidad'],
            ['nombre_area' => 'Docentes de Primaria', 'descripcion' => 'Plana docente nivel primaria'],
            ['nombre_area' => 'Docentes de Secundaria', 'descripcion' => 'Plana docente nivel secundaria'],
            ['nombre_area' => 'Alumnos de Inicial', 'descripcion' => 'Estudiantes 3, 4 y 5 años'],
            ['nombre_area' => 'Alumnos de Primaria', 'descripcion' => 'Estudiantes 1ro a 6to grado'],
            ['nombre_area' => 'Alumnos de Secundaria', 'descripcion' => 'Estudiantes 1ro a 5to año'],
            ['nombre_area' => 'Tutoría y Psicología', 'descripcion' => 'Soporte emocional y académico'],
            ['nombre_area' => 'Mantenimiento y Limpieza', 'descripcion' => 'Servicios generales'],
            ['nombre_area' => 'Seguridad', 'descripcion' => 'Vigilancia y control de acceso'],
            ['nombre_area' => 'Biblioteca', 'descripcion' => 'Recursos educativos'],
            ['nombre_area' => 'Laboratorio', 'descripcion' => 'Ciencias y cómputo'],
            ['nombre_area' => 'Coordinación Académica', 'descripcion' => 'Supervisión pedagógica'],
            ['nombre_area' => 'Servicio Médico', 'descripcion' => 'Tópico y primeros auxilios'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(['nombre_area' => $area['nombre_area']], $area);
        }
    }
}