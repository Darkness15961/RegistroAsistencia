<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * 📘 Devuelve las configuraciones actuales del sistema
     * en formato clave-valor (ej: { "tolerancia_tardanza": 10 }).
     */
    public function index()
    {
        // Obtener todas las configuraciones en formato clave => valor
        $configuraciones = Configuracion::pluck('valor', 'clave');

        // Valores por defecto si aún no existen en la base de datos
        $defaults = [
            'tolerancia_tardanza' => 10,
            'nombre_institucion' => 'Colegio 4Scan',
            'modo_asistencia' => 'reconocimiento_facial', // valor por defecto agregado
        ];

        // Fusionar valores de la BD + los defaults
        $merged = array_merge($defaults, $configuraciones->toArray());

        return response()->json($merged);
    }

    /**
     * 💾 Guarda o actualiza las configuraciones enviadas desde el panel.
     */
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'tolerancia_tardanza' => 'required|integer|min:0',
            'nombre_institucion' => 'required|string|max:255',
            'modo_asistencia' => 'nullable|string|in:manual,reconocimiento_facial', // opcional
        ]);

        foreach ($datosValidados as $clave => $valor) {
            Configuracion::updateOrCreate(
                ['clave' => $clave], // Busca por clave existente
                ['valor' => $valor]  // Actualiza o crea
            );
        }

        return response()->json([
            'message' => 'Configuraciones actualizadas correctamente.',
        ]);
    }
}
