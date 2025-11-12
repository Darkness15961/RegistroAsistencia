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

// ------------------------------------------------------
// 🔓 RUTAS PÚBLICAS (sin autenticación)
// ------------------------------------------------------

// 🔐 Autenticación básica
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// 🧾 Registro de asistencia desde IA (FaceAPI)
Route::post('/asistencias/registrar', [AsistenciaController::class, 'store']);

// 🤖 Descriptores faciales para la IA
// (El frontend los usa para reconocer rostros)
Route::get('/reconocimientos/descriptores', [ReconocimientoController::class, 'index']);

// ✅ NUEVO: Resumen semanal de asistencias (público o para dashboard)
Route::get('/asistencias-semana', [AsistenciaController::class, 'asistenciasSemana']);


// ------------------------------------------------------
// 🔒 RUTAS PRIVADAS (requieren autenticación con Sanctum)
// ------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // 👤 Perfil del usuario autenticado
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/perfil', [AuthController::class, 'update']);
    Route::delete('/perfil', [AuthController::class, 'destroy']);
    Route::post('/perfil/cambiar-password', [AuthController::class, 'cambiarPassword']);

    // ⚙️ Configuraciones y Dashboard
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);
    Route::get('/configuraciones', [ConfiguracionController::class, 'index']);
    Route::post('/configuraciones', [ConfiguracionController::class, 'store']);

    // 📂 CRUDs del sistema
    Route::apiResource('/areas', AreaController::class);
    Route::apiResource('/horarios', HorarioController::class);
    Route::apiResource('/grupos', GrupoController::class);
    Route::apiResource('/personas', PersonaController::class);
    
    // 📋 Asistencias (store es público, el resto requiere autenticación)
    Route::apiResource('/asistencias', AsistenciaController::class)->except(['store']);

    // 🧠 Reconocimientos faciales
    Route::apiResource('/reconocimientos', ReconocimientoController::class)
        ->only(['store', 'destroy', 'show']);

    // 👥 Gestión de usuarios del sistema
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
});