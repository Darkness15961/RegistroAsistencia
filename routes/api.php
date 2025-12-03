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
Route::get('/reconocimientos/descriptores', [ReconocimientoController::class, 'index']);

// RUTA TEMPORAL DE PRUEBA
Route::get('/test-asistencia/{id_persona}', function($id_persona) {
    $persona = \App\Models\Persona::find($id_persona);
    
    if (!$persona) {
        return response()->json(['error' => 'Persona no encontrada'], 404);
    }
    
    $horario = \App\Models\Horario::where('id_area', $persona->id_area)->first();
    $horaActual = \Carbon\Carbon::now('America/Lima');
    
    $info = [
        'persona' => [
            'id' => $persona->id_persona,
            'nombre' => $persona->nombre_completo,
            'tipo' => $persona->tipo_persona,
            'id_area' => $persona->id_area
        ],
        'hora_actual' => $horaActual->format('H:i:s'),
        'horario' => $horario ? [
            'entrada' => $horario->hora_entrada,
            'salida' => $horario->hora_salida
        ] : 'SIN HORARIO'
    ];
    
    if ($horario) {
        $fechaHoy = $horaActual->toDateString();
        $horaEntradaProgramada = \Carbon\Carbon::parse($fechaHoy . ' ' . $horario->hora_entrada, 'America/Lima');
        
        if ($persona->tipo_persona === 'estudiante') {
            $ventanaInicio = $horaEntradaProgramada->copy()->subMinutes(60);
            $ventanaFin = $horaEntradaProgramada->copy()->addMinutes(15);
        } else {
            $ventanaInicio = $horaEntradaProgramada->copy()->subMinutes(30);
            $ventanaFin = $horaEntradaProgramada->copy()->addMinutes(15);
        }
        
        $info['ventana_entrada'] = [
            'inicio' => $ventanaInicio->format('H:i:s'),
            'fin' => $ventanaFin->format('H:i:s'),
            'puede_marcar' => $horaActual->between($ventanaInicio, $ventanaFin) ? 'SI' : 'NO'
        ];
    }
    
    return response()->json($info, 200, [], JSON_PRETTY_PRINT);
});


// Resumen semanal público o para dashboard
Route::get('/asistencias-semana', [AsistenciaController::class, 'asistenciasSemana']);

// Resumen semanal de salidas (solo personal)
Route::get('/asistencias-salida-semana', [AsistenciaController::class, 'asistenciasSalidaSemana']);

// Historial por persona (público)
Route::get('/asistencias/historial', [AsistenciaController::class, 'historialPersona']);

/* RUTAS PROTEGIDAS (auth:sanctum) */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/usuario-actual', [AuthController::class, 'usuarioActual']);
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
    Route::post('/personas/asignar-grupo-masivo', [PersonaController::class, 'asignarGrupoMasivo']);

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
