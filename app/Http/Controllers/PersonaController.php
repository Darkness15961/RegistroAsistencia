<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $persona = $usuario ? $usuario->persona : null;
        
        $query = Persona::query();
        
        if ($usuario && $usuario->rol === 'supervisor') {
            // Solo personal
            $query->whereIn('tipo_persona', ['empleado', 'docente', 'administrativo']);
        }
        
        if ($usuario && $usuario->rol === 'docente' && $persona) {
            // Solo estudiantes de sus grupos
            $gruposIds = Grupo::where('id_tutor', $persona->id_persona)->pluck('id_grupo');
            $query->whereIn('id_grupo', $gruposIds)
                  ->where('tipo_persona', 'estudiante');
        }
        
        // Admin ve todos
        
        // Aplicar filtros adicionales del request
        if ($request->has('tipo')) {
            $tipo = strtolower($request->tipo);
            if ($tipo === 'personal') {
                $query->whereIn('tipo_persona', ['empleado', 'docente', 'administrativo']);
            } else {
                $query->where('tipo_persona', $tipo);
            }
        }
        
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        // Lógica de validación (puedes añadir más reglas)
        $datosValidados = $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'dni' => 'required|string|max:20|unique:personas',
            'correo' => 'required|email|max:100|unique:personas',
            'tipo_persona' => 'required|in:empleado,estudiante',
            'id_area' => 'required|exists:areas,id_area',
            'id_grupo' => 'nullable|exists:grupos,id_grupo',
            'cargo_grado' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);
        
        $persona = Persona::create($datosValidados);
        return response()->json($persona, 201);
    }

    public function show(Persona $persona)
    {
        // Carga las relaciones para ver más detalle
        return $persona->load(['area', 'grupo', 'asistencias']);
    }

    public function update(Request $request, Persona $persona)
    {
        // Lógica de validación
        $datosValidados = $request->validate([
            'nombre_completo' => 'string|max:150',
            'dni' => 'string|max:20|unique:personas,dni,' . $persona->id_persona . ',id_persona',
            'correo' => 'email|max:100|unique:personas,correo,' . $persona->id_persona . ',id_persona',
            'tipo_persona' => 'in:empleado,estudiante',
            'id_area' => 'exists:areas,id_area',
            'id_grupo' => 'nullable|exists:grupos,id_grupo',
            'cargo_grado' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'in:activo,inactivo',
        ]);

        $persona->update($datosValidados);
        return response()->json($persona, 200);
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();
        return response()->json([
            'message' => 'Persona eliminada correctamente',
            'id_eliminado' => $persona->id_persona
        ], 200);
    }
}