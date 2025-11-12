<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * 🔹 Listar asistencias (filtradas o generales)
     */
    public function index(Request $request)
    {
        $query = Asistencia::with('persona');

        if ($request->has('id_persona')) {
            $query->where('id_persona', $request->id_persona);
        }

        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $asistencias = $query->orderBy('fecha', 'desc')->paginate(10);
        return response()->json($asistencias);
    }

    /**
     * 🔹 Nuevo método: resumen semanal (Lun–Vie)
     */
    public function asistenciasSemana()
    {
        // Obtener rango de la semana actual (Lunes a Viernes)
        $inicioSemana = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $finSemana = Carbon::now()->endOfWeek(Carbon::FRIDAY);

        // Traer asistencias de esta semana con persona
        $asistencias = Asistencia::with('persona')
            ->whereBetween('fecha', [$inicioSemana, $finSemana])
            ->get();

        // Agrupar por persona y generar letras P/T/F por día
        $resumen = $asistencias->groupBy('id_persona')->map(function ($registros) {
            $persona = $registros->first()->persona;

            $dias = [
                'lunes' => '-',
                'martes' => '-',
                'miercoles' => '-',
                'jueves' => '-',
                'viernes' => '-',
            ];

            foreach ($registros as $asistencia) {
                $dia = strtolower(Carbon::parse($asistencia->fecha)->isoFormat('dddd'));

                if (isset($dias[$dia])) {
                    switch (strtolower($asistencia->estado_asistencia)) {
                        case 'presente': $dias[$dia] = 'P'; break;
                        case 'tarde': $dias[$dia] = 'T'; break;
                        case 'falta': $dias[$dia] = 'F'; break;
                    }
                }
            }

            return [
                'id' => $persona->id_persona,
                'persona' => [
                    'nombre_completo' => $persona->nombre_completo,
                    'cargo_grado' => $persona->cargo_grado,
                ],
                ...$dias,
            ];
        })->values();

        return response()->json($resumen);
    }

    /**
     * 🔹 Registrar asistencia o salida
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'fecha' => 'nullable|date',
            'hora_entrada' => 'nullable|date_format:H:i:s',
            'hora_salida' => 'nullable|date_format:H:i:s',
        ]);

        $persona = Persona::find($request->id_persona);
        $fecha = $request->input('fecha', Carbon::now()->toDateString());
        $horaActual = $request->input('hora_entrada', Carbon::now()->toTimeString());

        $horario = Horario::where('id_area', $persona->id_area)->first();
        if (!$horario) {
            return response()->json(['error' => 'La persona no tiene un horario asignado'], 404);
        }

        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $fecha,
        ]);

        if (is_null($asistencia->hora_entrada)) {
            $asistencia->hora_entrada = $horaActual;

            $horaLimiteEntrada = Carbon::parse($horario->hora_entrada);
            $asistencia->estado_asistencia = Carbon::parse($horaActual)->lte($horaLimiteEntrada)
                ? 'Presente'
                : 'Tarde';

            $asistencia->metodo_registro = 'IA';
        } else {
            $asistencia->hora_salida = $request->input('hora_salida', Carbon::now()->toTimeString());
        }

        $asistencia->save();

        return response()->json($asistencia->load('persona'), 201);
    }

    /**
     * 🔹 Mostrar una asistencia
     */
    public function show(Asistencia $asistencia)
    {
        return $asistencia->load('persona');
    }

    /**
     * 🔹 Actualizar asistencia
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        $datosValidados = $request->validate([
            'fecha' => 'date',
            'hora_entrada' => 'nullable|date_format:H:i:s',
            'hora_salida' => 'nullable|date_format:H:i:s|after_or_equal:hora_entrada',
            'estado_asistencia' => 'string|in:Presente,Tarde,Falta',
            'metodo_registro' => 'string|in:IA,Manual',
        ]);
        
        $asistencia->update($datosValidados);
        return response()->json($asistencia->load('persona'), 200);
    }

    /**
     * 🔹 Eliminar asistencia
     */
    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json(null, 204);
    }
}
