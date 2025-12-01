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
        $area->delete();
        return response()->json(null, 204);
    }
}