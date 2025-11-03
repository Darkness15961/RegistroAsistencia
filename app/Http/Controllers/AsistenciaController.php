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
        $query = Asistencia::with('persona'); // Cargar la persona
        
        // Filtro por fecha (ej: /api/asistencias?fecha=2025-10-31)
        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        // Ordenar por más reciente y paginar
        return $query->latest()->paginate(50);
    }

    // ESTA ES LA LÓGICA 
    public function store(Request $request)
    {
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
        ]);

        $persona = Persona::find($request->id_persona);
        $hoy = Carbon::now();
        $horaActual = $hoy->toTimeString();

        // 1. Buscar el horario de la persona a través de su área
        $horario = Horario::where('id_area', $persona->id_area)->first();
        if (!$horario) {
            return response()->json(['error' => 'La persona no tiene un horario asignado'], 404);
        }

        // 2. Buscar si ya existe un registro de asistencia para esta persona HOY
        $asistencia = Asistencia::firstOrNew([
            'id_persona' => $persona->id_persona,
            'fecha' => $hoy->toDateString(),
        ]);

        // 3. Lógica de registro (Entrada/Salida)
        if (is_null($asistencia->hora_entrada)) {
            // -- ES MARCACIÓN DE ENTRADA --
            $asistencia->hora_entrada = $horaActual;
            
            // Calcular estado (Puntual o Tarde)
            $horaLimiteEntrada = Carbon::parse($horario->hora_entrada);
            if (Carbon::parse($horaActual)->lte($horaLimiteEntrada)) {
                $asistencia->estado_asistencia = 'Presente';
            } else {
                $asistencia->estado_asistencia = 'Tarde';
            }
            $asistencia->metodo_registro = 'IA';
        
        } else {
            // -- ES MARCACIÓN DE SALIDA --
            $asistencia->hora_salida = $horaActual;
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