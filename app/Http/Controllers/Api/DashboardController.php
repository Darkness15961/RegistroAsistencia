<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        // Estadísticas principales
        $stats = [
            'empleados' => Persona::count(),
            'asistenciasHoy' => Asistencia::whereDate('fecha', today())->count(),
            'horariosActivos' => Horario::count(),
            'usuarios' => User::count()
        ];

        // Gráfico de asistencias de los últimos 5 días
        $labels = [];
        $data = [];

        for ($i = 4; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $labels[] = $day->format('D'); // Ejemplo: Mon, Tue, Wed
            $data[] = Asistencia::whereDate('fecha', $day)->count();
        }

        return response()->json([
            'stats' => $stats,
            'chart' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }
}
