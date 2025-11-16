<?php

namespace App\Http\Controllers;

use App\Models\Reconocimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReconocimientoController extends Controller
{
    /**
     * Lista todos los descriptores faciales
     */
    public function index(Request $request)
    {
        // Traer todos los reconocimientos, sin paginación
        $reconocimientos = Reconocimiento::with('persona:id_persona,nombre_completo,dni')
            ->select('id_reconocimiento', 'id_persona', 'face_descriptor', 'image_url', 'fecha_registro', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get(); // <-- aquí eliminamos la paginación

        return response()->json([
            'message' => 'Lista de descriptores cargada correctamente.',
            'data' => $reconocimientos
        ], 200);
    }



    /**
     * Registrar nuevo rostro
     */
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
            'face_descriptor' => 'required|array',
            'image_base64' => 'nullable|string', // 👈 AÑADIDO
        ]);

        try {

            // ------------------------------------------------------------------
            // 1) GUARDAR LA IMAGEN BASE64 (solo si viene del frontend)
            // ------------------------------------------------------------------
            $imageUrl = null;

            if (!empty($request->image_base64)) {
                $imageUrl = $this->guardarImagenBase64($request->image_base64);
            }

            // ------------------------------------------------------------------
            // 2) CREAR EL REGISTRO TAL COMO YA HACÍAS
            // ------------------------------------------------------------------
            $reconocimiento = Reconocimiento::create([
                'id_persona' => $datosValidados['id_persona'],
                'face_descriptor' => $datosValidados['face_descriptor'],
                'image_url' => $imageUrl,                
                'fecha_registro' => now(),
            ]);

            return response()->json([
                'message' => 'Rostro registrado correctamente.',
                'data' => $reconocimiento->load('persona:id_persona,nombre_completo,dni')
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
     * Guardar imagen Base64 en storage/app/public/rostros/
     */
    private function guardarImagenBase64($base64)
    {
        // Limpiar encabezado: "data:image/jpeg;base64,"
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Nombre único
        $filename = 'rostro_' . Str::random(20) . '.jpg';

        // Guardar en disco público
        Storage::disk('public')->put("rostros/{$filename}", $imageData);

        // URL accesible públicamente
        return asset("storage/rostros/{$filename}");
    }


    /**
     * Mostrar un reconocimiento específico
     */
    public function show(Reconocimiento $reconocimiento)
    {
        return response()->json([
            'data' => $reconocimiento->load('persona:id_persona,nombre_completo,dni')
        ], 200);
    }


    /**
     * Eliminar rostro
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

