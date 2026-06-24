<?php

namespace App\Http\Controllers\Profesor;

use App\Domain\Auth\Services\AuthService;
use App\Domain\Horarios\Services\HorarioService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// CU14 y CU02: Controlador para que docentes consulten horario y administradores revisen horarios.
class HorarioController extends Controller
{
    // CU14: Inyecta servicios de horario y autenticacion.
    public function __construct(
        protected HorarioService $horarioService,
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    // CU14: Muestra horario por docente, agrupado por dia.
    public function index(Request $request)
    {
        // CU14: Mide el rendimiento completo de la consulta.
        $inicio = microtime(true);
        // CU01: Usuario autenticado.
        $user = Auth::user();
        $idProfesor = null;

        // CU14: El administrador puede elegir docente desde filtro.
        if ($this->authService->esAdmin($user)) {
            $idProfesor = $request->get('id_profesor', 1);
        } else {
            // CU02: El profesor ve su propio horario segun id_user.
            $inicioProfesorUser = microtime(true);
            $profesor = $this->horarioService->obtenerProfesorPorUserId($user->id_user);

            Log::info('[PERF] HorarioController@index obtenerProfesorPorUserId', [
                'ms' => round((microtime(true) - $inicioProfesorUser) * 1000, 2),
                'id_user' => $user->id_user ?? null,
            ]);

            if ($profesor) {
                $idProfesor = $profesor->id_profesor;
            } else {
                // CU02: Soporte para usuarios antiguos tipo profesor_001.
                $idProfesor = $this->horarioService->extraerIdProfesorDesdeUsername($user->username);
            }
        }

        // CU14: Coleccion vacia si no se pudo resolver docente.
        $horarios = collect();

        // CU14: Consulta horario solo si existe docente resuelto.
        if ($idProfesor) {
            $inicioHorario = microtime(true);
            $horarios = $this->horarioService->obtenerHorarioProfesor((int) $idProfesor);

            Log::info('[PERF] HorarioController@index obtenerHorarioProfesor', [
                'ms' => round((microtime(true) - $inicioHorario) * 1000, 2),
                'id_user' => $user->id_user ?? null,
                'id_profesor' => $idProfesor,
                'cantidad' => $horarios->count(),
            ]);
        }

        // CU14: Agrupa las clases por dia para dibujar la tabla semanal.
        $inicioAgrupar = microtime(true);
        $horariosPorDia = $this->horarioService->agruparPorDia($horarios);

        Log::info('[PERF] HorarioController@index agruparPorDia', [
            'ms' => round((microtime(true) - $inicioAgrupar) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        $inicioProfesorId = microtime(true);
        $profesor = $idProfesor ? $this->horarioService->obtenerProfesorPorId((int) $idProfesor) : null;

        Log::info('[PERF] HorarioController@index obtenerProfesorPorId', [
            'ms' => round((microtime(true) - $inicioProfesorId) * 1000, 2),
            'id_user' => $user->id_user ?? null,
            'id_profesor' => $idProfesor,
        ]);

        $inicioProfesores = microtime(true);
        $profesores = $this->authService->esAdmin($user)
            ? $this->horarioService->obtenerTodosLosProfesores()
            : [];

        Log::info('[PERF] HorarioController@index obtenerTodosLosProfesores', [
            'ms' => round((microtime(true) - $inicioProfesores) * 1000, 2),
            'id_user' => $user->id_user ?? null,
            'es_admin' => $this->authService->esAdmin($user),
            'cantidad' => is_countable($profesores) ? count($profesores) : 0,
        ]);

        Log::info('[PERF] HorarioController@index total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
            'id_profesor' => $idProfesor,
        ]);

        return view('profesor.horario', compact(
            'horariosPorDia',
            'dias',
            'profesor',
            'profesores',
            'user',
            'idProfesor'
        ));
    }
}
