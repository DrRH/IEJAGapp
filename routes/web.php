<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\EstudiantesController;

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
});
