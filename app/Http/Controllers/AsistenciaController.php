<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AsistenciaController extends Controller
{
    /**
     * Listar asistencias (filtradas o generales)
     * Soporta: ?id_persona= X, ?fecha=YYYY-MM-DD, ?per_page=N
     */
    public function index(Request $request)
    {
        $query = Asistencia::with('persona');

        if ($request->filled('id_persona')) {
            $query->where('id_persona', $request->id_persona);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $perPage = $request->query('per_page', 10);
        $asistencias = $query->orderBy('fecha', 'desc')->paginate($perPage);

        return response()->json($asistencias);
    }

    /**
     * [FUNCIÓN PRIVADA]
     * Normaliza el estado de la base de datos a códigos unificados (P, T, F).
     * Si recibe algo vacío, retorna 'F' (asumiendo que si hay fila creada pero sin estado, es un error o falta).
     * NOTA: Si no hay fila en la BD, esta función no se llama (quedará en NULL).
     */
    private function normalizarEstadoAsistencia($estadoRaw): string
    {
        if (!$estadoRaw) return 'F';
        
        $v = Str::lower(trim($estadoRaw));

        if (in_array($v, ['presente', 'p', 'pres'])) return 'P';
        if (in_array($v, ['tarde', 't'])) return 'T';
        if (in_array($v, ['falta', 'ausente', 'f'])) return 'F';

        // Fallback: tomar primera letra
        $first = strtoupper(substr($v, 0, 1));
        return in_array($first, ['P','T','F']) ? $first : 'F';
    }

    /**
     * Resumen semanal adaptado al tipo de persona.
     * - Estudiantes: Lunes a Sábado.
     * - Empleados: Lunes a Domingo.
     * - Días sin registro: NULL (el frontend mostrará guion "-").
     */
    public function asistenciasSemana(Request $request)
    {
        $groupId = $request->query('group_id');
        $dateParam = $request->query('date');

        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $inicioSemana = (clone $baseDate)->startOfWeek(Carbon::MONDAY)->startOfDay();
        
        // Consultamos siempre hasta el DOMINGO para tener la data completa de empleados
        $finSemana = (clone $inicioSemana)->addDays(6)->endOfDay();

        $queryAsistencias = Asistencia::with('persona')
            ->whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()]);

        $queryPersonas = Persona::query();

        if ($groupId) {
            $queryAsistencias->whereHas('persona', function ($q) use ($groupId) {
                $q->where('id_grupo', $groupId);
            });
            $queryPersonas->where('id_grupo', $groupId);
        }

        $todasLasAsistencias = $queryAsistencias->get();
        $todasLasPersonas = $queryPersonas->get()->keyBy('id_persona');
        $asistenciasAgrupadas = $todasLasAsistencias->groupBy('id_persona');

        // Mapa completo de días para buscar en la BD (Lunes=1 ... Domingo=7)
        $dayMap = [
            Carbon::MONDAY => 'lunes',
            Carbon::TUESDAY => 'martes',
            Carbon::WEDNESDAY => 'miercoles',
            Carbon::THURSDAY => 'jueves',
            Carbon::FRIDAY => 'viernes',
            Carbon::SATURDAY => 'sabado',
            Carbon::SUNDAY => 'domingo',
        ];

        $resumen = $todasLasPersonas->map(function ($persona) use ($asistenciasAgrupadas, $dayMap) {
            $registros = $asistenciasAgrupadas->get($persona->id_persona, collect());

            // 1. Estructura Base: Lunes a Sábado (Común para todos)
            // Inicializamos en NULL para indicar "Sin registro"
            $dias = [
                'lunes' => null,
                'martes' => null,
                'miercoles' => null,
                'jueves' => null,
                'viernes' => null,
                'sabado' => null,
            ];

            // 2. Lógica Diferenciada: Agregamos Domingo solo si es empleado
            // (Verifica cómo guardas el 'tipo_persona' en tu BD: 'empleado', 'docente', etc.)
            if (in_array($persona->tipo_persona, ['empleado', 'docente', 'administrativo'])) {
                $dias['domingo'] = null;
            }

            // 3. Rellenar con datos reales de la BD
            foreach ($registros as $asistencia) {
                $diaDeLaSemana = Carbon::parse($asistencia->fecha)->dayOfWeek;

                // Verificamos si el día es válido en el mapa
                if (isset($dayMap[$diaDeLaSemana])) {
                    $llaveDia = $dayMap[$diaDeLaSemana];
                    
                    // Solo asignamos el valor si la llave existe en el array $dias de esta persona
                    // (Esto evita poner datos de domingo a un estudiante que no tiene esa columna)
                    if (array_key_exists($llaveDia, $dias)) {
                        $dias[$llaveDia] = $this->normalizarEstadoAsistencia($asistencia->estado_asistencia);
                    }
                }
            }

            return [
                'id_persona' => $persona->id_persona,
                'persona' => [
                    'nombre_completo' => $persona->nombre_completo,
                    'cargo_grado' => $persona->cargo_grado,
                    'id_area' => $persona->id_area,
                    'id_grupo' => $persona->id_grupo,
                    'tipo_persona' => $persona->tipo_persona, // Enviamos el tipo para control en frontend
                ],
                ...$dias,
            ];
        })->values();

        return response()->json($resumen);
    }

    /**
     * Registrar asistencia (IA o Manual)
     * Calcula automáticamente estado (Presente/Tarde) según horario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'fecha' => 'nullable|date',
        ]);

        $persona = Persona::find($request->id_persona);
        $fecha = $request->input('fecha', Carbon::now()->toDateString());

        // Helper para normalizar hora HH:MM -> HH:MM:SS
        $normalizeHora = function ($hora) {
            if (!$hora) return null;
            if (preg_match('/^\d{2}:\d{2}$/', $hora)) return $hora . ':00';
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora)) return $hora;
            try {
                return Carbon::parse($hora)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        $horaEntradaInput = $normalizeHora($request->input('hora_entrada'));
        $horaSalidaInput = $normalizeHora($request->input('hora_salida'));

        // Buscar horario del área
        $horario = Horario::where('id_area', $persona->id_area)->first();
        
        // Si no hay horario, podrías retornar error 404 o dejar pasar. 
        // Aquí retornamos error para asegurar consistencia.
        if (!$horario) {
             return response()->json(['error' => 'La persona no tiene un horario asignado en su área'], 404);
        }

        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $fecha,
        ]);

        $estadoRecibido = $request->input('estado_asistencia');

        // CASO 1: Registro Manual con estado explícito
        if ($estadoRecibido) {
            $v = Str::lower(trim($estadoRecibido));
            if (in_array($v, ['presente','p','pres'])) $asistencia->estado_asistencia = 'Presente';
            elseif (in_array($v, ['tarde','t'])) $asistencia->estado_asistencia = 'Tarde';
            elseif (in_array($v, ['falta','f','ausente'])) $asistencia->estado_asistencia = 'Falta';
            else $asistencia->estado_asistencia = ucfirst($v);

            if ($horaEntradaInput) $asistencia->hora_entrada = $horaEntradaInput;
            if ($horaSalidaInput) $asistencia->hora_salida = $horaSalidaInput;
            $asistencia->metodo_registro = $request->input('metodo_registro', 'Manual');
        
        // CASO 2: Registro Automático (IA)
        } else {
            // Si no hay hora de entrada, registramos ENTRADA
            if (is_null($asistencia->hora_entrada)) {
                $horaActual = $horaEntradaInput ?: Carbon::now()->toTimeString();
                $asistencia->hora_entrada = $horaActual;

                // Cálculo automático de Tardanza
                if ($horario) {
                    $horaLimiteEntrada = Carbon::parse($horario->hora_entrada);
                    // Puedes agregar minutos de tolerancia aquí, ej: ->addMinutes(5)
                    $asistencia->estado_asistencia = Carbon::parse($horaActual)->lte($horaLimiteEntrada)
                        ? 'Presente'
                        : 'Tarde';
                } else {
                    $asistencia->estado_asistencia = 'Presente'; // Fallback si no hay horario
                }

                $asistencia->metodo_registro = 'IA';
            
            // Si ya hay entrada, registramos SALIDA
            } else {
                $asistencia->hora_salida = $horaSalidaInput ?: Carbon::now()->toTimeString();
            }
        }

        $asistencia->save();

        return response()->json($asistencia->load('persona'), 201);
    }

    /**
     * Mostrar asistencia específica
     */
    public function show(Asistencia $asistencia)
    {
        return $asistencia->load('persona');
    }

    /**
     * Actualizar asistencia manualmente
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        $request->validate([
            'fecha' => 'sometimes|date',
            'hora_entrada' => 'nullable|sometimes|string',
            'hora_salida' => 'nullable|sometimes|string',
            'estado_asistencia' => 'sometimes|string',
            'metodo_registro' => ['sometimes','string', Rule::in(['IA','Manual'])],
        ]);

        $normalizeHora = function ($hora) {
            if ($hora === null) return null;
            $h = trim($hora);
            if ($h === '') return null;
            if (preg_match('/^\d{2}:\d{2}$/', $h)) return $h . ':00';
            try { return Carbon::parse($h)->format('H:i:s'); } catch (\Exception $e) { return null; }
        };

        $estado = $request->input('estado_asistencia');
        if (!is_null($estado)) {
            $v = Str::lower(trim($estado));
            if (in_array($v, ['presente','p','pres'])) $normalizedEstado = 'Presente';
            elseif (in_array($v, ['tarde','t'])) $normalizedEstado = 'Tarde';
            elseif (in_array($v, ['falta','f','ausente'])) $normalizedEstado = 'Falta';
            else $normalizedEstado = ucfirst($v);
        } else {
            $normalizedEstado = null;
        }

        $payload = [];
        if ($request->filled('fecha')) $payload['fecha'] = $request->input('fecha');
        if ($request->has('hora_entrada')) $payload['hora_entrada'] = $normalizeHora($request->input('hora_entrada'));
        if ($request->has('hora_salida')) $payload['hora_salida'] = $normalizeHora($request->input('hora_salida'));
        if (!is_null($normalizedEstado)) $payload['estado_asistencia'] = $normalizedEstado;
        if ($request->filled('metodo_registro')) $payload['metodo_registro'] = $request->input('metodo_registro');

        $asistencia->update($payload);

        return response()->json($asistencia->load('persona'), 200);
    }

    /**
     * Eliminar asistencia
     */
    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json(['message' => 'Asistencia eliminada correctamente.'], 200);
    }

    /**
     * HISTORIAL COMPLETO POR PERSONA
     * Muestra historial completo, usando NULL para días sin registro.
     */
    public function historialPersona(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona'
        ]);

        $id = $request->id_persona;

        $asistencias = Asistencia::where('id_persona', $id)
            ->orderBy('fecha', 'desc')
            ->get()
            ->groupBy(function ($item) {
                // Agrupar por Año-Semana (ej: 2025-46)
                return Carbon::parse($item->fecha)->format('o-W');
            });

        $historial = [];
        
        $dayMap = [
            Carbon::MONDAY => 'lunes',
            Carbon::TUESDAY => 'martes',
            Carbon::WEDNESDAY => 'miercoles',
            Carbon::THURSDAY => 'jueves',
            Carbon::FRIDAY => 'viernes',
            Carbon::SATURDAY => 'sabado',
            Carbon::SUNDAY => 'domingo',
        ];

        foreach ($asistencias as $semana => $registros) {
            $fechaPrimerRegistro = Carbon::parse($registros->first()->fecha);
            $fechaInicio = $fechaPrimerRegistro->copy()->startOfWeek(Carbon::MONDAY);
            $fechaFin = $fechaInicio->copy()->addDays(6); // Semana completa

            // Inicializar en NULL (Sin registro)
            $dias = [
                'lunes' => null,
                'martes' => null,
                'miercoles' => null,
                'jueves' => null,
                'viernes' => null,
                'sabado' => null,
                'domingo' => null,
            ];

            foreach ($registros as $a) {
                $diaDeLaSemana = Carbon::parse($a->fecha)->dayOfWeek;
                if (isset($dayMap[$diaDeLaSemana])) {
                    $llaveDia = $dayMap[$diaDeLaSemana];
                    $dias[$llaveDia] = $this->normalizarEstadoAsistencia($a->estado_asistencia);
                }
            }

            $historial[] = [
                'semana' => $semana,
                'fecha_inicio' => $fechaInicio->toDateString(),
                'fecha_fin' => $fechaFin->toDateString(),
                ...$dias
            ];
        }

        // Ordenar: Semanas más recientes primero
        usort($historial, function ($a, $b) {
            return $b['semana'] <=> $a['semana'];
        });

        return response()->json($historial);
    }
}