<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        // Permite filtrar por tipo, ej: /api/personas?tipo=estudiante
        if ($request->has('tipo')) {
            return Persona::where('tipo_persona', $request->tipo)->get();
        }
        return Persona::all();
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
        return response()->json(null, 204);
    }
}