<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\ApoderadoController;
use App\Http\Controllers\Admin\FichaMedicaController;
use App\Http\Controllers\Admin\FuncionalidadController;
use App\Http\Controllers\Admin\HorarioController as AdminHorarioController;
use App\Http\Controllers\Admin\InfraestructuraController;
use App\Http\Controllers\Admin\MatriculaController;
use App\Http\Controllers\Admin\MensualidadController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\NotaController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PersonalAdministrativoController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\PermisoRolController;
use App\Http\Controllers\Admin\UsuarioController;

// CU06: Iniciar sesion - registra las rutas base de login de Laravel.
Auth::routes(['register' => false]);

Route::get('admin/password/reset', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showForgotForm'])->name('admin.password.request');
Route::post('admin/password/email', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'sendResetCode'])->name('admin.password.email');
Route::get('admin/password/reset-form', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset.form');
Route::post('admin/password/update', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'resetPassword'])->name('admin.password.reset.submit');

// CU07: Cerrar sesion - envia la peticion al LoginController@logout.
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/olvido-password', function () {
    return view('auth.olvido');
});

Route::redirect('/dashboard', '/')->middleware('auth');

Route::redirect('/home', '/')->middleware('auth');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home-panel');
    }
    return view('landing');
});

Route::get('/panel', [App\Http\Controllers\HomeController::class, 'index'])->name('home-panel')->middleware(['auth', 'can:home-panel']);

Route::get('/admin/configuracion', [App\Http\Controllers\Admin\ConfiguracionController::class, 'index'])->name('admin.configuracion.index')->middleware('auth');
Route::post('/admin/configuracion/create', [App\Http\Controllers\Admin\ConfiguracionController::class , 'store'])->name('admin.configuracion.store')->middleware('auth');

// CU03: Gestionar Estudiante - listado de estudiantes.
Route::get('/admin/alumnos', [App\Http\Controllers\Admin\AlumnoController::class, 'index'])->name('admin.alumnos.index')->middleware(['auth', 'can:admin.alumnos.index']);
// CU03: Gestionar Estudiante - formulario de creacion.
Route::get('/admin/alumnos/create', [App\Http\Controllers\Admin\AlumnoController::class, 'create'])->name('admin.alumnos.create')->middleware(['auth', 'can:admin.alumnos.index']);
// CU03 y CU01: Gestionar Estudiante/Usuario - crea alumno y usuario de acceso.
Route::post('/admin/alumnos/create', [App\Http\Controllers\Admin\AlumnoController::class, 'store'])->name('admin.alumnos.store')->middleware(['auth', 'can:admin.alumnos.index']);
// CU03: Gestionar Estudiante - formulario de edicion.
Route::get('/admin/alumnos/{id}/edit', [App\Http\Controllers\Admin\AlumnoController::class, 'edit'])->name('admin.alumnos.edit')->middleware(['auth', 'can:admin.alumnos.index']);
// CU03 y CU01: Gestionar Estudiante/Usuario - actualiza alumno y usuario.
Route::put('/admin/alumnos/{id}', [App\Http\Controllers\Admin\AlumnoController::class, 'update'])->name('admin.alumnos.update')->middleware(['auth', 'can:admin.alumnos.index']);
// CU03 y CU01: Gestionar Estudiante/Usuario - elimina alumno y usuario vinculado.
Route::delete('/admin/alumnos/{id}', [App\Http\Controllers\Admin\AlumnoController::class, 'destroy'])->name('admin.alumnos.destroy')->middleware(['auth', 'can:admin.alumnos.index']);

// CU02: Gestionar Docente - listado de docentes.
Route::get('/admin/profesores', [App\Http\Controllers\Admin\ProfesorController::class, 'index'])->name('admin.profesores.index')->middleware(['auth', 'can:admin.profesores.index']);
// CU02: Gestionar Docente - formulario de creacion.
Route::get('/admin/profesores/create', [App\Http\Controllers\Admin\ProfesorController::class, 'create'])->name('admin.profesores.create')->middleware(['auth', 'can:admin.profesores.index']);
// CU02 y CU01: Gestionar Docente/Usuario - crea docente y usuario de acceso.
Route::post('/admin/profesores', [App\Http\Controllers\Admin\ProfesorController::class, 'store'])->name('admin.profesores.store')->middleware(['auth', 'can:admin.profesores.index']);
// CU02 y CU01: Gestionar Docente/Usuario - formulario para editar acceso.
Route::get('/admin/profesores/{id}/edit', [App\Http\Controllers\Admin\ProfesorController::class, 'edit'])->name('admin.profesores.edit')->middleware(['auth', 'can:admin.profesores.index']);
// CU02 y CU01: Gestionar Docente/Usuario - actualiza usuario y permiso del docente.
Route::put('/admin/profesores/{id}', [App\Http\Controllers\Admin\ProfesorController::class, 'update'])->name('admin.profesores.update')->middleware(['auth', 'can:admin.profesores.index']);
// CU02: Gestionar Docente - formulario para editar informacion personal.
Route::get('/admin/profesores/{id}/edit-info', [App\Http\Controllers\Admin\ProfesorController::class, 'editInfo'])->name('admin.profesores.editInfo')->middleware(['auth', 'can:admin.profesores.index']);
// CU02: Gestionar Docente - actualiza informacion personal.
Route::put('/admin/profesores/{id}/info', [App\Http\Controllers\Admin\ProfesorController::class, 'updateInfo'])->name('admin.profesores.updateInfo')->middleware(['auth', 'can:admin.profesores.index']);
// CU02 y CU01: Gestionar Docente/Usuario - elimina docente y usuario relacionado.
Route::delete('/admin/profesores/{id}', [App\Http\Controllers\Admin\ProfesorController::class, 'destroy'])->name('admin.profesores.destroy')->middleware(['auth', 'can:admin.profesores.index']);

Route::get('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'edit'])->name('admin.password.edit')->middleware('auth');
Route::put('/admin/password', [App\Http\Controllers\Admin\CambiarPasswordController::class, 'update'])->name('admin.password.update')->middleware('auth');

// CU08: Gestionar Perfil - consulta datos actuales del usuario autenticado.
Route::get('/profile', [PerfilController::class, 'show'])->name('profile')->middleware('auth');
// CU08: Gestionar Perfil - actualiza datos personales permitidos sin modificar rol, CI ni fecha de nacimiento.
Route::put('/profile', [PerfilController::class, 'update'])->name('profile.update')->middleware('auth');
// CU08: Gestionar Perfil - cambia contrasena validando la contrasena actual.
Route::put('/profile/password', [PerfilController::class, 'updatePassword'])->name('profile.password')->middleware('auth');

Route::get('/admin/gestiones', [App\Http\Controllers\GestionController::class, 'index'])->name('admin.gestiones.index')->middleware(['auth', 'can:admin.gestiones.index']);
Route::get('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'create'])->name('admin.gestiones.create')->middleware(['auth', 'can:admin.gestiones.index']);
Route::post('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'store'])->name('admin.gestiones.store')->middleware(['auth', 'can:admin.gestiones.index']);
Route::get('/admin/gestiones/{id}/edit', [App\Http\Controllers\GestionController::class, 'edit'])->name('admin.gestiones.edit')->middleware(['auth', 'can:admin.gestiones.index']);
Route::put('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'update'])->name('admin.gestiones.update')->middleware(['auth', 'can:admin.gestiones.index']);
// CU22: Gestionar Anio Escolar - activa una gestion y desactiva las demas.
Route::put('/admin/gestiones/{id}/activar', [App\Http\Controllers\GestionController::class, 'activar'])->name('admin.gestiones.activar')->middleware(['auth', 'can:admin.gestiones.index']);
Route::delete('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'destroy'])->name('admin.gestiones.destroy')->middleware(['auth', 'can:admin.gestiones.index']);

// CU10: Gestionar Modulo - CRUD de modulos del sistema.
Route::resource('/admin/modulos', ModuloController::class, ['as' => 'admin'])
    ->except(['show'])
    ->middleware(['auth', 'can:admin.modulos.index']);
// CU09: Gestionar Funcionalidad - CRUD de acciones o permisos por modulo.
Route::resource('/admin/funcionalidades', FuncionalidadController::class, ['as' => 'admin'])
    ->except(['show'])
    ->parameters(['funcionalidades' => 'funcionalidad'])
    ->middleware(['auth', 'can:admin.funcionalidades.index']);
// Permisos por rol - administra la tabla rol_funcionalidad.
Route::get('/admin/permisos', [PermisoRolController::class, 'index'])
    ->name('admin.permisos.index')
    ->middleware(['auth', 'can:admin.permisos.index']);
Route::put('/admin/permisos/{rol}', [PermisoRolController::class, 'update'])
    ->name('admin.permisos.update')
    ->middleware(['auth', 'can:admin.permisos.index']);
// CU24: Gestionar Personal Administrativo - CRUD con usuario generado automaticamente.
Route::resource('/admin/personal-administrativo', PersonalAdministrativoController::class, ['as' => 'admin'])
    ->except(['show'])
    ->parameters(['personal-administrativo' => 'personalAdministrativo'])
    ->middleware(['auth', 'can:admin.personal-administrativo.index']);

// CU01: Gestionar Usuario - CRUD de credenciales de acceso del sistema.
Route::resource('/admin/usuarios', UsuarioController::class, ['as' => 'admin'])
    ->except(['show'])
    ->parameters(['usuarios' => 'usuario'])
    ->middleware(['auth', 'can:admin.usuarios.index']);

// CU04: Gestionar Tutor - CRUD de apoderados y vinculacion con estudiantes.
Route::resource('/admin/apoderados', ApoderadoController::class, ['as' => 'admin'])
    ->except(['show'])
    ->middleware(['auth', 'can:admin.apoderados.index']);

// CU11: Gestionar Matricula - inscripcion de estudiantes a curso y gestion.
Route::get('/admin/matriculas', [MatriculaController::class, 'index'])->name('admin.matriculas.index')->middleware(['auth', 'can:admin.matriculas.index']);
Route::get('/admin/matriculas/create', [MatriculaController::class, 'create'])->name('admin.matriculas.create')->middleware(['auth', 'can:admin.matriculas.index']);
Route::post('/admin/matriculas', [MatriculaController::class, 'store'])->name('admin.matriculas.store')->middleware(['auth', 'can:admin.matriculas.index']);
Route::get('/admin/matriculas/{idInscripcion}/edit', [MatriculaController::class, 'edit'])->name('admin.matriculas.edit')->middleware(['auth', 'can:admin.matriculas.index']);
Route::put('/admin/matriculas/{idInscripcion}', [MatriculaController::class, 'update'])->name('admin.matriculas.update')->middleware(['auth', 'can:admin.matriculas.index']);
Route::patch('/admin/matriculas/{idInscripcion}/estado', [MatriculaController::class, 'cambiarEstado'])->name('admin.matriculas.estado')->middleware(['auth', 'can:admin.matriculas.index']);
Route::delete('/admin/matriculas/{idInscripcion}', [MatriculaController::class, 'destroy'])->name('admin.matriculas.destroy')->middleware(['auth', 'can:admin.matriculas.index']);

Route::get('/admin/cursos', [App\Http\Controllers\Admin\CursoController::class, 'index'])->name('admin.cursos.index')->middleware(['auth', 'can:admin.cursos.index']);
Route::get('/admin/cursos/create', [App\Http\Controllers\Admin\CursoController::class, 'create'])->name('admin.cursos.create')->middleware(['auth', 'can:admin.cursos.index']);
Route::post('/admin/cursos', [App\Http\Controllers\Admin\CursoController::class, 'store'])->name('admin.cursos.store')->middleware(['auth', 'can:admin.cursos.index']);
Route::get('/admin/cursos/{id}/edit', [App\Http\Controllers\Admin\CursoController::class, 'edit'])->name('admin.cursos.edit')->middleware(['auth', 'can:admin.cursos.index']);
Route::put('/admin/cursos/{id}', [App\Http\Controllers\Admin\CursoController::class, 'update'])->name('admin.cursos.update')->middleware(['auth', 'can:admin.cursos.index']);
Route::delete('/admin/cursos/{id}', [App\Http\Controllers\Admin\CursoController::class, 'destroy'])->name('admin.cursos.destroy')->middleware(['auth', 'can:admin.cursos.index']);

Route::get('/admin/materias', [App\Http\Controllers\Admin\MateriaController::class, 'index'])->name('admin.materias.index')->middleware(['auth', 'can:admin.materias.index']);
Route::get('/admin/materias/create', [App\Http\Controllers\Admin\MateriaController::class, 'create'])->name('admin.materias.create')->middleware(['auth', 'can:admin.materias.index']);
Route::post('/admin/materias', [App\Http\Controllers\Admin\MateriaController::class, 'store'])->name('admin.materias.store')->middleware(['auth', 'can:admin.materias.index']);
Route::get('/admin/materias/{id}/edit', [App\Http\Controllers\Admin\MateriaController::class, 'edit'])->name('admin.materias.edit')->middleware(['auth', 'can:admin.materias.index']);
Route::put('/admin/materias/{id}', [App\Http\Controllers\Admin\MateriaController::class, 'update'])->name('admin.materias.update')->middleware(['auth', 'can:admin.materias.index']);
Route::delete('/admin/materias/{id}', [App\Http\Controllers\Admin\MateriaController::class, 'destroy'])->name('admin.materias.destroy')->middleware(['auth', 'can:admin.materias.index']);

// CU15: Gestionar Nota - registro, edicion, consulta, busqueda y eliminacion de calificaciones.
Route::get('/admin/notas', [NotaController::class, 'index'])->name('admin.notas.index')->middleware(['auth', 'can:admin.notas.index']);
Route::get('/admin/notas/create', [NotaController::class, 'create'])->name('admin.notas.create')->middleware(['auth', 'can:admin.notas.index']);
Route::post('/admin/notas', [NotaController::class, 'store'])->name('admin.notas.store')->middleware(['auth', 'can:admin.notas.index']);
Route::get('/admin/notas/{idAlumno}/{idMateria}/{idGestion}/{idCurso}/{idTrimestre}/edit', [NotaController::class, 'edit'])->name('admin.notas.edit')->middleware(['auth', 'can:admin.notas.index']);
Route::put('/admin/notas/{idAlumno}/{idMateria}/{idGestion}/{idCurso}/{idTrimestre}', [NotaController::class, 'update'])->name('admin.notas.update')->middleware(['auth', 'can:admin.notas.index']);
Route::delete('/admin/notas/{idAlumno}/{idMateria}/{idGestion}/{idCurso}/{idTrimestre}', [NotaController::class, 'destroy'])->name('admin.notas.destroy')->middleware(['auth', 'can:admin.notas.index']);

// CU18: Gestionar Mensualidad - genera obligaciones, consulta estados y registra pagos mensuales.
Route::get('/admin/mensualidades', [MensualidadController::class, 'index'])->name('admin.mensualidades.index')->middleware(['auth', 'can:admin.mensualidades.index']);
Route::get('/admin/mensualidades/create', [MensualidadController::class, 'create'])->name('admin.mensualidades.create')->middleware(['auth', 'can:admin.mensualidades.index']);
Route::post('/admin/mensualidades', [MensualidadController::class, 'store'])->name('admin.mensualidades.store')->middleware(['auth', 'can:admin.mensualidades.index']);
Route::patch('/admin/mensualidades/{idPagoMensual}/pago', [MensualidadController::class, 'registrarPago'])->name('admin.mensualidades.pago')->middleware(['auth', 'can:admin.mensualidades.index']);

// CU17: Gestionar Pago - registra, edita, anula, consulta y busca pagos.
Route::get('/admin/pagos', [PagoController::class, 'index'])->name('admin.pagos.index')->middleware(['auth', 'can:admin.pagos.index']);
Route::get('/admin/pagos/create', [PagoController::class, 'create'])->name('admin.pagos.create')->middleware(['auth', 'can:admin.pagos.index']);
Route::post('/admin/pagos', [PagoController::class, 'store'])->name('admin.pagos.store')->middleware(['auth', 'can:admin.pagos.index']);
Route::get('/admin/pagos/{tipo}/{referencia}/edit', [PagoController::class, 'edit'])->name('admin.pagos.edit')->middleware(['auth', 'can:admin.pagos.index']);
Route::put('/admin/pagos/{tipo}/{referencia}', [PagoController::class, 'update'])->name('admin.pagos.update')->middleware(['auth', 'can:admin.pagos.index']);
Route::patch('/admin/pagos/{tipo}/{referencia}/anular', [PagoController::class, 'anular'])->name('admin.pagos.anular')->middleware(['auth', 'can:admin.pagos.index']);

// CU23: Gestionar Ficha Medica - CRUD de informacion medica por estudiante.
Route::resource('/admin/fichas-medicas', FichaMedicaController::class, ['as' => 'admin'])
    ->parameters(['fichas-medicas' => 'ficha'])
    ->middleware(['auth', 'can:admin.fichas-medicas.index']);

// CU20: Gestionar Infraestructura - CRUD de aulas y ambientes institucionales.
Route::resource('/admin/infraestructura', InfraestructuraController::class, ['as' => 'admin'])
    ->except(['show'])
    ->middleware(['auth', 'can:admin.infraestructura.index']);

// CU14: Gestionar Horario - asignacion de dias, horas y aulas para clases.
Route::get('/admin/horarios', [AdminHorarioController::class, 'index'])->name('admin.horarios.index')->middleware(['auth', 'can:admin.horarios.index']);
Route::get('/admin/horarios/create', [AdminHorarioController::class, 'create'])->name('admin.horarios.create')->middleware(['auth', 'can:admin.horarios.index']);
Route::post('/admin/horarios', [AdminHorarioController::class, 'store'])->name('admin.horarios.store')->middleware(['auth', 'can:admin.horarios.index']);
Route::get('/admin/horarios/{idMateria}/{idGestion}/{idCurso}/{idParalelo}/edit', [AdminHorarioController::class, 'edit'])->name('admin.horarios.edit')->middleware(['auth', 'can:admin.horarios.index']);
Route::put('/admin/horarios/{idMateria}/{idGestion}/{idCurso}/{idParalelo}', [AdminHorarioController::class, 'update'])->name('admin.horarios.update')->middleware(['auth', 'can:admin.horarios.index']);
Route::delete('/admin/horarios/{idMateria}/{idGestion}/{idCurso}/{idParalelo}', [AdminHorarioController::class, 'destroy'])->name('admin.horarios.destroy')->middleware(['auth', 'can:admin.horarios.index']);



// CU05: Gestionar Bitacora - consulta del historial de acciones.
Route::get('/admin/bitacora', [App\Http\Controllers\Admin\BitacoraController::class, 'index'])->name('admin.bitacora.index')->middleware(['auth', 'can:admin.bitacora.index']);

// CU21: Generar Reporte - modulo para la generacion, visualizacion y exportacion de reportes.
Route::get('/admin/reportes', [App\Http\Controllers\Admin\ReporteController::class, 'index'])->name('admin.reportes.index')->middleware(['auth', 'can:admin.reportes.index']);
Route::post('/admin/reportes/generar', [App\Http\Controllers\Admin\ReporteController::class, 'generar'])->name('admin.reportes.generar')->middleware(['auth', 'can:admin.reportes.index']);
Route::get('/admin/reportes/exportar', [App\Http\Controllers\Admin\ReporteController::class, 'exportar'])->name('admin.reportes.exportar')->middleware(['auth', 'can:admin.reportes.index']);

// Modulo de Reportes Estáticos
Route::get('/admin/reportes-estaticos', [App\Http\Controllers\Admin\ReporteEstaticoController::class, 'index'])->name('admin.reportes_estaticos.index')->middleware(['auth', 'can:admin.reportes_estaticos.index']);

Route::get('/profesor/horario', [App\Http\Controllers\Profesor\HorarioController::class, 'index'])->name('profesor.horario')->middleware(['auth', 'can:profesor.horario']);

// CU04: Gestionar Tutor - ruta relacionada con el tutor/apoderado para consultar hijos y notas.
Route::get('/apoderado/consulta', [App\Http\Controllers\Apoderado\ConsultaController::class, 'index'])
    ->name('apoderado.consulta')
    ->middleware(['auth', 'can:apoderado.consulta']);
