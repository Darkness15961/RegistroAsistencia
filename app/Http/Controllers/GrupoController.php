<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        // Traemos los grupos con su área y la persona que es tutora
        return Grupo::with(['area', 'tutor'])->get();
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'id_area' => 'required|exists:areas,id_area',
            'id_tutor' => 'nullable|exists:personas,id_persona',
            'nivel' => 'required|string|max:20',
            'grado' => 'required|string|max:20',
            'seccion' => 'required|string|max:5',
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
            'nivel' => 'string|max:20',
            'grado' => 'string|max:20',
            'seccion' => 'string|max:5',
        ]);

        $grupo->update($datosValidados);
        return response()->json($grupo->load('area', 'tutor'), 200);
    }

    public function destroy(Grupo $grupo)
    {
        return response()->json([
            'message' => 'Grupo eliminado correctamente',
            'id_eliminado' => $grupo->id_grupo
        ], 200);
    }
}