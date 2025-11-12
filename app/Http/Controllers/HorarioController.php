<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        // Trae todos los horarios junto con el área asociada
        return response()->json(
            Horario::with('area')->get()
        );
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'id_area' => 'required|exists:areas,id_area',
            'hora_entrada' => 'required|date_format:H:i',
            'hora_salida' => 'required|date_format:H:i|after:hora_entrada',
            'turno' => 'required|string|max:10',
        ]);

        $horario = Horario::create($datosValidados);

        return response()->json([
            'message' => 'Horario creado correctamente',
            'data' => $horario->load('area')
        ], 201);
    }

    public function show(Horario $horario)
    {
        return response()->json(
            $horario->load('area')
        );
    }

    public function update(Request $request, Horario $horario)
    {
        $datosValidados = $request->validate([
            'id_area' => 'exists:areas,id_area',
            'hora_entrada' => 'date_format:H:i',
            'hora_salida' => 'date_format:H:i|after:hora_entrada',
            'turno' => 'string|max:10',
        ]);

        $horario->update($datosValidados);

        return response()->json([
            'message' => 'Horario actualizado correctamente',
            'data' => $horario->load('area')
        ], 200);
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();

        return response()->json([
            'message' => 'Horario eliminado correctamente',
            'id_eliminado' => $horario->id_horario
        ], 200);
    }
}
