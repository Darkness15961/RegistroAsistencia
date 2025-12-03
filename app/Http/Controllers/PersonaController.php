<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Grupo;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->query('tipo');
        
        if ($tipo === 'personal') {
            return Persona::with(['area', 'grupo.area'])
                ->whereIn('tipo_persona', ['empleado', 'docente', 'administrativo'])
                ->get();
        }
        
        if ($tipo === 'alumnos') {
            return Persona::with(['area', 'grupo.area'])
                ->where('tipo_persona', 'estudiante')
                ->get();
        }
        
        return Persona::with(['area', 'grupo.area'])->get();
    }

    public function store(Request $request)
    {
        // Lógica de validación (puedes añadir más reglas)
        $datosValidados = $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'dni' => 'required|string|max:20|unique:personas',
            'correo' => 'required|email|max:100|unique:personas',
            'tipo_persona' => 'required|in:empleado,estudiante,docente,administrativo',
            'id_area' => 'required|exists:areas,id_area',
            'id_grupo' => 'nullable|exists:grupos,id_grupo',
            'cargo_grado' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);
        
        $persona = Persona::create($datosValidados);
        return response()->json($persona, 201);
    }

    public function show($id)
    {
        $persona = Persona::with(['area', 'grupo'])->findOrFail($id);
        return response()->json($persona);
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);
        
        // Lógica de validación
        $datosValidados = $request->validate([
            'nombre_completo' => 'string|max:150',
            'dni' => 'string|max:20|unique:personas,dni,' . $persona->id_persona . ',id_persona',
            'correo' => 'email|max:100|unique:personas,correo,' . $persona->id_persona . ',id_persona',
            'tipo_persona' => 'in:empleado,estudiante,docente,administrativo',
            'id_area' => 'exists:areas,id_area',
            'id_grupo' => 'nullable|exists:grupos,id_grupo',
            'cargo_grado' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'in:activo,inactivo',
        ]);
        
        $persona->update($datosValidados);
        return response()->json($persona, 200);
    }

    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return response()->json(null, 204);
    }

    /**
     * Asignar múltiples personas a un grupo de forma masiva
     */
    public function asignarGrupoMasivo(Request $request)
    {
        $datosValidados = $request->validate([
            'ids_personas' => 'required|array|min:1',
            'ids_personas.*' => 'exists:personas,id_persona',
            'id_grupo' => 'required|exists:grupos,id_grupo',
        ]);

        try {
            // Obtener el grupo con su área
            $grupo = Grupo::with('area')->findOrFail($datosValidados['id_grupo']);

            // Actualizar todas las personas seleccionadas
            Persona::whereIn('id_persona', $datosValidados['ids_personas'])
                ->update([
                    'id_grupo' => $grupo->id_grupo,
                    'id_area' => $grupo->id_area, // Asignar automáticamente el área del grupo
                ]);

            return response()->json([
                'message' => 'Personas asignadas correctamente al grupo.',
                'cantidad' => count($datosValidados['ids_personas']),
                'grupo' => $grupo->grado,
                'area' => $grupo->area->nombre_area ?? 'Sin área'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al asignar personas al grupo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}