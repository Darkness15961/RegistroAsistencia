<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * REGISTRO DE NUEVO USUARIO (PÚBLICO)
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'dni' => 'required|string|max:20|unique:personas,dni',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'cargo_grado' => 'nullable|string|max:100',
            'tipo_persona' => 'required|string|in:empleado,estudiante',
            'id_area' => 'nullable|exists:areas,id_area',
            'id_grupo' => 'nullable|exists:grupos,id_grupo',
            'email' => 'required|email|unique:usuarios,email',
            'rol' => 'required|string|in:administrador,empleado,docente,psicologia,secretaria',
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        // 1️⃣ Crear la persona
        $persona = Persona::create([
            'nombre_completo' => $request->nombre_completo,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'cargo_grado' => $request->cargo_grado,
            'tipo_persona' => $request->tipo_persona,
            'id_area' => $request->id_area,
            'id_grupo' => $request->id_grupo,
            'estado' => 'activo',
        ]);

        // 2️⃣ Crear el usuario vinculado
        $usuario = Usuario::create([
            'id_persona' => $persona->id_persona,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'rol' => $request->rol,
            'estado' => 'activo',
        ]);

        // 3️⃣ Crear token para login automático
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $usuario->load('persona'),
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if ($usuario->estado !== 'activo') {
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta de usuario está inactiva.'],
            ]);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $usuario->load('persona'),
        ], 200);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
        ], 200);
    }

    /**
     * ACTUALIZAR PERFIL
     */
    public function update(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre_completo' => 'sometimes|string|max:150',
            'email' => 'sometimes|email|unique:usuarios,email,' . $usuario->id_usuario . ',id_usuario',
        ]);

        if ($request->has('email')) {
            $usuario->email = $request->email;
            $usuario->save();
        }

        if ($request->has('nombre_completo') && $usuario->persona) {
            $usuario->persona->nombre_completo = $request->nombre_completo;
            $usuario->persona->save();
        }

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user' => $usuario->load('persona'),
        ], 200);
    }

    /**
     * CAMBIO DE CONTRASEÑA
     */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $usuario = Auth::user();

        if (! Hash::check($request->current_password, $usuario->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $usuario->password_hash = Hash::make($request->new_password);
        $usuario->save();

        return response()->json([
            'message' => '¡Contraseña actualizada exitosamente!',
        ], 200);
    }

    /**
     * ELIMINAR CUENTA
     */
    public function destroy(Request $request)
    {
        $usuario = Auth::user();

        $usuario->tokens()->delete();
        $usuario->delete();

        return response()->json([
            'message' => 'Cuenta eliminada exitosamente',
        ], 200);
    }
}
