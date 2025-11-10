<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Persona;
use App\Models\Horario;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si viene un id_persona en la URL
        $query = Asistencia::with('persona');

        if ($request->has('id_persona')) {
            $query->where('id_persona', $request->id_persona);
        }

        // También puedes filtrar por fecha si deseas (opcional)
        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        // Finalmente, devolver los resultados
        $asistencias = $query->orderBy('fecha', 'desc')->paginate(10);
        return response()->json($asistencias);
    }

    // ESTA ES LA LÓGICA 
        public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'fecha' => 'nullable|date',
            'hora_entrada' => 'nullable|date_format:H:i:s',
            'hora_salida' => 'nullable|date_format:H:i:s',
        ]);

        $persona = Persona::find($request->id_persona);
        $fecha = $request->input('fecha', Carbon::now()->toDateString()); // si no se envía, usa hoy
        $horaActual = $request->input('hora_entrada', Carbon::now()->toTimeString());

        // Buscar el horario del área
        $horario = Horario::where('id_area', $persona->id_area)->first();
        if (!$horario) {
            return response()->json(['error' => 'La persona no tiene un horario asignado'], 404);
        }

        // Buscar si ya existe asistencia para esa persona y fecha
        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $fecha,
        ]);

        // Si no tiene hora de entrada, registrar entrada
        if (is_null($asistencia->hora_entrada)) {
            $asistencia->hora_entrada = $horaActual;

            // Calcular estado según hora de entrada del horario
            $horaLimiteEntrada = Carbon::parse($horario->hora_entrada);
            if (Carbon::parse($horaActual)->lte($horaLimiteEntrada)) {
                $asistencia->estado_asistencia = 'Presente';
            } else {
                $asistencia->estado_asistencia = 'Tarde';
            }

            $asistencia->metodo_registro = 'IA';
        } else {
            // Si ya tiene entrada, registrar salida
            $asistencia->hora_salida = $request->input('hora_salida', Carbon::now()->toTimeString());
        }

        $asistencia->save();

        return response()->json($asistencia->load('persona'), 201);
    }

    
    // El resto de métodos (show, update, destroy) son para el panel
    
    public function show(Asistencia $asistencia)
    {
        return $asistencia->load('persona');
    }

    public function update(Request $request, Asistencia $asistencia)
    {
        // Para correcciones manuales desde el panel
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

    public function destroy(Asistencia $asistencia)
    {
        $asistencia->delete();
        return response()->json(null, 204);
    }
}