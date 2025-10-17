<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/login', [GoogleController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// �rea autenticada
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GoogleController::class, 'dashboard'])->name('dashboard');

    // Perfil de usuario
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Configuraci�n institucional
    Route::get('/administracion/configuracion', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/administracion/configuracion', [SettingsController::class, 'update'])->name('settings.update');

    // Gestión de usuarios
    Route::resource('administracion/usuarios', UsersController::class)->names([
        'index' => 'administracion.usuarios.index',
        'create' => 'administracion.usuarios.create',
        'store' => 'administracion.usuarios.store',
        'show' => 'administracion.usuarios.show',
        'edit' => 'administracion.usuarios.edit',
        'update' => 'administracion.usuarios.update',
        'destroy' => 'administracion.usuarios.destroy',
    ]);
});
