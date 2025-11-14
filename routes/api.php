<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Importar Controladores ---
use App\Http\Controllers\{
    AreaController,
    HorarioController,
    GrupoController,
    PersonaController,
    AsistenciaController,
    ReconocimientoController,
    UsuarioController,
    ConfiguracionController,
    AuthController
};
use App\Http\Controllers\Api\DashboardController;

// RUTAS PÚBLICAS
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Registro de asistencia desde IA (FaceAPI) - pública
Route::post('/asistencias/registrar', [AsistenciaController::class, 'store']);

// Descriptores faciales para la IA
Route::get('/reconocimientos/descriptores', [ReconocimientoController::class, 'index']);

// Resumen semanal público o para dashboard
Route::get('/asistencias-semana', [AsistenciaController::class, 'asistenciasSemana']);

// Historial por persona (público)
Route::get('/asistencias/historial', [AsistenciaController::class, 'historialPersona']);

/* RUTAS PROTEGIDAS (auth:sanctum) */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/perfil', [AuthController::class, 'update']);
    Route::delete('/perfil', [AuthController::class, 'destroy']);
    Route::post('/perfil/cambiar-password', [AuthController::class, 'cambiarPassword']);

    Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);
    Route::get('/configuraciones', [ConfiguracionController::class, 'index']);
    Route::post('/configuraciones', [ConfiguracionController::class, 'store']);

    Route::apiResource('/areas', AreaController::class);
    Route::apiResource('/horarios', HorarioController::class);
    Route::apiResource('/grupos', GrupoController::class);
    Route::apiResource('/personas', PersonaController::class);

    // Las rutas REST de asistencias (index, show, update, destroy, etc.) protegidas
    Route::apiResource('/asistencias', AsistenciaController::class)->except(['store']);
    
    Route::apiResource('/reconocimientos', ReconocimientoController::class)
        ->only(['store', 'destroy', 'show']);

    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
});
