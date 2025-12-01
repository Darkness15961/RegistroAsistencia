<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use App\Models\Usuario;
use App\Models\Area;
use App\Models\Grupo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Obtiene todas las estadísticas para el dashboard principal.
     */
    public function getStats()
    {
        $user = Auth::user();
        $rol = $user->rol;
        
        // Obtener IDs de grupos según el rol
        $grupoIds = $this->getGrupoIdsPorRol($user, $rol);
        
        // --- 1. Estadísticas Principales (Tarjetas) ---
        $stats = $this->getStatsSegunRol($rol, $grupoIds);

        // --- 2. Gráfico de Asistencias (Últimos 7 días) ---
        $asistenciaChart = $this->getAsistenciaChartSegunRol($rol, $grupoIds);

        // --- 3. Gráfico de Empleados Registrados (Últimos 6 meses) ---
        $empleadosChart = $this->getEmpleadosChartSegunRol($rol, $grupoIds);

        // --- 4. Gráfico de Estado de Hoy (Presente, Tarde, Falta) ---
        $estadoHoyChart = $this->getEstadoHoyChartSegunRol($rol, $grupoIds);

        // --- 5. Gráfico de Asistencias de Hoy por Área ---
        $asistenciaAreaChart = $this->getAsistenciaAreaChartSegunRol($rol, $grupoIds);

        // --- Devolver todo en una sola respuesta JSON ---
        return response()->json([
            'stats' => $stats,
            'asistenciaChart' => $asistenciaChart,
            'empleadosChart' => $empleadosChart,
            'estadoHoyChart' => $estadoHoyChart,
            'asistenciaAreaChart' => $asistenciaAreaChart
        ]);
    }

    private function getGrupoIdsPorRol($user, $rol)
    {
        if ($rol === 'admin') {
            return null; // Admin ve todo
        } elseif ($rol === 'supervisor') {
            // Supervisor ve solo grupos de áreas de tipo 'personal'
            return Grupo::whereHas('area', function($q) {
                $q->where('tipo_area', 'personal');
            })->pluck('id_grupo')->toArray();
        } elseif ($rol === 'docente') {
            // Docente ve solo sus grupos asignados
            $persona = $user->persona;
            return $persona ? Grupo::where('id_tutor', $persona->id_persona)->pluck('id_grupo')->toArray() : [];
        }
        return [];
    }

    private function getStatsSegunRol($rol, $grupoIds)
    {
        if ($rol === 'admin') {
            return [
                'empleados' => Persona::where('tipo_persona', 'empleado')->count(),
                'alumnos' => Persona::where('tipo_persona', 'estudiante')->count(),
                'asistenciasHoy' => Asistencia::whereDate('fecha', today())->count(),
                'horariosActivos' => Horario::count(),
                'usuarios' => Usuario::count(),
                'asistenciaRateHoy' => $this->calcularTasaAsistencia(null)
            ];
        } elseif ($rol === 'supervisor') {
            $personasIds = Persona::whereIn('id_grupo', $grupoIds)->pluck('id_persona');
            return [
                'empleados' => Persona::whereIn('id_grupo', $grupoIds)->where('tipo_persona', 'empleado')->count(),
                'alumnos' => 0,
                'asistenciasHoy' => Asistencia::whereIn('id_persona', $personasIds)->whereDate('fecha', today())->count(),
                'horariosActivos' => Horario::whereIn('id_area', function($q) use ($grupoIds) {
                    $q->select('id_area')->from('grupos')->whereIn('id_grupo', $grupoIds);
                })->count(),
                'usuarios' => 0,
                'asistenciaRateHoy' => $this->calcularTasaAsistencia($grupoIds)
            ];
        } else { // docente
            $personasIds = Persona::whereIn('id_grupo', $grupoIds)->pluck('id_persona');
            return [
                'empleados' => 0,
                'alumnos' => Persona::whereIn('id_grupo', $grupoIds)->where('tipo_persona', 'estudiante')->count(),
                'asistenciasHoy' => Asistencia::whereIn('id_persona', $personasIds)->whereDate('fecha', today())->count(),
                'horariosActivos' => 0,
                'usuarios' => 0,
                'asistenciaRateHoy' => $this->calcularTasaAsistencia($grupoIds)
            ];
        }
    }

    private function calcularTasaAsistencia($grupoIds)
    {
        $query = Persona::whereIn('tipo_persona', ['empleado', 'estudiante']);
        if ($grupoIds !== null) {
            $query->whereIn('id_grupo', $grupoIds);
        }
        $totalPersonas = $query->count();

        $asistenciasQuery = Asistencia::whereDate('fecha', today())
            ->whereIn('estado_asistencia', ['Presente', 'Tarde']);
        
        if ($grupoIds !== null) {
            $personasIds = Persona::whereIn('id_grupo', $grupoIds)->pluck('id_persona');
            $asistenciasQuery->whereIn('id_persona', $personasIds);
        }
        
        $asistieronHoy = $asistenciasQuery->count();
        return $totalPersonas > 0 ? round(($asistieronHoy / $totalPersonas) * 100, 1) : 0;
    }

    private function getAsistenciaChartSegunRol($rol, $grupoIds)
    {
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $labels[] = $day->isoFormat('ddd');
            
            $query = Asistencia::whereDate('fecha', $day);
            if ($grupoIds !== null) {
                $personasIds = Persona::whereIn('id_grupo', $grupoIds)->pluck('id_persona');
                $query->whereIn('id_persona', $personasIds);
            }
            $data[] = $query->count();
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    private function getEmpleadosChartSegunRol($rol, $grupoIds)
    {
        $query = Persona::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"), 
                DB::raw('count(*) as total')
            )
            ->where('tipo_persona', $rol === 'docente' ? 'estudiante' : 'empleado')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth());
        
        if ($grupoIds !== null) {
            $query->whereIn('id_grupo', $grupoIds);
        }
        
        $empleadosPorMes = $query->groupBy('mes')->orderBy('mes', 'asc')->get();

        $labels = [];
        $data = [];
        $fechaActual = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $mesIterado = $fechaActual->copy()->subMonths($i);
            $mesFormato = $mesIterado->format('Y-m');
            $labels[] = $mesIterado->isoFormat('MMM');

            $registro = $empleadosPorMes->firstWhere('mes', $mesFormato);
            $data[] = $registro ? $registro->total : 0;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    private function getEstadoHoyChartSegunRol($rol, $grupoIds)
    {
        $query = Asistencia::whereDate('fecha', today())
            ->select('estado_asistencia', DB::raw('count(*) as total'))
            ->groupBy('estado_asistencia');
        
        if ($grupoIds !== null) {
            $personasIds = Persona::whereIn('id_grupo', $grupoIds)->pluck('id_persona');
            $query->whereIn('id_persona', $personasIds);
        }
        
        $asistenciasHoy = $query->pluck('total', 'estado_asistencia');

        $queryPersonas = Persona::whereIn('tipo_persona', ['empleado', 'estudiante']);
        if ($grupoIds !== null) {
            $queryPersonas->whereIn('id_grupo', $grupoIds);
        }
        $totalPersonas = $queryPersonas->count();
        
        $presentesHoy = $asistenciasHoy->get('Presente', 0);
        $tardesHoy = $asistenciasHoy->get('Tarde', 0);
        $asistieronHoy = $presentesHoy + $tardesHoy;
        $faltasHoy = $totalPersonas - $asistieronHoy;

        return [
            'labels' => ['Presente', 'Tarde', 'Falta'],
            'data' => [$presentesHoy, $tardesHoy, $faltasHoy < 0 ? 0 : $faltasHoy]
        ];
    }

    private function getAsistenciaAreaChartSegunRol($rol, $grupoIds)
    {
        $query = Asistencia::join('personas', 'asistencias.id_persona', '=', 'personas.id_persona')
            ->join('areas', 'personas.id_area', '=', 'areas.id_area')
            ->whereDate('asistencias.fecha', today())
            ->select('areas.nombre_area', DB::raw('count(asistencias.id_asistencia) as total'))
            ->groupBy('areas.nombre_area');
        
        if ($grupoIds !== null) {
            $query->whereIn('personas.id_grupo', $grupoIds);
        }
        
        $asistenciasPorArea = $query->pluck('total', 'nombre_area');
            
        return [
            'labels' => $asistenciasPorArea->keys(),
            'data' => $asistenciasPorArea->values()
        ];
    }
}