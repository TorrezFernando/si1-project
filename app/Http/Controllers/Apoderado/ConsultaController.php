<?php

namespace App\Http\Controllers\Apoderado;

use App\Domain\Apoderados\Services\ApoderadoService;
use App\Domain\Auth\Services\AuthService;
use App\Domain\Notas\Services\NotaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConsultaController extends Controller
{
    public function __construct(
        protected ApoderadoService $apoderadoService,
        protected NotaService $notaService,
        protected AuthService $authService,
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $inicio = microtime(true);
        $user = Auth::user();

        if ($this->authService->esAdmin($user)) {
            return $this->indexComoAdmin($request);
        }

        if ($this->authService->esApoderado($user)) {
            return $this->indexComoApoderado($request, $user->username);
        }

        abort(403);
    }

    private function indexComoApoderado(Request $request, string $username)
    {
        $inicio = microtime(true);

        $apoderado = $this->apoderadoService->resolverPorUsername($username);

        if (!$apoderado) {
            return view('apoderado.consulta', [
                'esAdmin' => false,
                'apoderado' => null,
                'hijos' => collect(),
                'alumnoSeleccionado' => null,
                'notasPorHijo' => collect(),
                'hijoSeleccionado' => null,
                'stats' => null,
            ]);
        }

        $hijos = $this->apoderadoService->obtenerHijosDeApoderado((int) $apoderado->id_apoderado);
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(403);
        }

        // Default to first child if none selected
        if (!$hijoSeleccionado && $hijos->isNotEmpty()) {
            $hijoSeleccionado = $hijos->first()->id_alumno;
        }

        // Only load grades if a specific child is selected
        $notasPorHijo = collect();
        $stats = null;

        if ($hijoSeleccionado) {
            $notas = $this->notaService->notasFiltradasPorApoderado(
                (int) $apoderado->id_apoderado,
                $hijoSeleccionado
            );
            $notasPorHijo = $notas->groupBy('id_alumno');

            // Stats for selected student
            if ($notas->isNotEmpty()) {
                $stats = (object) [
                    'total' => $notas->count(),
                    'promedio' => round($notas->avg('promediofinal'), 2),
                    'mejor' => $notas->max('promediofinal'),
                    'materias' => $notas->pluck('materia')->unique()->count(),
                ];
            }
        }

        $alumnoSeleccionado = $hijoSeleccionado ? $hijos->firstWhere('id_alumno', $hijoSeleccionado) : null;

        Log::info('[PERF] ConsultaController@indexComoApoderado total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'username' => $username,
            'notas_cargadas' => $notasPorHijo->flatten(1)->count(),
        ]);

        return view('apoderado.consulta', compact(
            'apoderado', 'hijos', 'alumnoSeleccionado',
            'notasPorHijo', 'hijoSeleccionado', 'stats'
        ) + ['esAdmin' => false]);
    }

    private function indexComoAdmin(Request $request)
    {
        $inicio = microtime(true);
        $hijoSeleccionado = $request->filled('hijo') ? $request->integer('hijo') : null;

        $hijos = $this->apoderadoService->obtenerTodosLosHijosConApoderados();

        if ($hijoSeleccionado && !$hijos->pluck('id_alumno')->contains($hijoSeleccionado)) {
            abort(404);
        }

        // Default to first student if none selected
        if (!$hijoSeleccionado && $hijos->isNotEmpty()) {
            $hijoSeleccionado = $hijos->first()->id_alumno;
        }

        // Only load grades if a specific child is selected
        $notasPorHijo = collect();
        $stats = null;

        if ($hijoSeleccionado) {
            $notas = $this->notaService->consultaBaseNotas()
                ->where('n.id_alumno', $hijoSeleccionado)
                ->get();

            $notasPorHijo = $notas->groupBy('id_alumno');

            if ($notas->isNotEmpty()) {
                $stats = (object) [
                    'total' => $notas->count(),
                    'promedio' => round($notas->avg('promediofinal'), 2),
                    'mejor' => $notas->max('promediofinal'),
                    'materias' => $notas->pluck('materia')->unique()->count(),
                ];
            }
        }

        $alumnoSeleccionado = $hijoSeleccionado ? $hijos->firstWhere('id_alumno', $hijoSeleccionado) : null;

        Log::info('[PERF] ConsultaController@indexComoAdmin total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'notas_cargadas' => $notasPorHijo->flatten(1)->count(),
        ]);

        return view('apoderado.consulta', compact(
            'hijos', 'alumnoSeleccionado', 'notasPorHijo',
            'hijoSeleccionado', 'stats'
        ) + ['esAdmin' => true, 'apoderado' => null]);
    }
}
