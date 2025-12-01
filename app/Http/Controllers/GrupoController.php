<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $persona = $usuario ? $usuario->persona : null;
        
        $query = Grupo::with(['area', 'tutor']);
        
        if ($usuario && $usuario->rol === 'supervisor') {
            // Supervisores no ven grupos de estudiantes
            return response()->json([]);
        }
        
        if ($usuario && $usuario->rol === 'docente' && $persona) {
            // Solo grupos donde es tutor
            $query->where('id_tutor', $persona->id_persona);
        }
        
        // Admin ve todos
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'id_area' => 'required|exists:areas,id_area',
            'id_tutor' => 'nullable|exists:personas,id_persona',
            'nivel' => 'required|string|max:50', // Aumentado a 50 por si el nombre es largo
            
            // ✅ CORRECCIÓN: Ahora son 'nullable' para permitir grupos sin grado/sección
            'grado' => 'nullable|string|max:20',
            'seccion' => 'nullable|string|max:5',
        ]);

        $grupo = Grupo::create($datosValidados);
        return response()->json($grupo->load('area', 'tutor'), 201);
    }

    public function show(Grupo $grupo)
    {
        // Mostramos el grupo y todos sus estudiantes
        return $grupo->load(['area', 'tutor', 'estudiantes']);
    }

    public function update(Request $request, Grupo $grupo)
    {
        $datosValidados = $request->validate([
            'id_area' => 'exists:areas,id_area',
            'id_tutor' => 'nullable|exists:personas,id_persona',
            'nivel' => 'string|max:50',
            
            // ✅ CORRECCIÓN: También 'nullable' en la actualización
            'grado' => 'nullable|string|max:50',
            'seccion' => 'nullable|string|max:20',
        ]);

        $grupo->update($datosValidados);
        return response()->json($grupo->load('area', 'tutor'), 200);
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->delete();
        
        return response()->json([
            'message' => 'Grupo eliminado correctamente',
            'id_eliminado' => $grupo->id_grupo
        ], 200);
    }
}