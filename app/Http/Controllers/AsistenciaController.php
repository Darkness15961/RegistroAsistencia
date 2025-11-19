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
        $groupId = $request->query('group_id');
        $dateParam = $request->query('date');

        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $inicioSemana = (clone $baseDate)->startOfWeek(Carbon::MONDAY)->startOfDay();
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
                    'tipo_persona' => $persona->tipo_persona,
                ],
                ...$dias,
            ];
        })->values();

        return response()->json($resumen);
    }

    /**
     * Registrar asistencia (IA o Manual)
     */
    /**
     * Registrar asistencia (IA o Manual)
     * Lógica inteligente para evitar dobles registros accidentales.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'fecha' => 'nullable|date',
        ]);

        $persona = Persona::find($request->id_persona);
        // Forzamos zona horaria America/Lima
        $fecha = $request->input('fecha', Carbon::now('America/Lima')->toDateString());
        $horaActualCarbon = Carbon::now('America/Lima'); // Objeto Carbon para cálculos
        $horaActualStr = $horaActualCarbon->toTimeString(); // String para la BD

        // ... (Tu helper normalizeHora sigue igual aquí) ...
        $normalizeHora = function ($hora) { /* ... */ }; // (Mantenlo como lo tienes)
        $horaEntradaInput = $normalizeHora($request->input('hora_entrada'));
        $horaSalidaInput = $normalizeHora($request->input('hora_salida'));


        // Buscar o crear el registro del día
        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $fecha,
        ]);

        $estadoRecibido = $request->input('estado_asistencia');

        // --- CASO 1: MANUAL (Admin corrige o crea) ---
        if ($estadoRecibido) {
            // ... (Tu lógica manual existente se queda igual) ...
            // Copia tu lógica manual aquí (presente/tarde/falta)
            $asistencia->metodo_registro = 'Manual';
            // ...
        } 
        
        // --- CASO 2: AUTOMÁTICO (IA - Escaneo Facial) ---
        else {
            $asistencia->metodo_registro = 'IA';

            // A) NO TIENE ENTRADA -> REGISTRAR ENTRADA
            if (is_null($asistencia->hora_entrada)) {
                $asistencia->hora_entrada = $horaActualStr;

                // Lógica de horario y tardanza
                $horario = Horario::where('id_area', $persona->id_area)->first();
                if ($horario) {
                    $horaLimite = Carbon::parse($horario->hora_entrada);
                    $asistencia->estado_asistencia = $horaActualCarbon->lte($horaLimite) ? 'Presente' : 'Tarde';
                } else {
                    $asistencia->estado_asistencia = 'Presente';
                }
            
            // B) YA TIENE ENTRADA... ¿QUÉ HACEMOS?
            } else {
                
                // 1. Validar si YA TIENE SALIDA
                if (!is_null($asistencia->hora_salida)) {
                    // Si ya marcó todo, no hacemos nada para no sobreescribir por error
                    return response()->json([
                        'message' => 'La asistencia de hoy ya está completa (Entrada y Salida registradas).',
                        'persona' => $persona,
                        'estado_asistencia' => $asistencia->estado_asistencia
                    ], 200); 
                }

                // 2. LÓGICA ANTI-REBOTE (Evitar doble registro por error)
                // Calculamos tiempo desde la entrada
                $tiempoEntrada = Carbon::parse($asistencia->hora_entrada);
                $diferenciaMinutos = $horaActualCarbon->diffInMinutes($tiempoEntrada);

                // Si pasaron menos de 30 minutos desde la entrada, asumimos que es un error/duplicado
                if ($diferenciaMinutos < 30) {
                     return response()->json([
                        'message' => 'Entrada ya registrada hace ' . $diferenciaMinutos . ' minutos. La salida se habilitará después de 30 min.',
                        'persona' => $persona,
                        'estado_asistencia' => $asistencia->estado_asistencia
                    ], 200);
                }

                // 3. LÓGICA DE SALIDA OPCIONAL (Solo Personal marca salida)
                // los alumnos NO registran salida
                
                if ($persona->tipo_persona === 'estudiante') {
                     return response()->json([
                        'message' => 'Entrada registrada correctamente. (Alumnos no marcan salida)',
                        'persona' => $persona,
                         'estado_asistencia' => $asistencia->estado_asistencia
                    ], 200);
                }
                

                // Si pasa todas las validaciones, registramos la SALIDA
                $asistencia->hora_salida = $horaActualStr;
            }
        }

        $asistencia->save();

        return response()->json($asistencia->load('persona'), 201);
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
}