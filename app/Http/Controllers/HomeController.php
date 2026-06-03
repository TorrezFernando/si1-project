<?php

namespace App\Http\Controllers;

use App\Domain\Auth\Services\AuthService;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// CU06: Controlador del panel inicial despues de iniciar sesion.
class HomeController extends Controller
{
    // CU06: Inyecta servicio de autenticacion y exige sesion activa.
    public function __construct(
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    // CU06: Decide si muestra el panel o redirige segun el rol del usuario.
    public function index()
    {
        // CU06: Mide rendimiento del flujo de entrada al panel.
        $inicio = microtime(true);
        // CU01: Usuario autenticado.
        $user = Auth::user();

        // CU06: Consulta si el usuario debe ir a una pantalla especifica.
        $inicioRedirect = microtime(true);
        $redirectRoute = $this->authService->redirectAfterHome($user);

        Log::info('[PERF] HomeController@index redirectAfterHome', [
            'ms' => round((microtime(true) - $inicioRedirect) * 1000, 2),
            'redirect_to' => $redirectRoute,
            'id_user' => $user->id_user ?? null,
        ]);

        // CU06: Si el rol tiene pantalla propia, redirige antes de cargar el panel.
        if ($redirectRoute !== route('home-panel')) {
            Log::info('[PERF] HomeController@index total', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'resultado' => 'redirect',
                'id_user' => $user->id_user ?? null,
            ]);

            return redirect($redirectRoute);
        }

        // Configuracion institucional que se muestra en el panel.
        $inicioConfiguracion = microtime(true);
        $configuracion = Configuracion::first();

        Log::info('[PERF] HomeController@index configuracion', [
            'ms' => round((microtime(true) - $inicioConfiguracion) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        // Datos para widgets del dashboard segun el rol
        $stats = [];
        $idRol = $user->id_rol;

        if (in_array($idRol, [1, 5, 6])) {
            $stats['total_alumnos'] = \App\Models\Alumno::count();
            $stats['total_profesores'] = \App\Models\Profesor::count();
            $stats['total_cursos'] = \App\Models\Curso::count();
            $stats['total_materias'] = \App\Models\Materia::count();
        }

        if ($idRol == 2) {
            $profesor = \App\Models\Profesor::where('id_user', $user->id_user)->first();
            if ($profesor) {
                $stats['total_horarios'] = \App\Models\Horario::where('id_profesor', $profesor->id_profesor)->count();
                $stats['materias_count'] = \App\Models\Horario::where('id_profesor', $profesor->id_profesor)
                    ->distinct('id_materia')->count('id_materia');
            }
            $stats['profesor'] = $profesor;
        }

        if ($idRol == 3) {
            $alumno = \App\Models\Alumno::where('id_user', $user->id_user)->first();
            if ($alumno) {
                $stats['total_notas'] = \App\Models\Nota::where('id_alumno', $alumno->id_alumno)->count();
            }
            $stats['alumno'] = $alumno;
        }

        if ($idRol == 4) {
            $apoderado = \App\Models\Apoderado::where('id_user', $user->id_user)->first();
            if ($apoderado) {
                $stats['total_hijos'] = \App\Models\Parentesco::where('id_apoderado', $apoderado->id_apoderado)->count();
            }
            $stats['apoderado'] = $apoderado;
        }

        Log::info('[PERF] HomeController@index total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'resultado' => 'view',
            'id_user' => $user->id_user ?? null,
        ]);

        return view('home', compact('configuracion', 'stats'));
    }
}
