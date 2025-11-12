<?php

namespace App\Http\Controllers;

use App\Models\Reconocimiento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReconocimientoController extends Controller
{
    /**
     * ✅ Devuelve todos los descriptores faciales registrados.
     * Usado por la IA en el frontend para cargar las caras conocidas.
     */
    public function index()
    {
        // Traemos todos los descriptores junto a la persona asociada
        $reconocimientos = Reconocimiento::with('persona:id_persona,nombres,apellidos,dni,image_url')->get();

        return response()->json([
            'message' => 'Lista de descriptores cargada correctamente.',
            'data' => $reconocimientos
        ], 200);
    }

    /**
     * ✅ Registra un nuevo descriptor facial para una persona.
     */
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'face_descriptor' => 'required|json',
            'image_url' => 'nullable|string|max:255',
        ]);

        try {
            $reconocimiento = Reconocimiento::create($datosValidados);

            return response()->json([
                'message' => 'Rostro registrado correctamente.',
                'data' => $reconocimiento->load('persona')
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al registrar reconocimiento: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al registrar el reconocimiento facial.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     *  Muestra un solo registro (opcional).
     */
    public function show(Reconocimiento $reconocimiento)
    {
        return response()->json([
            'data' => $reconocimiento->load('persona')
        ], 200);
    }

    /**
     *  Elimina un reconocimiento facial.
     * Usado cuando se elimina una persona o se reemplaza su rostro.
     */
    public function destroy(Reconocimiento $reconocimiento)
    {
        try {
            $reconocimiento->delete();

            return response()->json([
                'message' => 'Reconocimiento eliminado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al eliminar reconocimiento: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al eliminar el reconocimiento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
