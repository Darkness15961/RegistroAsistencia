<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        return Area::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_area' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo_area' => 'required|in:personal,alumnado',
        ]);
        $area = Area::create($request->all());
        return response()->json($area, 201);
    }

    public function show(Area $area)
    {
        return $area;
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre_area' => 'string|max:100',
            'descripcion' => 'nullable|string',
            'tipo_area' => 'in:personal,alumnado',
        ]);
        $area->update($request->all());
        return response()->json($area, 200);
    }

    public function destroy(Area $area)
    {
        try {
            // 1. Poner en NULL el id_area e id_grupo de todas las personas asociadas
            $area->personas()->update([
                'id_area' => null,
                'id_grupo' => null
            ]);

            // 2. Eliminar todos los horarios asociados al área
            $area->horarios()->delete();

            // 3. Eliminar todos los grupos asociados al área
            $area->grupos()->delete();

            // 4. Finalmente, eliminar el área
            $area->delete();

            return response()->json([
                'message' => 'Área eliminada correctamente. Las personas han sido desasignadas.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el área.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}