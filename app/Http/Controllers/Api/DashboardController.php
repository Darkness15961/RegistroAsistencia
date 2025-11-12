<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use App\Models\Usuario;
use App\Models\Area; // ¡Importante! Asegúrate de importar Area
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ¡Importante! Asegúrate de importar DB

class DashboardController extends Controller
{
    /**
     * Obtiene todas las estadísticas para el dashboard principal.
     */
    public function getStats()
    {
        // --- 1. Estadísticas Principales (Tarjetas) ---
        $stats = [
            'empleados' => Persona::where('tipo_persona', 'empleado')->count(),
            'alumnos' => Persona::where('tipo_persona', 'alumno')->count(),
            'asistenciasHoy' => Asistencia::whereDate('fecha', today())->count(),
            'horariosActivos' => Horario::count(),
            'usuarios' => Usuario::count()
        ];

        // --- 2. Gráfico de Asistencias (Últimos 7 días) ---
        $asistenciaLabels = [];
        $asistenciaData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $asistenciaLabels[] = $day->isoFormat('ddd'); // Formato: Lun, Mar, Mié...
            $asistenciaData[] = Asistencia::whereDate('fecha', $day)->count();
        }
        $asistenciaChart = [
            'labels' => $asistenciaLabels,
            'data' => $asistenciaData,
        ];

        // --- 3. Gráfico de Empleados Registrados (Últimos 6 meses) ---
        $empleadosPorMes = Persona::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"), 
                DB::raw('count(*) as total')
            )
            ->where('tipo_persona', 'empleado')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        $empleadosLabels = [];
        $empleadosData = [];
        $fechaActual = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $mesIterado = $fechaActual->copy()->subMonths($i);
            $mesFormato = $mesIterado->format('Y-m');
            $empleadosLabels[] = $mesIterado->isoFormat('MMM'); // Formato: Ene, Feb, Mar...

            $registro = $empleadosPorMes->firstWhere('mes', $mesFormato);
            $empleadosData[] = $registro ? $registro->total : 0;
        }
        $empleadosChart = [
            'labels' => $empleadosLabels,
            'data' => $empleadosData,
        ];

        // --- 4. Gráfico de Estado de Hoy (Presente, Tarde, Falta) ---
        $asistenciasHoy = Asistencia::whereDate('fecha', today())
            ->select('estado_asistencia', DB::raw('count(*) as total'))
            ->groupBy('estado_asistencia')
            ->pluck('total', 'estado_asistencia');

        // Contar total de personas relevantes (empleados + alumnos)
        $totalPersonas = Persona::whereIn('tipo_persona', ['empleado', 'alumno'])->count();
        $presentesHoy = $asistenciasHoy->get('Presente', 0);
        $tardesHoy = $asistenciasHoy->get('Tarde', 0);
        
        // Faltas = Total de personas - (los que vinieron presentes + los que vinieron tarde)
        $asistieronHoy = $presentesHoy + $tardesHoy;
        $faltasHoy = $totalPersonas - $asistieronHoy;

        $estadoHoyChart = [
            'labels' => ['Presente', 'Tarde', 'Falta'],
            'data' => [
                $presentesHoy,
                $tardesHoy,
                $faltasHoy < 0 ? 0 : $faltasHoy // Asegura que no sea negativo
            ]
        ];

        // --- 5. Gráfico de Asistencias de Hoy por Área ---
        $asistenciasPorArea = Asistencia::join('personas', 'asistencias.id_persona', '=', 'personas.id_persona')
            ->join('areas', 'personas.id_area', '=', 'areas.id_area')
            ->whereDate('asistencias.fecha', today())
            ->select('areas.nombre_area', DB::raw('count(asistencias.id_asistencia) as total'))
            ->groupBy('areas.nombre_area')
            ->pluck('total', 'nombre_area');
            
        $asistenciaAreaChart = [
            'labels' => $asistenciasPorArea->keys(),
            'data' => $asistenciasPorArea->values()
        ];

        // --- Devolver todo en una sola respuesta JSON ---
        return response()->json([
            'stats' => $stats,
            'asistenciaChart' => $asistenciaChart,
            'empleadosChart' => $empleadosChart,
            'estadoHoyChart' => $estadoHoyChart,
            'asistenciaAreaChart' => $asistenciaAreaChart
        ]);
    }
}