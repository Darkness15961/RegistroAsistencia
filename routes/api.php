<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Importar todos los Controladores ---
// (Solo los que se usan en esta API)
use App\Http\Controllers\AreaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ReconocimientoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\AuthController; 

/*
|--------------------------------------------------------------------------
| Rutas de la API (Backend de Laravel)
|--------------------------------------------------------------------------
|
| Estas rutas se cargan automáticamente con el prefijo '/api'.
|
*/

// --- RUTAS PÚBLICAS (Para Login y registro) ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/asistencias/registrar', [AsistenciaController::class, 'store']);
Route::get('/reconocimientos/descriptores', [ReconocimientoController::class, 'index']);


// --- RUTAS PRIVADAS (¡PROTEGIDAS!) ---
Route::middleware('auth:sanctum')->group(function () {

    // Ruta de Logout y Perfil
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