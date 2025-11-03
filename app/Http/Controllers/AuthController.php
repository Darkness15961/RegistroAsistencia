<?php

namespace App\Http\Controllers;

use App\Models\Usuario; // Importa tu modelo de Usuario
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Importante para verificar la contraseña
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Maneja la solicitud de inicio de sesión.
     */
    public function login(Request $request)
    {
        // 1. Validar los datos que llegan desde Login.vue
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Buscar al usuario por su email en la tabla 'usuarios'
        $usuario = Usuario::where('email', $request->email)->first();

        // 3. Verificar si el usuario existe y la contraseña es correcta
        if (! $usuario || ! Hash::check($request->password, $usuario->password_hash)) {
            // Si no, devolver un error de credenciales
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // 4. Verificar si el usuario está 'activo'
        if ($usuario->estado !== 'activo') {
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta de usuario está inactiva.'],
            ]);
        }

        // 5. Crear el token de API (Sanctum)
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // 6. Devolver la respuesta exitosa
        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $usuario->load('persona') // Devolvemos el usuario y sus datos de persona
        ], 200);
    }

    /**
     * Maneja la solicitud de cierre de sesión.
     */
    public function logout(Request $request)
    {
        // Revoca el token de API que se usó para hacer esta solicitud
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    public function cambiarPassword(Request $request)
    {
        // 1. Validar los datos
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        // 2. Obtener el usuario autenticado
        $usuario = Auth::user(); // $request->user() también funciona

        // 3. Verificar la contraseña actual
        if (! Hash::check($request->current_password, $usuario->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        // 4. Actualizar la nueva contraseña
        $usuario->password_hash = Hash::make($request->new_password);
        $usuario->save();

        // 5. Devolver respuesta exitosa
        return response()->json([
            'message' => '¡Contraseña actualizada exitosamente!'
        ], 200);
    }

}