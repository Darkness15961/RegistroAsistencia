<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AsistenciaController extends Controller
{
    /**
     * Listar asistencias (filtradas o generales)
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
     * [FUNCIÓN PRIVADA] Normaliza el estado (P, T, F).
     */
    private function normalizarEstadoAsistencia($estadoRaw): string
    {
        if (!$estadoRaw) return 'F';
        
        $v = Str::lower(trim($estadoRaw));

        if (in_array($v, ['presente', 'p', 'pres'])) return 'P';
        if (in_array($v, ['tarde', 't'])) return 'T';
        if (in_array($v, ['falta', 'ausente', 'f'])) return 'F';

        $first = strtoupper(substr($v, 0, 1));
        return in_array($first, ['P','T','F']) ? $first : 'F';
    }

    /**
     * Resumen semanal adaptado al tipo de persona.
     * - Estudiantes: Lun-Sáb.
     * - Empleados: Lun-Dom.
     */
    public function asistenciasSemana(Request $request)
    {
        $usuario = Auth::user();
        $persona = $usuario ? $usuario->persona : null;
        
        $groupId = $request->query('group_id');
        $dateParam = $request->query('date');

        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $inicioSemana = (clone $baseDate)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $finSemana = (clone $inicioSemana)->addDays(6)->endOfDay();

        $queryAsistencias = Asistencia::with('persona')
            ->whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()]);

        // Aplicar filtros de acceso basados en rol
        if ($usuario) {
            $queryAsistencias = $this->aplicarFiltrosDeAcceso($queryAsistencias, $usuario, $persona);
        }

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

            $dias = [
                'lunes' => null,
                'martes' => null,
                'miercoles' => null,
                'jueves' => null,
                'viernes' => null,
                'sabado' => null,
            ];

            if (in_array($persona->tipo_persona, ['empleado', 'docente', 'administrativo'])) {
                $dias['domingo'] = null;
            }

            foreach ($registros as $asistencia) {
                $diaDeLaSemana = Carbon::parse($asistencia->fecha)->dayOfWeek;
                if (isset($dayMap[$diaDeLaSemana])) {
                    $llaveDia = $dayMap[$diaDeLaSemana];
                    if (array_key_exists($llaveDia, $dias)) {
                        $dias[$llaveDia] = [
                            'estado' => $this->normalizarEstadoAsistencia($asistencia->estado_asistencia),
                            'hora_entrada' => $asistencia->hora_entrada ? Carbon::parse($asistencia->hora_entrada)->format('H:i') : null,
                            'hora_salida' => $asistencia->hora_salida ? Carbon::parse($asistencia->hora_salida)->format('H:i') : null,
                        ];
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
                    'tipo_persona' => $persona->tipo_persona,
                ],
                ...$dias,
            ];
        })->values();

        return response()->json($resumen);
    }

    /**
     * Registrar asistencia (IA o Manual)
     * 
     * REGLAS DE NEGOCIO:
     * - ESTUDIANTES: Marcan solo ENTRADA (1h antes hasta 15min después)
     * - PERSONAL: Marca ENTRADA (30min antes hasta 15min después) y SALIDA (hasta 15min después)
     * - ANTI-REBOTE: Solo se acepta el primer registro, intentos posteriores muestran "Usuario ya registrado"
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'fecha' => 'nullable|date',
        ]);

        $persona = Persona::find($request->id_persona);
        
        // Forzar zona horaria America/Lima
        $fecha = $request->input('fecha', Carbon::now('America/Lima')->toDateString());
        $horaActualCarbon = Carbon::now('America/Lima');
        $horaActualStr = $horaActualCarbon->toTimeString();

        // Helper para normalizar horas
        $normalizeHora = function ($hora) {
            if ($hora === null) return null;
            $h = trim($hora);
            if ($h === '') return null;
            if (preg_match('/^\d{2}:\d{2}$/', $h)) return $h . ':00';
            try { 
                return Carbon::parse($h)->format('H:i:s'); 
            } catch (\Exception $e) { 
                return null; 
            }
        };

        $horaEntradaInput = $normalizeHora($request->input('hora_entrada'));
        $horaSalidaInput = $normalizeHora($request->input('hora_salida'));

        // Buscar registro existente del día
        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $fecha,
        ]);

        $estadoRecibido = $request->input('estado_asistencia');

        // ========================================
        // CASO 1: REGISTRO MANUAL (Admin/Docente)
        // ========================================
        if ($estadoRecibido) {
            $asistencia->metodo_registro = 'Manual';
            
            // Normalizar estado
            $v = Str::lower(trim($estadoRecibido));
            if (in_array($v, ['presente', 'p', 'pres'])) {
                $asistencia->estado_asistencia = 'Presente';
            } elseif (in_array($v, ['tarde', 't'])) {
                $asistencia->estado_asistencia = 'Tarde';
            } elseif (in_array($v, ['falta', 'f', 'ausente'])) {
                $asistencia->estado_asistencia = 'Falta';
            } else {
                $asistencia->estado_asistencia = ucfirst($v);
            }

            // Asignar horas si vienen
            if ($horaEntradaInput) $asistencia->hora_entrada = $horaEntradaInput;
            if ($horaSalidaInput) $asistencia->hora_salida = $horaSalidaInput;

            $asistencia->save();
            return response()->json($asistencia->load('persona'), 201);
        }

        // ========================================
        // CASO 2: REGISTRO AUTOMÁTICO (IA - Facial)
        // ========================================
        $asistencia->metodo_registro = 'IA';

        // Obtener horario del área
        $horario = Horario::where('id_area', $persona->id_area)->first();
        
        if (!$horario) {
            return response()->json([
                'message' => 'No hay horario configurado para esta área',
                'persona' => $persona
            ], 400);
        }

        // ----------------------------------------
        // A) REGISTRO DE ENTRADA
        // ----------------------------------------
        if (is_null($asistencia->hora_entrada)) {
            
            // IMPORTANTE: Parsear con la fecha actual para comparaciones correctas
            $fechaHoy = $horaActualCarbon->toDateString();
            $horaEntradaProgramada = Carbon::parse($fechaHoy . ' ' . $horario->hora_entrada, 'America/Lima');
            
            // Ventanas según tipo de persona
            if ($persona->tipo_persona === 'estudiante') {
                // ESTUDIANTES: 1 hora antes hasta 15 min después
                $ventanaInicio = $horaEntradaProgramada->copy()->subMinutes(60);
                $ventanaFin = $horaEntradaProgramada->copy()->addMinutes(15);
            } else {
                // PERSONAL: 30 min antes hasta 15 min después
                $ventanaInicio = $horaEntradaProgramada->copy()->subMinutes(30);
                $ventanaFin = $horaEntradaProgramada->copy()->addMinutes(15);
            }

            // Validar ventana de entrada
            if ($horaActualCarbon->lt($ventanaInicio)) {
                return response()->json([
                    'message' => 'Aún no puede marcar asistencia. Intente después de las ' . $ventanaInicio->format('H:i'),
                    'persona' => $persona
                ], 400);
            }

            if ($horaActualCarbon->gt($ventanaFin)) {
                return response()->json([
                    'message' => 'Tiempo de marcado expirado. Contacte al encargado para registro manual.',
                    'persona' => $persona
                ], 400);
            }

            // Registrar entrada
            $asistencia->hora_entrada = $horaActualStr;

            // Determinar estado (Presente o Tarde)
            if ($horaActualCarbon->lte($horaEntradaProgramada)) {
                $asistencia->estado_asistencia = 'Presente';
            } else {
                $asistencia->estado_asistencia = 'Tarde';
            }

            $asistencia->save();

            return response()->json([
                'message' => 'Entrada registrada exitosamente',
                'asistencia' => $asistencia->load('persona'),
                'estado' => $asistencia->estado_asistencia
            ], 201);
        }

        // ----------------------------------------
        // B) YA TIENE ENTRADA REGISTRADA
        // ----------------------------------------
        
        // 1. ANTI-REBOTE: Verificar si es estudiante intentando marcar de nuevo
        if ($persona->tipo_persona === 'estudiante') {
            return response()->json([
                'message' => 'Usuario ya registrado. Los estudiantes solo marcan entrada.',
                'persona' => $persona,
                'estado_asistencia' => $asistencia->estado_asistencia
            ], 200);
        }

        // 2. Verificar si ya tiene salida registrada
        if (!is_null($asistencia->hora_salida)) {
            return response()->json([
                'message' => 'Usuario ya registrado. Entrada y salida completas.',
                'persona' => $persona,
                'estado_asistencia' => $asistencia->estado_asistencia
            ], 200);
        }

        // 3. ANTI-REBOTE: Evitar marcado duplicado muy rápido
        $tiempoEntrada = Carbon::parse($asistencia->hora_entrada);
        $diferenciaMinutos = $horaActualCarbon->diffInMinutes($tiempoEntrada);

        if ($diferenciaMinutos < 30) {
            return response()->json([
                'message' => 'Usuario ya registrado. La salida se habilitará después de 30 minutos.',
                'persona' => $persona,
                'estado_asistencia' => $asistencia->estado_asistencia
            ], 200);
        }

        // ----------------------------------------
        // C) REGISTRO DE SALIDA (Solo Personal)
        // ----------------------------------------
        
        $fechaHoy = $horaActualCarbon->toDateString();
        $horaSalidaProgramada = Carbon::parse($fechaHoy . ' ' . $horario->hora_salida, 'America/Lima');
        $ventanaSalidaFin = $horaSalidaProgramada->copy()->addMinutes(15);

        // Validar ventana de salida
        if ($horaActualCarbon->gt($ventanaSalidaFin)) {
            return response()->json([
                'message' => 'Tiempo de marcado de salida expirado. Contacte al encargado.',
                'persona' => $persona
            ], 400);
        }

        // Registrar salida
        $asistencia->hora_salida = $horaActualStr;

        // Marcar si salió fuera de tiempo
        if ($horaActualCarbon->gt($ventanaSalidaFin)) {
            $asistencia->salida_fuera_tiempo = true;
        } else {
            $asistencia->salida_fuera_tiempo = false;
        }

        $asistencia->save();

        return response()->json([
            'message' => 'Salida registrada exitosamente',
            'asistencia' => $asistencia->load('persona'),
            'fuera_tiempo' => $asistencia->salida_fuera_tiempo
        ], 201);
    }
    public function show(Asistencia $asistencia)
    {
        return $asistencia->load('persona');
    }

    public function update(Request $request, Asistencia $asistencia)
    {
        // 'nullable|string' para permitir borrar horas (en caso de Falta)
        $request->validate([
            'fecha' => 'sometimes|date',
            'hora_entrada' => 'nullable|string',
            'hora_salida' => 'nullable|string',
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
        
        // Permitimos enviar NULL explícitamente para borrar la hora
        if ($request->has('hora_entrada')) $payload['hora_entrada'] = $normalizeHora($request->input('hora_entrada'));
        if ($request->has('hora_salida')) $payload['hora_salida'] = $normalizeHora($request->input('hora_salida'));
        
        if (!is_null($normalizedEstado)) $payload['estado_asistencia'] = $normalizedEstado;
        if ($request->filled('metodo_registro')) $payload['metodo_registro'] = $request->input('metodo_registro');

        $asistencia->update($payload);

        return response()->json($asistencia->load('persona'), 200);
    }

    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json(['message' => 'Asistencia eliminada correctamente.'], 200);
    }

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
            $fechaFin = $fechaInicio->copy()->addDays(6);

            $dias = [
                'lunes' => null, 'martes' => null, 'miercoles' => null, 'jueves' => null, 
                'viernes' => null, 'sabado' => null, 'domingo' => null,
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

        usort($historial, function ($a, $b) {
            return $b['semana'] <=> $a['semana'];
        });

        return response()->json($historial);
    }

    /**
     * Resumen semanal de salidas (solo personal)
     */
    public function asistenciasSalidaSemana(Request $request)
    {
        $usuario = Auth::user();
        $persona = $usuario ? $usuario->persona : null;
        
        // Docentes no ven salidas
        if ($usuario && $usuario->rol === 'docente') {
            return response()->json([]);
        }
        
        $groupId = $request->query('group_id');
        $dateParam = $request->query('date');

        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $inicioSemana = (clone $baseDate)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $finSemana = (clone $inicioSemana)->addDays(6)->endOfDay();

        $queryAsistencias = Asistencia::with('persona')
            ->whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()])
            ->whereNotNull('hora_salida'); // Solo con salida registrada

        // Aplicar filtros de acceso basados en rol
        if ($usuario) {
            $queryAsistencias = $this->aplicarFiltrosDeAcceso($queryAsistencias, $usuario, $persona);
        }

        $queryPersonas = Persona::query()
            ->whereIn('tipo_persona', ['empleado', 'docente', 'administrativo']);

        if ($groupId) {
            $queryAsistencias->whereHas('persona', function ($q) use ($groupId) {
                $q->where('id_grupo', $groupId)
                  ->whereIn('tipo_persona', ['empleado', 'docente', 'administrativo']);
            });
            $queryPersonas->where('id_grupo', $groupId);
        }

        $todasLasAsistencias = $queryAsistencias->get();
        $todasLasPersonas = $queryPersonas->get()->keyBy('id_persona');
        $asistenciasAgrupadas = $todasLasAsistencias->groupBy('id_persona');

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

            $dias = [
                'lunes' => null,
                'martes' => null,
                'miercoles' => null,
                'jueves' => null,
                'viernes' => null,
                'sabado' => null,
                'domingo' => null,
            ];

            foreach ($registros as $asistencia) {
                $diaDeLaSemana = Carbon::parse($asistencia->fecha)->dayOfWeek;
                if (isset($dayMap[$diaDeLaSemana])) {
                    $llaveDia = $dayMap[$diaDeLaSemana];
                    if (array_key_exists($llaveDia, $dias)) {
                        $dias[$llaveDia] = [
                            'hora_salida' => $asistencia->hora_salida ? Carbon::parse($asistencia->hora_salida)->format('H:i') : null,
                            'fuera_tiempo' => $asistencia->salida_fuera_tiempo ?? false,
                        ];
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
                    'tipo_persona' => $persona->tipo_persona,
                ],
                ...$dias,
            ];
        })->values();

        return response()->json($resumen);
    }

    /**
     * Aplicar filtros de acceso según rol del usuario
     */
    private function aplicarFiltrosDeAcceso($query, $usuario, $persona)
    {
        if ($usuario->rol === 'admin') {
            return $query; // Sin filtros
        }
        
        if ($usuario->rol === 'supervisor') {
            // Solo personal
            $query->whereHas('persona', function($q) {
                $q->whereIn('tipo_persona', ['empleado', 'docente', 'administrativo']);
            });
        }
        
        if ($usuario->rol === 'docente') {
            // Solo estudiantes de sus grupos
            $gruposIds = Grupo::where('id_tutor', $persona->id_persona)->pluck('id_grupo');
            $query->whereHas('persona', function($q) use ($gruposIds) {
                $q->whereIn('id_grupo', $gruposIds)
                  ->where('tipo_persona', 'estudiante');
            });
        }
        
        return $query;
    }
}