<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación (Login, Registro, etc.)
Auth::routes();

Route::get('/olvido-password', function () {
    return view('auth.olvido');
});

// Ruta de tu panel de control principal
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// rutas para configuracion del sistema
Route::get('/admin/configuracion', [App\Http\Controllers\Admin\ConfiguracionController::class, 'index'])->name('admin.configuracion.index')->middleware('auth');
Route::post('/admin/configuracion/create', [App\Http\Controllers\Admin\ConfiguracionController::class , 'store'])->name('admin.configuracion.store')->middleware('auth');

Route::get('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'edit'])->name('admin.password.edit')->middleware('auth');
Route::put('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'update'])->name('admin.password.update')->middleware('auth');
// rutas para las gestiones del sistema
Route::get('/admin/gestiones', [App\Http\Controllers\GestionController::class, 'index'])->name('admin.gestiones.index')->middleware('auth');
Route::get('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'create'])->name('admin.gestiones.create')->middleware('auth');
Route::post('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'store'])->name('admin.gestiones.store')->middleware('auth');
Route::get('/admin/gestiones/{id}/edit', [App\Http\Controllers\GestionController::class, 'edit'])->name('admin.gestiones.edit')->middleware('auth');
Route::put('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'update'])->name('admin.gestiones.update')->middleware('auth');
Route::delete('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'destroy'])->name('admin.gestiones.destroy')->middleware('auth');
// rutas para las Niveles del sistema
Route::get('/admin/niveles', [App\Http\Controllers\NivelController::class, 'index'])->name('admin.nivels.index')->middleware('auth');
Route::post('/admin/niveles/create', [App\Http\Controllers\NivelController::class, 'store'])->name('admin.nivels.store')->middleware('auth');
Route::put('/admin/niveles/{id}', [App\Http\Controllers\NivelController::class, 'update'])->name('admin.nivels.update')->middleware('auth');
Route::delete('/admin/niveles/{id}', [App\Http\Controllers\NivelController::class, 'destroy'])->name('admin.nivels.destroy')->middleware('auth');
// rutas para los turnos del sistema
Route::get('/admin/turnos', [App\Http\Controllers\TurnoController::class, 'index'])->name('admin.turnos.index')->middleware('auth');
Route::get('/admin/turnos/create', [App\Http\Controllers\TurnoController::class, 'create'])->name('admin.turnos.create')->middleware('auth');
Route::post('/admin/turnos/create', [App\Http\Controllers\TurnoController::class, 'store'])->name('admin.turnos.store')->middleware('auth');
Route::get('/admin/turnos/{id}/edit', [App\Http\Controllers\TurnoController::class, 'edit'])->name('admin.turnos.edit')->middleware('auth');
Route::put('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'update'])->name('admin.turnos.update')->middleware('auth');
Route::delete('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'destroy'])->name('admin.turnos.destroy')->middleware('auth');

// rutas para la bitacora de accesos
Route::get('/admin/bitacora', [App\Http\Controllers\Admin\BitacoraController::class, 'index'])->name('admin.bitacora.index')->middleware('auth');
