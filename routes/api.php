<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Importar todos los Controladores ---
use App\Http\Controllers\AreaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ReconocimientoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| Rutas de la API (Backend de Laravel)
|--------------------------------------------------------------------------
*/

// --- 🔓 RUTAS PÚBLICAS (No requieren autenticación) ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/asistencias/registrar', [AsistenciaController::class, 'store']);
Route::get('/reconocimientos/descriptores', [ReconocimientoController::class, 'index']);


// --- 🔒 RUTAS PRIVADAS (Protegidas con auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    /** 👤 Perfil del usuario autenticado */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/perfil', [AuthController::class, 'update']);
    Route::delete('/perfil', [AuthController::class, 'destroy']);
    Route::post('/perfil/cambiar-password', [AuthController::class, 'cambiarPassword']);

    /** ⚙️ Configuraciones y Dashboard */
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);
    Route::get('/configuraciones', [ConfiguracionController::class, 'index']);
    Route::post('/configuraciones', [ConfiguracionController::class, 'store']);

    /** 📂 CRUDs del panel */
    Route::apiResource('/areas', AreaController::class);
    Route::apiResource('/horarios', HorarioController::class);
    Route::apiResource('/grupos', GrupoController::class);
    Route::apiResource('/personas', PersonaController::class);
    Route::apiResource('/asistencias', AsistenciaController::class)->except(['store']);
    Route::apiResource('/reconocimientos', ReconocimientoController::class)->only(['store', 'destroy']);

    /** 👥 Gestión de usuarios */
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);
});
