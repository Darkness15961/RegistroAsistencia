<?php

use Illuminate\Support\Facades\Route;

// --- 1. Importar todos los Controladores ---
use App\Http\Controllers\AreaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ReconocimientoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracionController;

// ¡¡IMPORTANTE!! Importa el nuevo controlador
use App\Http\Controllers\AuthController; 

/*
|--------------------------------------------------------------------------
| Rutas Web (Frontend de Vue)
|--------------------------------------------------------------------------
*/
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');


/*
|--------------------------------------------------------------------------
| Rutas de la API (Backend de Laravel)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    // --- RUTAS PÚBLICAS (Para Login y registro de asistencia de los empleados e alumnos) ---

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/asistencias/registrar', [AsistenciaController::class, 'store']);
    Route::get('/reconocimientos/descriptores', [ReconocimientoController::class, 'index']);


    // --- RUTAS PRIVADAS (¡PROTEGIDAS!) ---
    Route::middleware('auth:sanctum')->group(function () {

        // Ruta de Logout 
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/perfil/cambiar-password', [AuthController::class, 'cambiarPassword']);
        // Rutas del panel (CRUDs)
        Route::get('/configuraciones', [ConfiguracionController::class, 'index']);
        Route::post('/configuraciones', [ConfiguracionController::class, 'store']);
        Route::apiResource('/areas', AreaController::class);
        Route::apiResource('/horarios', HorarioController::class);
        Route::apiResource('/grupos', GrupoController::class);
        Route::apiResource('/personas', PersonaController::class);
        Route::apiResource('/usuarios', UsuarioController::class);
        Route::apiResource('/asistencias', AsistenciaController::class)->except(['store']);
        Route::apiResource('/reconocimientos', ReconocimientoController::class)->only([
            'store', 'destroy'
        ]);

    });
});