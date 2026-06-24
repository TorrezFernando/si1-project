<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReporteEstaticoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $rol = (int) $user->id_rol;

        // Cargar gestiones académicas ordenada por fecha de inicio descendente
        $gestiones = DB::table('gestion')->orderByDesc('fechainicio')->get();
        $cursos = collect();
        $materias = collect();
        $hijos = collect();
        $alumnos = collect();

        // Obtener filtros enviados
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;
        $idAlumno = $request->integer('id_alumno') ?: null;

        // Consulta base de calificaciones (tomando como ejemplo el modelo dinámico)
        $query = DB::table('nota as n')
            ->join('alumno as a', 'a.id_alumno', '=', 'n.id_alumno')
            ->join('materia as m', 'm.id_materia', '=', 'n.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'n.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'n.id_curso')
            ->join('trimestre as t', 't.id_trimestre', '=', 'n.id_trimestre')
            ->select(
                'n.id_alumno',
                DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as estudiante"),
                'a.ci',
                'm.id_materia',
                'm.nombre as materia',
                'c.id_curso',
                'c.nombre as curso',
                'g.id_gestion',
                'g.nombre as gestion',
                't.id_trimestre as trimestre_num',
                DB::raw("CONCAT('Trimestre ', t.id_trimestre) as trimestre"),
                'n.ser',
                'n.saber',
                'n.hacer',
                'n.autoevaluacion',
                'n.promediofinal'
            );

        // Control de Acceso y Población de Catálogos por Rol
        if ($rol === Rol::ADMIN->value || $rol === Rol::SECRETARIA->value) {
            // Acceso Total: Carga todos los catálogos
            $cursos = DB::table('curso')->orderBy('nombre')->get();
            $materias = DB::table('materia')->orderBy('nombre')->get();
            $alumnos = DB::table('alumno')
                ->select('id_alumno', DB::raw("CONCAT_WS(' ', ap_paterno, ap_materno, nombres) as nombre_completo"))
                ->orderBy('nombre_completo')
                ->get();

            if ($idGestion) {
                $query->where('n.id_gestion', $idGestion);
            }
            if ($idCurso) {
                $query->where('n.id_curso', $idCurso);
            }
            if ($idMateria) {
                $query->where('n.id_materia', $idMateria);
            }
            if ($idAlumno) {
                $query->where('n.id_alumno', $idAlumno);
            }
        } elseif ($rol === Rol::PROFESOR->value) {
            // Docente: Solo tiene acceso a los estudiantes de sus materias
            $idProfesor = DB::table('profesor')->where('id_user', $user->id_user)->value('id_profesor');
            
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

                // Unir con materia_curso_gestion para restringir a sus materias/cursos/gestión asignados
                $query->join('materia_curso_gestion as mcg', function($join) use ($idProfesor) {
                    $join->on('mcg.id_materia', '=', 'n.id_materia')
                         ->on('mcg.id_curso', '=', 'n.id_curso')
                         ->on('mcg.id_gestion', '=', 'n.id_gestion')
                         ->where('mcg.id_profesor', '=', $idProfesor);
                });

                if ($idGestion) {
                    $query->where('n.id_gestion', $idGestion);
                }
                if ($idCurso) {
                    $query->where('n.id_curso', $idCurso);
                }
                if ($idMateria) {
                    $query->where('n.id_materia', $idMateria);
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($rol === Rol::APODERADO->value) {
            // Apoderados: Solo tienen acceso a sus hijos
            $idApoderado = DB::table('apoderado')->where('id_user', $user->id_user)->value('id_apoderado');
            if (!$idApoderado && str_starts_with($user->username, 'apoderado_')) {
                $idApoderado = (int) substr($user->username, 10);
            }

            if ($idApoderado) {
                $hijos = DB::table('parentesco as p')
                    ->join('alumno as a', 'a.id_alumno', '=', 'p.id_alumno')
                    ->where('p.id_apoderado', $idApoderado)
                    ->select('a.id_alumno', DB::raw("CONCAT_WS(' ', a.ap_paterno, a.ap_materno, a.nombres) as nombre_completo"))
                    ->orderBy('nombre_completo')
                    ->get();

                $hijosIds = $hijos->pluck('id_alumno')->toArray();
                $query->whereIn('n.id_alumno', $hijosIds);

                if ($idGestion) {
                    $query->where('n.id_gestion', $idGestion);
                }
                if ($idAlumno) {
                    $query->where('n.id_alumno', $idAlumno);
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            abort(403, 'Rol no autorizado.');
        }

        // Lógica de visualización del reporte
        $mostrarReporte = false;
        $reporteData = collect();

        if ($rol === Rol::ADMIN->value || $rol === Rol::SECRETARIA->value) {
            // Requiere al menos seleccionar la Gestión para no sobrecargar el renderizado inicial
            if ($idGestion) {
                $mostrarReporte = true;
                $reporteData = $query->orderBy('estudiante')
                    ->orderBy('materia')
                    ->orderBy('trimestre_num')
                    ->get();
            }
        } elseif ($rol === Rol::PROFESOR->value) {
            // Docentes: Requiere seleccionar Gestión, Curso y Materia
            if ($idGestion && $idCurso && $idMateria) {
                $mostrarReporte = true;
                $reporteData = $query->orderBy('estudiante')
                    ->orderBy('trimestre_num')
                    ->get();
            }
        } elseif ($rol === Rol::APODERADO->value) {
            // Autoseleccionar hijo si es único
            if ($hijos->count() === 1 && !$idAlumno) {
                $idAlumno = $hijos->first()->id_alumno;
            }
            if ($idGestion && $idAlumno) {
                $mostrarReporte = true;
                $reporteData = $query->where('n.id_alumno', $idAlumno)
                    ->orderBy('materia')
                    ->orderBy('trimestre_num')
                    ->get();
            }
        }

        // Estructuración del Boletín de Notas por Materia y Trimestre (Apoderados)
        $boletinTemp = [];
        if ($mostrarReporte && $rol === Rol::APODERADO->value) {
            foreach ($reporteData as $row) {
                $materiaName = $row->materia;
                if (!isset($boletinTemp[$materiaName])) {
                    $boletinTemp[$materiaName] = [
                        'materia' => $materiaName,
                        'trimestres' => [
                            1 => ['ser' => '-', 'saber' => '-', 'hacer' => '-', 'autoevaluacion' => '-', 'promedio' => '-'],
                            2 => ['ser' => '-', 'saber' => '-', 'hacer' => '-', 'autoevaluacion' => '-', 'promedio' => '-'],
                            3 => ['ser' => '-', 'saber' => '-', 'hacer' => '-', 'autoevaluacion' => '-', 'promedio' => '-'],
                        ],
                        'promedio_anual' => 0,
                    ];
                }
                $tNum = (int)$row->trimestre_num;
                if (in_array($tNum, [1, 2, 3])) {
                    $boletinTemp[$materiaName]['trimestres'][$tNum] = [
                        'ser' => $row->ser,
                        'saber' => $row->saber,
                        'hacer' => $row->hacer,
                        'autoevaluacion' => $row->autoevaluacion,
                        'promedio' => $row->promediofinal,
                    ];
                }
            }

            // Calcular el promedio anual
            foreach ($boletinTemp as $mName => $mDetails) {
                $sum = 0;
                $count = 0;
                foreach ($mDetails['trimestres'] as $tData) {
                    if ($tData['promedio'] !== '-') {
                        $sum += (float)$tData['promedio'];
                        $count++;
                    }
                }
                $boletinTemp[$mName]['promedio_anual'] = $count > 0 ? ($sum / $count) : '-';
            }
            $boletin = collect($boletinTemp);
        } else {
            $boletin = collect();
        }

        return view('admin.reportes-estaticos.index', compact(
            'rol', 'gestiones', 'cursos', 'materias', 'hijos', 'alumnos',
            'idGestion', 'idCurso', 'idMateria', 'idAlumno',
            'mostrarReporte', 'reporteData', 'boletin'
        ));
    }
}
