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
    /**
     * Listar usuarios con paginación
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $usuarios = Usuario::with('persona')->paginate($perPage);

        return response()->json($usuarios);
    }

    /**
     * Crear un nuevo usuario con su persona
     */
    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            // Datos de Persona
            'nombre_completo' => 'required|string|max:150',
            'correo' => 'required|email|max:100|unique:personas,correo',
            'dni' => 'required|string|max:20|unique:personas,dni',
            'telefono' => 'nullable|string|max:20',
            'cargo_grado' => 'required|string|max:100',
            'id_area' => 'required|exists:areas,id_area',
            'tipo_persona' => 'required|in:empleado,estudiante',

            // Datos de Usuario
            'email_login' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|string|in:administrador,empleado,estudiante',
        ]);

        try {
            DB::beginTransaction();

            // Crear Persona
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

            // Crear Usuario
            $usuario = Usuario::create([
                'id_persona' => $persona->id_persona,
                'email' => $request->email_login,
                'password_hash' => Hash::make($request->password),
                'rol' => $request->rol,
                'estado' => 'activo',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'usuario' => $usuario->load('persona')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'No se pudo crear el usuario',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un usuario específico con su persona
     */
    public function show($id)
    {
        $usuario = Usuario::with('persona')->find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    /**
     * Actualizar usuario y su persona
     */
    public function update(Request $request, Usuario $usuario)
    {
        $datosValidados = $request->validate([
            'email' => ['email', 'max:100', Rule::unique('usuarios')->ignore($usuario->id_usuario, 'id_usuario')],
            'password' => 'nullable|string|min:8',
            'rol' => 'string|in:administrador,empleado,estudiante',
            'estado' => 'string|in:activo,inactivo',

            // Datos de persona opcionales
            'nombre_completo' => 'sometimes|string|max:150',
            'dni' => ['sometimes','string','max:20', Rule::unique('personas')->ignore($usuario->persona->id_persona, 'id_persona')],
            'telefono' => 'nullable|string|max:20',
            'cargo_grado' => 'sometimes|string|max:100',
            'correo' => ['sometimes','email','max:100', Rule::unique('personas')->ignore($usuario->persona->id_persona, 'id_persona')],
        ]);

        if ($request->filled('password')) {
            $datosValidados['password_hash'] = Hash::make($request->password);
        }
        unset($datosValidados['password']);

        DB::beginTransaction();
        try {
            // Actualiza usuario
            $usuario->update($datosValidados);

            // Actualiza persona si se enviaron datos
            if ($request->hasAny(['nombre_completo', 'dni', 'telefono', 'cargo_grado', 'correo'])) {
                $usuario->persona->update($request->only(['nombre_completo', 'dni', 'telefono', 'cargo_grado', 'correo']));
            }

            DB::commit();

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'usuario' => $usuario->load('persona')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'No se pudo actualizar el usuario',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar usuario (solo administradores)
     */
    public function destroy(Usuario $usuario)
    {
        $usuarioAutenticado = auth()->user();

        if (!$usuarioAutenticado || $usuarioAutenticado->rol !== 'administrador') {
            return response()->json(['error' => 'No autorizado para eliminar usuarios'], 403);
        }

        if ($usuarioAutenticado->id_usuario === $usuario->id_usuario) {
            return response()->json(['error' => 'No puedes eliminar tu propia cuenta'], 400);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}
