<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Persona;    // Asumo que este es tu modelo para Empleados
use App\Models\Asistencia;
use App\Models\Horario;
use App\Models\User;       // El modelo de Usuario de Laravel

class DashboardController extends Controller
{
    /**
     * Devuelve las estadísticas para el dashboard principal.
     */
    public function getStats(Request $request)
    {
        // --- 1. DATOS PARA LAS TARJETAS ---
        // ¡Hacemos las consultas reales!
        $stats = [
            'empleados' => Persona::count(),
            'asistenciasHoy' => Asistencia::whereDate('fecha_hora', today())->count(),
            'horariosActivos' => Horario::count(), // O filtra por 'activo' si tienes ese campo
            'usuarios' => User::count()
        ];

        // --- 2. DATOS PARA GRÁFICO DE ASISTENCIA (TablaAsis) ---
        // (Esto es un ejemplo, puedes ajustarlo)
        $attendanceChart = [
            'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
            'datasets' => [
                [
                    'label' => 'Asistencias',
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)',
                        'rgba(251, 146, 60, 0.8)'
                    ],
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'data' => [
                        Asistencia::whereDay('fecha_hora', 'monday')->count(),
                        Asistencia::whereDay('fecha_hora', 'tuesday')->count(),
                        Asistencia::whereDay('fecha_hora', 'wednesday')->count(),
                        Asistencia::whereDay('fecha_hora', 'thursday')->count(),
                        Asistencia::whereDay('fecha_hora', 'friday')->count()
                    ] // Datos de prueba, reemplaza con tu consulta real
                ]
            ]
        ];

        // --- 3. DATOS PARA GRÁFICO DE EMPLEADOS (TablaEmp) ---
        $employeesChart = [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Empleados',
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                    'data' => [
                        Persona::whereMonth('created_at', 1)->count(),
                        Persona::whereMonth('created_at', 2)->count(),
                        Persona::whereMonth('created_at', 3)->count(),
                        Persona::whereMonth('created_at', 4)->count(),
                        Persona::whereMonth('created_at', 5)->count(),
                        Persona::whereMonth('created_at', 6)->count()
                    ] // Datos de prueba, reemplaza con tu consulta real
                ]
            ]
        ];

        // --- 4. RESPUESTA JSON ---
        return response()->json([
            'stats' => $stats,
            'attendanceChart' => $attendanceChart,
            'employeesChart' => $employeesChart
        ]);
    }
}