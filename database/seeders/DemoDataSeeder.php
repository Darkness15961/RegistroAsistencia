<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Horario;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. ÁREAS
        // ==========================================
        $areaAdministracion = Area::create([
            'nombre_area' => 'Administración',
            'descripcion' => 'Personal administrativo y directivo',
            'tipo_area' => 'personal'
        ]);

        $areaPrimaria = Area::create([
            'nombre_area' => 'Primaria',
            'descripcion' => 'Nivel primario',
            'tipo_area' => 'alumnado'
        ]);

        $areaSecundaria = Area::create([
            'nombre_area' => 'Secundaria',
            'descripcion' => 'Nivel secundario',
            'tipo_area' => 'alumnado'
        ]);

        // ==========================================
        // 2. PERSONAS - ADMINISTRADOR
        // ==========================================
        $personaAdmin = Persona::create([
            'nombre_completo' => 'Director General',
            'dni' => '12345678',
            'telefono' => '987654321',
            'correo' => 'admin@4scan.com',
            'cargo_grado' => 'Director',
            'tipo_persona' => 'empleado',
            'id_area' => $areaAdministracion->id_area,
            'id_grupo' => null,
            'estado' => 'activo'
        ]);

        Usuario::create([
            'id_persona' => $personaAdmin->id_persona,
            'email' => 'admin@4scan.com',
            'password_hash' => Hash::make('admin123456'),
            'rol' => 'admin',
            'estado' => 'activo'
        ]);

        // ==========================================
        // 3. PERSONAS - SUPERVISOR
        // ==========================================
        $personaSupervisor = Persona::create([
            'nombre_completo' => 'Carlos Mendoza',
            'dni' => '23456789',
            'telefono' => '987654322',
            'correo' => 'supervisor@4scan.com',
            'cargo_grado' => 'Supervisor de Personal',
            'tipo_persona' => 'empleado',
            'id_area' => $areaAdministracion->id_area,
            'id_grupo' => null,
            'estado' => 'activo'
        ]);

        Usuario::create([
            'id_persona' => $personaSupervisor->id_persona,
            'email' => 'supervisor@4scan.com',
            'password_hash' => Hash::make('supervisor123'),
            'rol' => 'supervisor',
            'estado' => 'activo'
        ]);

        // ==========================================
        // 4. DOCENTES Y GRUPOS
        // ==========================================
        
        // Docente 1 - Primaria
        $docente1 = Persona::create([
            'nombre_completo' => 'María García',
            'dni' => '34567890',
            'telefono' => '987654323',
            'correo' => 'maria.garcia@4scan.com',
            'cargo_grado' => 'Profesora',
            'tipo_persona' => 'docente',
            'id_area' => $areaPrimaria->id_area,
            'id_grupo' => null,
            'estado' => 'activo'
        ]);

        Usuario::create([
            'id_persona' => $docente1->id_persona,
            'email' => 'docente1@4scan.com',
            'password_hash' => Hash::make('docente123'),
            'rol' => 'docente',
            'estado' => 'activo'
        ]);

        // Grupo Primaria 1ro A
        $grupo1A = Grupo::create([
            'id_area' => $areaPrimaria->id_area,
            'id_tutor' => $docente1->id_persona,
            'nivel' => 'Primaria',
            'grado' => '1ro',
            'seccion' => 'A'
        ]);

        // Docente 2 - Primaria (con 2 grupos)
        $docente2 = Persona::create([
            'nombre_completo' => 'Juan Pérez',
            'dni' => '45678901',
            'telefono' => '987654324',
            'correo' => 'juan.perez@4scan.com',
            'cargo_grado' => 'Profesor',
            'tipo_persona' => 'docente',
            'id_area' => $areaPrimaria->id_area,
            'id_grupo' => null,
            'estado' => 'activo'
        ]);

        Usuario::create([
            'id_persona' => $docente2->id_persona,
            'email' => 'docente2@4scan.com',
            'password_hash' => Hash::make('docente123'),
            'rol' => 'docente',
            'estado' => 'activo'
        ]);

        // Grupo Primaria 2do A
        $grupo2A = Grupo::create([
            'id_area' => $areaPrimaria->id_area,
            'id_tutor' => $docente2->id_persona,
            'nivel' => 'Primaria',
            'grado' => '2do',
            'seccion' => 'A'
        ]);

        // Grupo Primaria 2do B
        $grupo2B = Grupo::create([
            'id_area' => $areaPrimaria->id_area,
            'id_tutor' => $docente2->id_persona,
            'nivel' => 'Primaria',
            'grado' => '2do',
            'seccion' => 'B'
        ]);

        // Docente 3 - Secundaria
        $docente3 = Persona::create([
            'nombre_completo' => 'Ana Torres',
            'dni' => '56789012',
            'telefono' => '987654325',
            'correo' => 'ana.torres@4scan.com',
            'cargo_grado' => 'Profesora',
            'tipo_persona' => 'docente',
            'id_area' => $areaSecundaria->id_area,
            'id_grupo' => null,
            'estado' => 'activo'
        ]);

        Usuario::create([
            'id_persona' => $docente3->id_persona,
            'email' => 'docente3@4scan.com',
            'password_hash' => Hash::make('docente123'),
            'rol' => 'docente',
            'estado' => 'activo'
        ]);

        // Grupo Secundaria 1ro A
        $grupo1Sec = Grupo::create([
            'id_area' => $areaSecundaria->id_area,
            'id_tutor' => $docente3->id_persona,
            'nivel' => 'Secundaria',
            'grado' => '1ro',
            'seccion' => 'A'
        ]);

        // ==========================================
        // 5. ESTUDIANTES
        // ==========================================
        
        // Estudiantes para 1ro A Primaria (5 estudiantes)
        $estudiantes1A = [
            ['nombre' => 'Luis Ramírez', 'dni' => '70000001'],
            ['nombre' => 'Sofía Martínez', 'dni' => '70000002'],
            ['nombre' => 'Diego López', 'dni' => '70000003'],
            ['nombre' => 'Valentina Cruz', 'dni' => '70000004'],
            ['nombre' => 'Mateo Silva', 'dni' => '70000005'],
        ];

        foreach ($estudiantes1A as $est) {
            Persona::create([
                'nombre_completo' => $est['nombre'],
                'dni' => $est['dni'],
                'telefono' => null,
                'correo' => strtolower(str_replace(' ', '.', $est['nombre'])) . '@estudiante.com',
                'cargo_grado' => '1ro A',
                'tipo_persona' => 'estudiante',
                'id_area' => $areaPrimaria->id_area,
                'id_grupo' => $grupo1A->id_grupo,
                'estado' => 'activo'
            ]);
        }

        // Estudiantes para 2do A Primaria (4 estudiantes)
        $estudiantes2A = [
            ['nombre' => 'Emma Flores', 'dni' => '70000006'],
            ['nombre' => 'Lucas Vargas', 'dni' => '70000007'],
            ['nombre' => 'Isabella Rojas', 'dni' => '70000008'],
            ['nombre' => 'Sebastián Díaz', 'dni' => '70000009'],
        ];

        foreach ($estudiantes2A as $est) {
            Persona::create([
                'nombre_completo' => $est['nombre'],
                'dni' => $est['dni'],
                'telefono' => null,
                'correo' => strtolower(str_replace(' ', '.', $est['nombre'])) . '@estudiante.com',
                'cargo_grado' => '2do A',
                'tipo_persona' => 'estudiante',
                'id_area' => $areaPrimaria->id_area,
                'id_grupo' => $grupo2A->id_grupo,
                'estado' => 'activo'
            ]);
        }

        // Estudiantes para 2do B Primaria (4 estudiantes)
        $estudiantes2B = [
            ['nombre' => 'Mía Herrera', 'dni' => '70000010'],
            ['nombre' => 'Benjamín Castro', 'dni' => '70000011'],
            ['nombre' => 'Camila Ortiz', 'dni' => '70000012'],
            ['nombre' => 'Thiago Morales', 'dni' => '70000013'],
        ];

        foreach ($estudiantes2B as $est) {
            Persona::create([
                'nombre_completo' => $est['nombre'],
                'dni' => $est['dni'],
                'telefono' => null,
                'correo' => strtolower(str_replace(' ', '.', $est['nombre'])) . '@estudiante.com',
                'cargo_grado' => '2do B',
                'tipo_persona' => 'estudiante',
                'id_area' => $areaPrimaria->id_area,
                'id_grupo' => $grupo2B->id_grupo,
                'estado' => 'activo'
            ]);
        }

        // Estudiantes para 1ro Secundaria (5 estudiantes)
        $estudiantes1Sec = [
            ['nombre' => 'Martina Sánchez', 'dni' => '70000014'],
            ['nombre' => 'Joaquín Ruiz', 'dni' => '70000015'],
            ['nombre' => 'Renata Vega', 'dni' => '70000016'],
            ['nombre' => 'Nicolás Campos', 'dni' => '70000017'],
            ['nombre' => 'Luciana Paredes', 'dni' => '70000018'],
        ];

        foreach ($estudiantes1Sec as $est) {
            Persona::create([
                'nombre_completo' => $est['nombre'],
                'dni' => $est['dni'],
                'telefono' => null,
                'correo' => strtolower(str_replace(' ', '.', $est['nombre'])) . '@estudiante.com',
                'cargo_grado' => '1ro Sec',
                'tipo_persona' => 'estudiante',
                'id_area' => $areaSecundaria->id_area,
                'id_grupo' => $grupo1Sec->id_grupo,
                'estado' => 'activo'
            ]);
        }

        // ==========================================
        // 6. PERSONAL ADICIONAL (para supervisor)
        // ==========================================
        
        $empleados = [
            ['nombre' => 'Roberto Chávez', 'dni' => '80000001', 'cargo' => 'Conserje'],
            ['nombre' => 'Patricia Núñez', 'dni' => '80000002', 'cargo' => 'Secretaria'],
            ['nombre' => 'Fernando Ríos', 'dni' => '80000003', 'cargo' => 'Auxiliar'],
        ];

        foreach ($empleados as $emp) {
            Persona::create([
                'nombre_completo' => $emp['nombre'],
                'dni' => $emp['dni'],
                'telefono' => '987654330',
                'correo' => strtolower(str_replace(' ', '.', $emp['nombre'])) . '@4scan.com',
                'cargo_grado' => $emp['cargo'],
                'tipo_persona' => 'empleado',
                'id_area' => $areaAdministracion->id_area,
                'id_grupo' => null,
                'estado' => 'activo'
            ]);
        }

        // ==========================================
        // 7. HORARIOS (por área, no por grupo)
        // ==========================================
        
        // Horario para Primaria
        Horario::create([
            'id_area' => $areaPrimaria->id_area,
            'hora_entrada' => '08:00:00',
            'hora_salida' => '13:00:00',
            'turno' => 'mañana'
        ]);

        // Horario para Secundaria
        Horario::create([
            'id_area' => $areaSecundaria->id_area,
            'hora_entrada' => '07:30:00',
            'hora_salida' => '14:00:00',
            'turno' => 'mañana'
        ]);

        // Horario para Administración/Personal
        Horario::create([
            'id_area' => $areaAdministracion->id_area,
            'hora_entrada' => '07:00:00',
            'hora_salida' => '15:00:00',
            'turno' => 'completo'
        ]);

        // Asignar personal al área de administración (ya tienen id_area correcto)
        // Los docentes ya tienen su área asignada

        $this->command->info('✅ Datos de demostración creados exitosamente!');
        $this->command->info('');
        $this->command->info('📧 Credenciales de acceso:');
        $this->command->info('');
        $this->command->info('👤 ADMIN:');
        $this->command->info('   Email: admin@4scan.com');
        $this->command->info('   Password: admin123456');
        $this->command->info('');
        $this->command->info('👤 SUPERVISOR:');
        $this->command->info('   Email: supervisor@4scan.com');
        $this->command->info('   Password: supervisor123');
        $this->command->info('');
        $this->command->info('👤 DOCENTES:');
        $this->command->info('   Email: docente1@4scan.com (1 grupo)');
        $this->command->info('   Email: docente2@4scan.com (2 grupos)');
        $this->command->info('   Email: docente3@4scan.com (1 grupo)');
        $this->command->info('   Password: docente123');
    }
}
