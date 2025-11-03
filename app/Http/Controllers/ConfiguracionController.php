<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Devuelve todas las configuraciones como un objeto clave-valor.
     */
    public function index()
    {
        // Esto transforma la tabla [ {clave: 'a', valor: 'b'} ]
        // en un objeto JSON { a: 'b' }
        $configuraciones = Configuracion::pluck('valor', 'clave');
        
        // Nos aseguramos de que existan valores por defecto
        return response()->json([
            'tolerancia_tardanza' => $configuraciones['tolerancia_tardanza'] ?? 10,
            'nombre_institucion' => $configuraciones['nombre_institucion'] ?? 'Colegio 4scan',
        ]);
    }

    /**
     * Guarda todas las configuraciones enviadas.
     */
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'tolerancia_tardanza' => 'required|integer|min:0',
            'nombre_institucion' => 'required|string|max:255',
        ]);

        foreach ($datosValidados as $clave => $valor) {
            Configuracion::updateOrCreate(
                ['clave' => $clave], // Busca por esta clave
                ['valor' => $valor]  // Actualiza o crea con este valor
            );
        }

        return response()->json(['message' => 'Configuración guardada exitosamente.']);
    }
}