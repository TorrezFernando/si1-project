<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ReporteController extends Controller
{
    private const REPORTES_POR_ROL = [
        1 => [
            'admin_usuarios',
            'admin_bitacora',
            'admin_estudiantes_curso',
            'admin_matriculas_gestion',
            'admin_pagos_estado',
            'admin_mora',
        ],
        6 => [
            'admin_estudiantes_curso',
            'admin_matriculas_gestion',
            'admin_pagos_estado',
            'admin_mora',
        ],
        2 => [
            'docente_notas',
            'docente_asistencia',
            'admin_estudiantes_curso',
        ],
        4 => [
            'tutor_calificaciones',
            'tutor_asistencia',
            'tutor_estado_cuenta',
        ],
    ];

    private const REPORTES_REQUIEREN_ALUMNO = [
        'tutor_calificaciones',
        'tutor_asistencia',
        'tutor_estado_cuenta',
    ];

    public function index(Request $request)
    {
        $rol = (int) $request->user()->id_rol;
        $catalogos = $this->obtenerCatalogos($request);

        return view('admin.reportes.index', compact('rol', 'catalogos'));
    }

    public function generar(Request $request)
    {
        $this->validarSolicitudReporte($request);

        $tipo = $request->input('tipo_reporte');
        $datos = $this->obtenerDatosReporte($request, $tipo);

        if ($this->sinDatos($datos)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontraron datos que cumplan con los criterios de busqueda.',
            ]);
        }

        return response()->json([
            'success' => true,
            'datos' => $datos->values(),
        ]);
    }

    public function exportar(Request $request)
    {
        $this->validarSolicitudReporte($request, true);

        $tipo = $request->input('tipo_reporte');
        $formato = $request->input('formato');
        $datos = $this->obtenerDatosReporte($request, $tipo);

        if ($this->sinDatos($datos)) {
            return redirect()->route('admin.reportes.index')
                ->with('mensaje', 'No hay datos para exportar.')
                ->with('icono', 'error');
        }

        if ($formato === 'print' || $formato === 'pdf') {
            return view('admin.reportes.imprimir', [
                'datos' => $datos->values(),
                'tipo' => $tipo,
                'formato' => $formato,
            ]);
        }

        return $this->generarArchivoDescarga($datos->values(), $tipo, $formato);
    }

    private function validarSolicitudReporte(Request $request, bool $exportar = false): void
    {
        $formatos = $exportar ? ['required', 'string', Rule::in(['csv', 'excel', 'print', 'pdf'])] : ['nullable'];

        $request->validate([
            'tipo_reporte' => ['required', 'string'],
            'formato' => $formatos,
            'id_gestion' => ['nullable', 'integer'],
            'id_curso' => ['nullable', 'integer'],
            'id_materia' => ['nullable', 'integer'],
            'id_alumno' => ['nullable', 'integer'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $tipo = $request->input('tipo_reporte');
        $rol = (int) $request->user()->id_rol;
        $permitidos = self::REPORTES_POR_ROL[$rol] ?? [];

        if (! in_array($tipo, $permitidos, true)) {
            abort(403, 'No tiene permiso para generar este reporte.');
        }

        if (in_array($tipo, self::REPORTES_REQUIEREN_ALUMNO, true) && ! $request->integer('id_alumno')) {
            abort(422, 'Debe seleccionar un estudiante.');
        }

        if ($tipo === 'docente_asistencia') {
            $request->validate([
                'id_gestion' => ['required', 'integer'],
                'id_curso' => ['required', 'integer'],
                'id_materia' => ['required', 'integer'],
            ]);
        }

        if ($tipo === 'docente_notas') {
            $request->validate([
                'id_gestion' => ['required', 'integer'],
                'id_curso' => ['required', 'integer'],
                'id_materia' => ['required', 'integer'],
            ]);
        }
    }

    private function obtenerDatosReporte(Request $request, string $tipo): Collection
    {
        $rol = (int) $request->user()->id_rol;

        return match ($tipo) {
            'admin_usuarios' => $this->reporteUsuarios(),
            'admin_bitacora' => $this->reporteBitacora($request),
            'admin_estudiantes_curso' => $this->reporteEstudiantesCurso($request, $rol),
            'admin_matriculas_gestion' => $this->reporteMatriculasGestion($request),
            'admin_pagos_estado' => $this->reportePagosEstado($request, false),
            'admin_mora' => $this->reportePagosEstado($request, true),
            'docente_notas' => $this->reporteNotasDocente($request, $rol),
            'docente_asistencia' => $this->reporteAsistenciaDocente($request),
            'tutor_calificaciones' => $this->reporteCalificacionesTutor($request),
            'tutor_asistencia' => $this->reporteAsistenciaTutor($request),
            'tutor_estado_cuenta' => $this->reporteEstadoCuentaTutor($request),
            default => collect(),
        };
    }

    private function reporteUsuarios(): Collection
    {
        return DB::table('usuario as u')
            ->join('rol as r', 'r.id_rol', '=', 'u.id_rol')
            ->select('u.id_user', 'u.username', 'r.tipo as rol')
            ->orderBy('r.tipo')
            ->orderBy('u.username')
            ->get();
    }

    private function reporteBitacora(Request $request): Collection
    {
        $search = $request->input('search');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        return DB::table('bitacora as b')
            ->join('usuario as u', 'u.id_user', '=', 'b.id_user')
            ->join('rol as r', 'r.id_rol', '=', 'u.id_rol')
            ->select('b.id_bitacora', 'u.username', 'r.tipo as rol', 'b.fecha_hora', 'b.accion', 'b.ip')
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('u.username', 'like', $like)
                        ->orWhere('b.accion', 'like', $like)
                        ->orWhere('b.ip', 'like', $like);
                });
            })
            ->when($fechaInicio, fn ($q) => $q->where('b.fecha_hora', '>=', $fechaInicio . ' 00:00:00'))
            ->when($fechaFin, fn ($q) => $q->where('b.fecha_hora', '<=', $fechaFin . ' 23:59:59'))
            ->orderByDesc('b.fecha_hora')
            ->limit(500)
            ->get();
    }

    private function reporteEstudiantesCurso(Request $request, int $rol): Collection
    {
        $idCurso = $request->integer('id_curso') ?: null;
        $idGestion = $request->integer('id_gestion') ?: null;

        if ($rol === Rol::PROFESOR->value) {
            $idProfesor = $this->obtenerIdProfesor($request->user());
            if (! $idProfesor) {
                return collect();
            }
            $this->validarAsignacionDocente($idProfesor, $idCurso, $idGestion);
        }

        return DB::table('inscripcion as i')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->select(
                'a.id_alumno',
                'a.ci',
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'a.genero',
                'c.nombre as curso',
                'icg.paralelo',
                'g.nombre as gestion'
            )
            ->when($idCurso, fn ($q) => $q->where('icg.id_curso', $idCurso))
            ->when($idGestion, fn ($q) => $q->where('icg.id_gestion', $idGestion))
            ->orderBy('estudiante')
            ->get();
    }

    private function reporteMatriculasGestion(Request $request): Collection
    {
        $idGestion = $request->integer('id_gestion') ?: null;
        $estado = Schema::hasColumn('matricula', 'estado') ? 'm.estado' : "'Registrada'";

        return DB::table('inscripcion as i')
            ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->select(
                'i.id_inscripcion',
                'm.fecha',
                'm.monto',
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'a.ci',
                'c.nombre as curso',
                'g.nombre as gestion',
                DB::raw($estado . ' as estado')
            )
            ->when($idGestion, fn ($q) => $q->where('icg.id_gestion', $idGestion))
            ->orderBy('estudiante')
            ->get();
    }

    private function reportePagosEstado(Request $request, bool $soloMora): Collection
    {
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $obligaciones = $this->obtenerObligaciones($idGestion, $idCurso);

        if (! $soloMora) {
            return $obligaciones;
        }

        $hoy = now()->toDateString();

        return $obligaciones
            ->filter(fn ($item) => $item->estado_pago !== 'Pagado' && $item->fecha_vencimiento < $hoy)
            ->values();
    }

    private function reporteNotasDocente(Request $request, int $rol): Collection
    {
        $idCurso = $request->integer('id_curso') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;
        $idGestion = $request->integer('id_gestion') ?: null;

        if ($rol === Rol::PROFESOR->value) {
            $idProfesor = $this->obtenerIdProfesor($request->user());
            if (! $idProfesor) {
                return collect();
            }
            $this->validarAsignacionDocente($idProfesor, $idCurso, $idGestion, $idMateria);
        }

        return $this->queryNotas()
            ->when($idCurso, fn ($q) => $q->where('n.id_curso', $idCurso))
            ->when($idMateria, fn ($q) => $q->where('n.id_materia', $idMateria))
            ->when($idGestion, fn ($q) => $q->where('n.id_gestion', $idGestion))
            ->orderBy('estudiante')
            ->orderBy('materia')
            ->orderBy('trimestre')
            ->get();
    }

    private function reporteAsistenciaDocente(Request $request): Collection
    {
        if (! Schema::hasTable('asistencia')) {
            return collect();
        }

        $idProfesor = $this->obtenerIdProfesor($request->user());
        if (! $idProfesor) {
            return collect();
        }

        $idCurso = $request->integer('id_curso') ?: null;
        $idGestion = $request->integer('id_gestion') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;

        $this->validarAsignacionDocente($idProfesor, $idCurso, $idGestion, $idMateria);

        return $this->queryAsistencia()
            ->addSelect('m.nombre as materia')
            ->join('materia as m', function ($join) use ($idMateria) {
                $join->where('m.id_materia', '=', $idMateria);
            })
            ->where('icg.id_curso', $idCurso)
            ->where('icg.id_gestion', $idGestion)
            ->orderBy('asist.fecha', 'desc')
            ->orderBy('estudiante')
            ->get();
    }

    private function reporteCalificacionesTutor(Request $request): Collection
    {
        $idApoderado = $this->obtenerIdApoderado($request->user());
        $idAlumno = $request->integer('id_alumno');

        $this->validarHijoTutor($idApoderado, $idAlumno);

        return $this->queryNotas()
            ->where('n.id_alumno', $idAlumno)
            ->orderBy('materia')
            ->orderBy('trimestre')
            ->get();
    }

    private function reporteAsistenciaTutor(Request $request): Collection
    {
        if (! Schema::hasTable('asistencia')) {
            return collect();
        }

        $idApoderado = $this->obtenerIdApoderado($request->user());
        $idAlumno = $request->integer('id_alumno');

        $this->validarHijoTutor($idApoderado, $idAlumno);

        return $this->queryAsistencia()
            ->where('i.id_alumno', $idAlumno)
            ->orderBy('asist.fecha', 'desc')
            ->get();
    }

    private function reporteEstadoCuentaTutor(Request $request): Collection
    {
        $idApoderado = $this->obtenerIdApoderado($request->user());
        $idAlumno = $request->integer('id_alumno');

        $this->validarHijoTutor($idApoderado, $idAlumno);

        return $this->obtenerObligaciones(null, null, $idAlumno);
    }

    private function queryNotas()
    {
        return DB::table('nota as n')
            ->join('alumno as a', 'a.id_alumno', '=', 'n.id_alumno')
            ->join('materia as m', 'm.id_materia', '=', 'n.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'n.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'n.id_curso')
            ->join('trimestre as t', 't.id_trimestre', '=', 'n.id_trimestre')
            ->select(
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'm.nombre as materia',
                'c.nombre as curso',
                'g.nombre as gestion',
                DB::raw("CONCAT('Trimestre ', t.id_trimestre) as trimestre"),
                'n.ser',
                'n.saber',
                'n.hacer',
                'n.autoevaluacion',
                'n.promediofinal'
            );
    }

    private function queryAsistencia()
    {
        return DB::table('asistencia as asist')
            ->join('matricula as mat', 'mat.id_matricula', '=', 'asist.id_matricula')
            ->join('inscripcion as i', 'i.id_matricula', '=', 'mat.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->select(
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'c.nombre as curso',
                'g.nombre as gestion',
                'asist.fecha',
                DB::raw("CASE asist.estado WHEN 'P' THEN 'Presente' WHEN 'A' THEN 'Ausente' WHEN 'F' THEN 'Falta' WHEN 'R' THEN 'Retraso' ELSE asist.estado END as estado")
            );
    }

    private function obtenerCatalogos(Request $request): array
    {
        $rol = (int) $request->user()->id_rol;

        $cursos = collect();
        $materias = collect();
        $hijos = collect();

        if ($rol === Rol::ADMIN->value || $rol === Rol::SECRETARIA->value) {
            $cursos = DB::table('curso')->orderBy('nombre')->get();
            $materias = DB::table('materia')->orderBy('nombre')->get();
        } elseif ($rol === Rol::PROFESOR->value) {
            $idProfesor = $this->obtenerIdProfesor($request->user());

            if ($idProfesor) {
                $cursos = DB::table('materia_curso_gestion as mcg')
                    ->join('curso as c', 'c.id_curso', '=', 'mcg.id_curso')
                    ->where('mcg.id_profesor', $idProfesor)
                    ->select('c.id_curso', 'c.nombre')
                    ->distinct()
                    ->orderBy('c.nombre')
                    ->get();

                $materias = DB::table('materia_curso_gestion as mcg')
                    ->join('materia as m', 'm.id_materia', '=', 'mcg.id_materia')
                    ->where('mcg.id_profesor', $idProfesor)
                    ->select('m.id_materia', 'm.nombre')
                    ->distinct()
                    ->orderBy('m.nombre')
                    ->get();
            }
        } elseif ($rol === Rol::APODERADO->value) {
            $idApoderado = $this->obtenerIdApoderado($request->user());

            if ($idApoderado) {
                $hijos = DB::table('parentesco as p')
                    ->join('alumno as a', 'a.id_alumno', '=', 'p.id_alumno')
                    ->where('p.id_apoderado', $idApoderado)
                    ->select('a.id_alumno', DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as nombre_completo"))
                    ->orderBy('nombre_completo')
                    ->get();
            }
        }

        $gestiones = DB::table('gestion')->orderByDesc('fechainicio')->get();

        return compact('cursos', 'materias', 'gestiones', 'hijos');
    }

    private function obtenerIdProfesor($user): ?int
    {
        return DB::table('profesor')->where('id_user', $user->id_user)->value('id_profesor');
    }

    private function obtenerIdApoderado($user): ?int
    {
        $id = DB::table('apoderado')->where('id_user', $user->id_user)->value('id_apoderado');

        if (! $id && str_starts_with($user->username, 'apoderado_')) {
            $id = (int) substr($user->username, 10);
        }

        return $id ?: null;
    }

    private function validarAsignacionDocente(?int $idProfesor, ?int $idCurso, ?int $idGestion, ?int $idMateria = null): void
    {
        if (! $idProfesor) {
            abort(403, 'No se encontro el docente vinculado al usuario.');
        }

        $query = DB::table('materia_curso_gestion')
            ->where('id_profesor', $idProfesor);

        if ($idCurso) {
            $query->where('id_curso', $idCurso);
        }

        if ($idGestion) {
            $query->where('id_gestion', $idGestion);
        }

        if ($idMateria) {
            $query->where('id_materia', $idMateria);
        }

        if (! $query->exists()) {
            abort(403, 'No tiene asignado este curso o materia en la gestion especificada.');
        }
    }

    private function validarHijoTutor(?int $idApoderado, int $idAlumno): void
    {
        if (! $idApoderado) {
            abort(403, 'No se encontro el tutor vinculado al usuario.');
        }

        $existe = DB::table('parentesco')
            ->where('id_apoderado', $idApoderado)
            ->where('id_alumno', $idAlumno)
            ->exists();

        if (! $existe) {
            abort(403, 'No tiene autorizacion sobre este estudiante.');
        }
    }

    private function obtenerObligaciones(?int $idGestion = null, ?int $idCurso = null, ?int $idAlumno = null): Collection
    {
        $estadoMatricula = Schema::hasColumn('matricula', 'estado') ? 'm.estado' : "'Pendiente'";
        $estadoMensualidad = Schema::hasColumn('pago_mensual', 'estado') ? 'pm.estado' : "'Pagado'";
        $fechaPagoMensual = Schema::hasColumn('pago_mensual', 'fecha_pago') ? 'pm.fecha_pago' : 'pm.fecha';

        $matriculas = DB::table('inscripcion as i')
            ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->select(
                DB::raw("'Matricula' as concepto"),
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'c.nombre as curso',
                'g.nombre as gestion',
                'm.monto as monto',
                DB::raw('0 as descuento'),
                'm.fecha as fecha_vencimiento',
                DB::raw($estadoMatricula . ' as estado_pago')
            )
            ->when($idGestion, fn ($q) => $q->where('icg.id_gestion', $idGestion))
            ->when($idCurso, fn ($q) => $q->where('icg.id_curso', $idCurso))
            ->when($idAlumno, fn ($q) => $q->where('i.id_alumno', $idAlumno))
            ->get();

        $mensualidades = DB::table('pago_mensual as pm')
            ->join('alumno as a', 'a.id_alumno', '=', 'pm.id_alumno')
            ->join('gestion as g', 'g.id_gestion', '=', 'pm.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'pm.id_curso')
            ->select(
                DB::raw("CONCAT('Mensualidad - ', pm.mes) as concepto"),
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'c.nombre as curso',
                'g.nombre as gestion',
                'pm.monto as monto',
                'pm.descuento',
                DB::raw($fechaPagoMensual . ' as fecha_vencimiento'),
                DB::raw($estadoMensualidad . ' as estado_pago')
            )
            ->when($idGestion, fn ($q) => $q->where('pm.id_gestion', $idGestion))
            ->when($idCurso, fn ($q) => $q->where('pm.id_curso', $idCurso))
            ->when($idAlumno, fn ($q) => $q->where('pm.id_alumno', $idAlumno))
            ->get();

        return $matriculas
            ->merge($mensualidades)
            ->sortBy(['estudiante', 'fecha_vencimiento'])
            ->values();
    }

    private function sinDatos(Collection $datos): bool
    {
        return $datos->isEmpty();
    }

    private function generarArchivoDescarga(Collection $datos, string $tipo, string $formato)
    {
        $filename = 'reporte_' . $tipo . '_' . date('Ymd_His');

        if ($formato === 'csv') {
            $headers = [
                'Content-type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.csv",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($datos) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                $primerDato = (array) $datos->first();
                fputcsv($file, array_keys($primerDato));

                foreach ($datos as $fila) {
                    fputcsv($file, (array) $fila);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($formato === 'excel') {
            $headers = [
                'Content-type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.xls",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($datos) {
                echo "\xEF\xBB\xBF";
                echo "<table border='1'>";
                $primerDato = (array) $datos->first();
                echo '<tr>';
                foreach (array_keys($primerDato) as $cabecera) {
                    echo '<th style="background-color:#198754;color:white;">' . e($cabecera) . '</th>';
                }
                echo '</tr>';

                foreach ($datos as $fila) {
                    echo '<tr>';
                    foreach ((array) $fila as $valor) {
                        echo '<td>' . e((string) ($valor ?? '-')) . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        abort(400);
    }
}
