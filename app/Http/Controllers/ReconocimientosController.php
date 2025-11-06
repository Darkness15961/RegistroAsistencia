<?php

namespace App\Http\Controllers;

use App\Models\Reconocimientos;
use Illuminate\Http\Request;

class ReconocimientosController extends Controller
{
    public function index()
    {
        // Útil para que la IA cargue todos los descriptores al iniciar
        return Reconocimientos::all();
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'face_descriptor' => 'required|json',
            'image_url' => 'nullable|string:255',
        ]);

        $reconocimiento = Reconocimientos::create($datosValidados);
        return response()->json($reconocimiento, 201);
    }

    public function show(Reconocimientos $reconocimiento)
    {
        return $reconocimiento->load('persona');
    }

    public function destroy(Reconocimientos $reconocimiento)
    {
        $reconocimiento->delete();
        return response()->json(null, 204);
    }

    // (El método Update no suele ser necesario aquí)
}
