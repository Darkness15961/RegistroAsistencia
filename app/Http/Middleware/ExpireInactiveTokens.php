<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class ExpireInactiveTokens
{
    /**
     * Maneja la expiración por inactividad del token.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                // Tiempo máximo de inactividad permitido (en minutos)
                $inactivityLimit = 30; // ⏳ Cambia esto si deseas otro tiempo

                $lastUsed = $accessToken->last_used_at ?? $accessToken->created_at;

                // Si el token lleva más de X minutos sin usarse
                if ($lastUsed->lt(Carbon::now()->subMinutes($inactivityLimit))) {
                    $accessToken->delete();

                    return response()->json([
                        'message' => 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.'
                    ], 401);
                }

                // Actualizar la última vez de uso
                $accessToken->forceFill(['last_used_at' => Carbon::now()])->save();
            }
        }

        return $next($request);
    }
}
