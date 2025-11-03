<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        // Carga el usuario y los datos de la persona asociada
        return Usuario::with('persona')->get();
    }

    // Este método crea AMBAS, la Persona y el Usuario, en una transacción
    // Justo como lo pedía tu modal 'FormularioUsuarioModal.vue'
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            // Datos para la Persona
            'nombre_completo' => 'required|string|max:150',
            'correo' => 'required|email|max:100|unique:personas',
            'dni' => 'required|string|max:20|unique:personas',
            'telefono' => 'nullable|string|max:20',
            'cargo_grado' => 'required|string|max:100',
            'id_area' => 'required|exists:areas,id_area',
            'tipo_persona' => 'required|in:empleado,estudiante', // Ej: 'empleado'

            // Datos para el Usuario
            'email_login' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|string|max:20',
        ]);

        // Usamos una transacción para asegurar que ambos se creen
        try {
            DB::beginTransaction();

            // 1. Crear la Persona
            $persona = Persona::create([
                'nombre_completo' => $request->nombre_completo,
                'correo' => $request->correo,
                'dni' => $request->dni,
                'telefono' => $request->telefono,
                'cargo_grado' => $request->cargo_grado,
                'id_area' => $request->id_area,
                'tipo_persona' => $request->tipo_persona,
                'estado' => 'activo',
            ]);

            // 2. Crear el Usuario y vincularlo
            $usuario = Usuario::create([
                'id_persona' => $persona->id_persona,
                'email' => $request->email_login,
                'password_hash' => Hash::make($request->password),
                'rol' => $request->rol,
                'estado' => 'activo',
            ]);

            DB::commit();

            return response()->json($usuario->load('persona'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear el usuario', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Usuario $usuario)
    {
        return $usuario->load('persona');
    }

    public function update(Request $request, Usuario $usuario)
    {
        $datosValidados = $request->validate([
            'email' => ['email', 'max:100', Rule::unique('usuarios')->ignore($usuario->id_usuario, 'id_usuario')],
            'password' => 'nullable|string|min:8',
            'rol' => 'string|max:20',
            'estado' => 'string|in:activo,inactivo',
        ]);

        // Actualizar contraseña solo si se envió una nueva
        if ($request->filled('password')) {
            $datosValidados['password_hash'] = Hash::make($request->password);
        }
        // Quitar 'password' para que no intente actualizar esa columna
        unset($datosValidados['password']);

        $usuario->update($datosValidados);
        
        return response()->json($usuario->load('persona'), 200);
    }

    public function destroy(Usuario $usuario)
    {
        // Nota: Esto elimina el usuario, pero no la persona.
        // Si quieres eliminar ambos, deberías eliminar la Persona.
        $usuario->delete();
        return response()->json(null, 204);
    }
}