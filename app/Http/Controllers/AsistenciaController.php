<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str; // Asegúrate que Str esté importado

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
     * [NUEVA FUNCIÓN PRIVADA]
     * Centraliza la lógica para convertir el estado de la BD (ej: "Presente", "tarde")
     * en un código unificado (P, T, F).
     */
    private function normalizarEstadoAsistencia($estadoRaw): string
    {
        if (!$estadoRaw) return 'F';
        
        // Limpiamos y pasamos a minúsculas
        $v = Str::lower(trim($estadoRaw));

        if (in_array($v, ['presente', 'p', 'pres'])) return 'P';
        if (in_array($v, ['tarde', 't'])) return 'T';
        // Acepta "falta", "ausente" o "f"
        if (in_array($v, ['falta', 'ausente', 'f'])) return 'F';

        // Fallback: si es algo desconocido, tomar la primera letra
        $first = strtoupper(substr($v, 0, 1));
        return in_array($first, ['P','T','F']) ? $first : 'F';
    }


    /**
     * Resumen semanal (Lun–Vie)
     * [MÉTODO REFACTORIZADO]
     */
    public function asistenciasSemana(Request $request)
    {
        $groupId = $request->query('group_id');
        $dateParam = $request->query('date');

        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $inicioSemana = (clone $baseDate)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $finSemana = (clone $inicioSemana)->addDays(4)->endOfDay();

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

        // Mapa de Día de la Semana (Lunes=1, Martes=2, etc. según Carbon) a nuestra llave
        $dayMap = [
            Carbon::MONDAY => 'lunes',
            Carbon::TUESDAY => 'martes',
            Carbon::WEDNESDAY => 'miercoles',
            Carbon::THURSDAY => 'jueves',
            Carbon::FRIDAY => 'viernes',
        ];

        $resumen = $todasLasPersonas->map(function ($persona) use ($asistenciasAgrupadas, $dayMap) {
            $registros = $asistenciasAgrupadas->get($persona->id_persona, collect());

            $dias = [
                'lunes' => 'F',
                'martes' => 'F',
                'miercoles' => 'F',
                'jueves' => 'F',
                'viernes' => 'F',
            ];

            foreach ($registros as $asistencia) {
                // Obtenemos el día de la semana (1-5)
                $diaDeLaSemana = Carbon::parse($asistencia->fecha)->dayOfWeek;

                // Verificamos si es un día laboral (L-V) usando nuestro mapa
                if (isset($dayMap[$diaDeLaSemana])) {
                    $llaveDia = $dayMap[$diaDeLaSemana];
                    // Usamos la nueva función centralizada
                    $dias[$llaveDia] = $this->normalizarEstadoAsistencia($asistencia->estado_asistencia);
                }
            }

            return [
                'id_persona' => $persona->id_persona,
                'persona' => [
                    'nombre_completo' => $persona->nombre_completo,
                    'cargo_grado' => $persona->cargo_grado,
                    'id_area' => $persona->id_area,
                    'id_grupo' => $persona->id_grupo,
                ],
                ...$dias,
            ];
        })->values();

        return response()->json($resumen);
    }

    /**
     * Registrar asistencia (IA o Manual)
     * (Este método no se modifica)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'fecha' => 'nullable|date',
            // hora_entrada/hora_salida se validarán manualmente y normalizarán
        ]);

        $persona = Persona::find($request->id_persona);
        $fecha = $request->input('fecha', Carbon::now()->toDateString());

        // Normalizar hora (agrega segundos si viene HH:mm)
        $normalizeHora = function ($hora) {
            if (!$hora) return null;
            if (preg_match('/^\d{2}:\d{2}$/', $hora)) return $hora . ':00';
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora)) return $hora;
            // intentar parseo con Carbon
            try {
                return Carbon::parse($hora)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        $horaEntradaInput = $normalizeHora($request->input('hora_entrada'));
        $horaSalidaInput = $normalizeHora($request->input('hora_salida'));

        $horario = Horario::where('id_area', $persona->id_area)->first();
        if (!$horario) {
            return response()->json(['error' => 'La persona no tiene un horario asignado'], 404);
        }

        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $fecha,
        ]);

        // Si viene estado_asistencia explícito (por ejemplo cuando creas manualmente),
        // normalizamos a Presente/Tarde/Falta/Ausente.
        $estadoRecibido = $request->input('estado_asistencia');

        if ($estadoRecibido) {
            $v = Str::lower(trim($estadoRecibido));
            if (in_array($v, ['presente','p','pres'])) $asistencia->estado_asistencia = 'Presente';
            elseif (in_array($v, ['tarde','t'])) $asistencia->estado_asistencia = 'Tarde';
            elseif (in_array($v, ['falta','f','ausente'])) $asistencia->estado_asistencia = 'Falta';
            else $asistencia->estado_asistencia = ucfirst($v);
            // si se envía hora_entrada la usamos
            if ($horaEntradaInput) $asistencia->hora_entrada = $horaEntradaInput;
            if ($horaSalidaInput) $asistencia->hora_salida = $horaSalidaInput;
            $asistencia->metodo_registro = $request->input('metodo_registro', 'Manual');
        } else {
            // comportamiento por defecto: registrar hora de entrada si no existe
            if (is_null($asistencia->hora_entrada)) {
                $horaActual = $horaEntradaInput ?: Carbon::now()->toTimeString();
                $asistencia->hora_entrada = $horaActual;

                $horaLimiteEntrada = Carbon::parse($horario->hora_entrada);
                $asistencia->estado_asistencia = Carbon::parse($horaActual)->lte($horaLimiteEntrada)
                    ? 'Presente'
                    : 'Tarde';

                $asistencia->metodo_registro = 'IA';
            } else {
                // ya tenía hora_entrada: registrar salida
                $asistencia->hora_salida = $horaSalidaInput ?: Carbon::now()->toTimeString();
            }
        }

        $asistencia->save();

        return response()->json($asistencia->load('persona'), 201);
    }

    /**
     * Mostrar asistencia
     * (Este método no se modifica)
     */
    public function show(Asistencia $asistencia)
    {
        return $asistencia->load('persona');
    }

    /**
     * Actualizar asistencia manualmente
     * (Este método no se modifica)
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        // Validación ligera: validamos tipos básicos y normalizamos manualmente
        $request->validate([
            'fecha' => 'sometimes|date',
            'hora_entrada' => 'sometimes|string',
            'hora_salida' => 'sometimes|string',
            'estado_asistencia' => 'sometimes|string',
            'metodo_registro' => ['sometimes','string', Rule::in(['IA','Manual'])],
        ]);

        // helper normalizador de hora
        $normalizeHora = function ($hora) {
            if ($hora === null) return null;
            $h = trim($hora);
            if ($h === '') return null;
            if (preg_match('/^\d{2}:\d{2}$/', $h)) return $h . ':00';
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $h)) return $h;
            try {
                return Carbon::parse($h)->format('H:i:s');
            } catch (\Exception $e) {
                // dejar como null si no parsea
                return null;
            }
        };

        // Normalizar estado:
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
     * (Este método no se modifica)
     */
    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json(['message' => 'Asistencia eliminada correctamente.'], 200);
    }

    /**
     * HISTORIAL COMPLETO POR PERSONA (todas las semanas)
     * [MÉTODO REFACTORIZADO]
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
                // 'o-W' -> año ISO y número de semana (ej: 2025-46)
                return Carbon::parse($item->fecha)->format('o-W');
            });

        $historial = [];

        // Mapa de Día de la Semana (Lunes=1, Martes=2, etc. según Carbon) a nuestra llave
        $dayMap = [
            Carbon::MONDAY => 'lunes',
            Carbon::TUESDAY => 'martes',
            Carbon::WEDNESDAY => 'miercoles',
            Carbon::THURSDAY => 'jueves',
            Carbon::FRIDAY => 'viernes',
        ];

        foreach ($asistencias as $semana => $registros) {
            // Usamos el primer registro de la semana para definir las fechas
            $fechaPrimerRegistro = Carbon::parse($registros->first()->fecha);
            $fechaInicio = $fechaPrimerRegistro->copy()->startOfWeek(Carbon::MONDAY);
            $fechaFin = $fechaInicio->copy()->addDays(4); // Lunes + 4 días = Viernes

            $dias = [
                'lunes' => 'F',
                'martes' => 'F',
                'miercoles' => 'F',
                'jueves' => 'F',
                'viernes' => 'F'
            ];

            foreach ($registros as $a) {
                // Obtenemos el día de la semana (1-5)
                $diaDeLaSemana = Carbon::parse($a->fecha)->dayOfWeek;

                // Verificamos si es un día laboral (L-V) usando nuestro mapa
                if (isset($dayMap[$diaDeLaSemana])) {
                    $llaveDia = $dayMap[$diaDeLaSemana];
                    // Usamos la nueva función centralizada
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

        // Ordenamos el historial para que las semanas más recientes salgan primero
        // (El groupBy de collection no garantiza el orden)
        usort($historial, function ($a, $b) {
            return $b['semana'] <=> $a['semana'];
        });

        return response()->json($historial);
    }
}