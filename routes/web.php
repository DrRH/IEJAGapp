<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\EstudiantesController;
use App\Http\Controllers\CasosController;
use App\Http\Controllers\ReportesConvivenciaController;
use App\Http\Controllers\ComitesConvivenciaController;
use App\Http\Controllers\AIAssistantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/login', [GoogleController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// Área autenticada
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GoogleController::class, 'dashboard'])->name('dashboard');

    // Perfil de usuario
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Configuración institucional
    Route::get('/administracion/configuracion', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/administracion/configuracion', [SettingsController::class, 'update'])->name('settings.update');

    // Gestión de usuarios
    Route::resource('administracion/usuarios', UsersController::class)->parameters([
        'usuarios' => 'user'
    ])->names([
        'index' => 'administracion.usuarios.index',
        'create' => 'administracion.usuarios.create',
        'store' => 'administracion.usuarios.store',
        'show' => 'administracion.usuarios.show',
        'edit' => 'administracion.usuarios.edit',
        'update' => 'administracion.usuarios.update',
        'destroy' => 'administracion.usuarios.destroy',
    ]);

    // Logs y Auditoría
    Route::get('/administracion/logs', [LogsController::class, 'index'])->name('administracion.logs.index');
    Route::get('/administracion/logs/{log}', [LogsController::class, 'show'])->name('administracion.logs.show');
    Route::post('/administracion/logs/cleanup', [LogsController::class, 'cleanup'])->name('administracion.logs.cleanup');

    // Módulo Académico - Estudiantes
    Route::get('/academico/estudiantes/export', [EstudiantesController::class, 'export'])->name('academico.estudiantes.export');
    Route::resource('academico/estudiantes', EstudiantesController::class)->parameters([
        'estudiantes' => 'estudiante'
    ])->names([
        'index' => 'academico.estudiantes.index',
        'create' => 'academico.estudiantes.create',
        'store' => 'academico.estudiantes.store',
        'show' => 'academico.estudiantes.show',
        'edit' => 'academico.estudiantes.edit',
        'update' => 'academico.estudiantes.update',
        'destroy' => 'academico.estudiantes.destroy',
    ]);

    // Módulo Convivencia - Atención a Situaciones de Convivencia
    Route::get('/convivencia/casos/export', [CasosController::class, 'export'])->name('convivencia.casos.export');
    Route::get('/convivencia/casos/{caso}/acta', [CasosController::class, 'acta'])->name('convivencia.casos.acta');
    Route::get('/convivencia/casos/{caso}/print', [CasosController::class, 'print'])->name('convivencia.casos.print');
    Route::get('/convivencia/casos/{caso}/pdf', [CasosController::class, 'downloadPdf'])->name('convivencia.casos.pdf');
    Route::resource('convivencia/casos', CasosController::class)->parameters([
        'casos' => 'caso'
    ])->names([
        'index' => 'convivencia.casos.index',
        'create' => 'convivencia.casos.create',
        'store' => 'convivencia.casos.store',
        'show' => 'convivencia.casos.show',
        'edit' => 'convivencia.casos.edit',
        'update' => 'convivencia.casos.update',
        'destroy' => 'convivencia.casos.destroy',
    ]);

    // Módulo Reportes - Reportes de Convivencia
    Route::get('/reportes/convivencia', [ReportesConvivenciaController::class, 'index'])->name('reportes.convivencia.index');
    Route::get('/reportes/convivencia/export-csv', [ReportesConvivenciaController::class, 'exportCSV'])->name('reportes.convivencia.export-csv');
    Route::get('/reportes/convivencia/export-pdf', [ReportesConvivenciaController::class, 'exportPDF'])->name('reportes.convivencia.export-pdf');

    // Módulo Actas - Comité de Convivencia
    Route::post('/actas/comite-convivencia/{acta}/aprobar', [ComitesConvivenciaController::class, 'aprobar'])->name('actas.comite-convivencia.aprobar');
    Route::post('/actas/comite-convivencia/{acta}/publicar', [ComitesConvivenciaController::class, 'publicar'])->name('actas.comite-convivencia.publicar');
    Route::get('/actas/comite-convivencia/{acta}/export-pdf', [ComitesConvivenciaController::class, 'exportPDF'])->name('actas.comite-convivencia.export-pdf');
    Route::resource('actas/comite-convivencia', ComitesConvivenciaController::class)->parameters([
        'comite-convivencia' => 'acta'
    ])->names([
        'index' => 'actas.comite-convivencia.index',
        'create' => 'actas.comite-convivencia.create',
        'store' => 'actas.comite-convivencia.store',
        'show' => 'actas.comite-convivencia.show',
        'edit' => 'actas.comite-convivencia.edit',
        'update' => 'actas.comite-convivencia.update',
        'destroy' => 'actas.comite-convivencia.destroy',
    ]);

    // AI Assistant - Asistente de IA
    Route::prefix('api/ai-assistant')->name('ai.')->group(function () {
        Route::post('/generar-sugerencia', [AIAssistantController::class, 'generarSugerencia'])->name('generar-sugerencia');
        Route::post('/mejorar-texto', [AIAssistantController::class, 'mejorarTexto'])->name('mejorar-texto');
        Route::post('/sugerencia-streaming', [AIAssistantController::class, 'sugerenciaStreaming'])->name('sugerencia-streaming');
        Route::get('/verificar-estado', [AIAssistantController::class, 'verificarEstado'])->name('verificar-estado');
    });
});
