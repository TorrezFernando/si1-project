<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AsistenciaController extends Controller
{
    private const ESTADOS = ['P' => 'Presente', 'A' => 'Ausente', 'L' => 'Tarde', 'F' => 'Falta justificada'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;
        $fecha = $request->input('fecha');
        $estado = $request->input('estado');

        $asistencias = $this->consultaBase()
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(m.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(c.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(g.nombre) LIKE LOWER(?)', [$like]);
                });
            })
            ->when($idGestion, fn ($query) => $query->where('asi.id_gestion', $idGestion))
            ->when($idCurso, fn ($query) => $query->where('asi.id_curso', $idCurso))
            ->when($idMateria, fn ($query) => $query->where('asi.id_materia', $idMateria))
            ->when($fecha, fn ($query) => $query->where('asi.fecha', $fecha))
            ->when($estado && array_key_exists($estado, self::ESTADOS), fn ($query) => $query->where('asi.estado', $estado))
            ->orderByDesc('asi.fecha')
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->paginate(15)
            ->appends($request->except('page'));

        $filtros = $this->catalogos();

        return view('admin.asistencias.index', array_merge($filtros, compact(
            'asistencias', 'search', 'idGestion', 'idCurso', 'idMateria', 'fecha', 'estado'
        )));
    }

    public function create()
    {
        return view('admin.asistencias.create', array_merge(
            $this->catalogos(),
            ['estados' => self::ESTADOS]
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asignacion' => ['required', 'regex:/^\d+\|\d+\|\d+$/'],
            'fecha' => ['required', 'date'],
            'alumnos' => ['required', 'array', 'min:1'],
            'alumnos.*.id_alumno' => ['required', 'integer', 'exists:alumno,id_alumno'],
            'alumnos.*.estado' => ['required', 'string', 'in:' . implode(',', array_keys(self::ESTADOS))],
        ], [
            'asignacion.required' => 'Seleccione materia, curso y gestion.',
            'asignacion.regex' => 'La asignacion seleccionada no es valida.',
            'fecha.required' => 'La fecha es obligatoria.',
            'alumnos.required' => 'Debe registrar al menos un estudiante.',
        ]);

        [$idMateria, $idGestion, $idCurso] = $this->parseAsignacion($data['asignacion']);

        $gestionActiva = DB::table('gestion')
            ->where('id_gestion', $idGestion)
            ->where('activo', true)
            ->exists();

        if (! $gestionActiva) {
            throw ValidationException::withMessages([
                'asignacion' => 'El anio lectivo seleccionado no esta activo.',
            ]);
        }

        DB::transaction(function () use ($data, $idMateria, $idGestion, $idCurso) {
            foreach ($data['alumnos'] as $alumno) {
                $idMatricula = $this->idMatriculaDelAlumno((int) $alumno['id_alumno'], $idGestion, $idCurso);

                if (! $idMatricula) {
                    continue;
                }

                $existe = DB::table('asistencia')
                    ->where('id_matricula', $idMatricula)
                    ->where('id_materia', $idMateria)
                    ->where('id_gestion', $idGestion)
                    ->where('id_curso', $idCurso)
                    ->where('fecha', $data['fecha'])
                    ->exists();

                if (! $existe) {
                    DB::table('asistencia')->insert([
                        'fecha' => $data['fecha'],
                        'estado' => $alumno['estado'],
                        'id_matricula' => $idMatricula,
                        'id_materia' => $idMateria,
                        'id_gestion' => $idGestion,
                        'id_curso' => $idCurso,
                    ]);
                }
            }
        });

        return redirect()->route('admin.asistencias.index')
            ->with('mensaje', 'Asistencia registrada exitosamente')
            ->with('icono', 'success');
    }

    public function edit(Request $request, int $idMateria, int $idGestion, int $idCurso, string $fecha)
    {
        $asistencias = $this->consultaBase()
            ->where('asi.id_materia', $idMateria)
            ->where('asi.id_gestion', $idGestion)
            ->where('asi.id_curso', $idCurso)
            ->where('asi.fecha', $fecha)
            ->get()
            ->keyBy('id_alumno');

        if ($asistencias->isEmpty()) {
            return redirect()->route('admin.asistencias.index')
                ->with('mensaje', 'No se encontraron registros de asistencia para la seleccion.')
                ->with('icono', 'error');
        }

        $alumnos = DB::table('inscripcion as i')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->where('icg.id_gestion', $idGestion)
            ->where('icg.id_curso', $idCurso)
            ->select(
                'a.id_alumno',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno")
            )
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->get()
            ->map(function ($a) use ($asistencias) {
                $a->estado = $asistencias->has($a->id_alumno) ? $asistencias[$a->id_alumno]->estado : null;
                return $a;
            });

        $materia = DB::table('materia')->where('id_materia', $idMateria)->value('nombre');
        $gestion = DB::table('gestion')->where('id_gestion', $idGestion)->value('nombre');
        $curso = DB::table('curso')->where('id_curso', $idCurso)->value('nombre');

        return view('admin.asistencias.edit', compact(
            'alumnos', 'idMateria', 'idGestion', 'idCurso', 'fecha',
            'materia', 'gestion', 'curso'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id_materia' => ['required', 'integer'],
            'id_gestion' => ['required', 'integer'],
            'id_curso' => ['required', 'integer'],
            'fecha' => ['required', 'date'],
            'alumnos' => ['required', 'array', 'min:1'],
            'alumnos.*.id_alumno' => ['required', 'integer'],
            'alumnos.*.estado' => ['required', 'string', 'in:' . implode(',', array_keys(self::ESTADOS))],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['alumnos'] as $alumno) {
                $idMatricula = $this->idMatriculaDelAlumno((int) $alumno['id_alumno'], (int) $data['id_gestion'], (int) $data['id_curso']);

                if (! $idMatricula) {
                    continue;
                }

                DB::table('asistencia')
                    ->where('id_matricula', $idMatricula)
                    ->where('id_materia', $data['id_materia'])
                    ->where('id_gestion', $data['id_gestion'])
                    ->where('id_curso', $data['id_curso'])
                    ->where('fecha', $data['fecha'])
                    ->update(['estado' => $alumno['estado']]);
            }
        });

        return redirect()->route('admin.asistencias.index')
            ->with('mensaje', 'Asistencia actualizada exitosamente')
            ->with('icono', 'success');
    }

    public function destroy(int $id)
    {
        $asistencia = DB::table('asistencia')->where('id_asistencia', $id)->firstOrFail();

        DB::table('asistencia')->where('id_asistencia', $id)->delete();

        return redirect()->route('admin.asistencias.index')
            ->with('mensaje', 'Registro de asistencia eliminado')
            ->with('icono', 'success');
    }

    public function alumnosPorAsignacion(Request $request)
    {
        $asignacion = $request->input('asignacion');

        if (! preg_match('/^(\d+)\|(\d+)\|(\d+)$/', $asignacion, $m)) {
            return response()->json(['error' => 'Asignacion invalida'], 422);
        }

        [$idMateria, $idGestion, $idCurso] = [(int) $m[1], (int) $m[2], (int) $m[3]];

        $alumnos = DB::table('inscripcion as i')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('matricula as mtr', 'mtr.id_matricula', '=', 'i.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->leftJoin('asistencia as asi', function ($join) use ($idMateria, $idGestion, $idCurso, $request) {
                $join->on('asi.id_matricula', '=', 'mtr.id_matricula')
                    ->where('asi.id_materia', $idMateria)
                    ->where('asi.id_gestion', $idGestion)
                    ->where('asi.id_curso', $idCurso)
                    ->where('asi.fecha', $request->input('fecha'));
            })
            ->where('icg.id_gestion', $idGestion)
            ->where('icg.id_curso', $idCurso)
            ->select(
                'a.id_alumno',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                DB::raw("COALESCE(asi.estado, 'P') as estado"),
                DB::raw('CASE WHEN asi.id_asistencia IS NOT NULL THEN 1 ELSE 0 END as ya_registrado')
            )
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->get();

        return response()->json($alumnos, 200, ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    public function groupEdit(string $fecha, int $idMateria, int $idGestion, int $idCurso)
    {
        return $this->edit(new Request(), $idMateria, $idGestion, $idCurso, $fecha);
    }

    private function consultaBase()
    {
        return DB::table('asistencia as asi')
            ->join('matricula as mtr', 'mtr.id_matricula', '=', 'asi.id_matricula')
            ->join('inscripcion as i', 'i.id_matricula', '=', 'mtr.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('materia as m', 'm.id_materia', '=', 'asi.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'asi.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'asi.id_curso')
            ->select(
                'asi.id_asistencia',
                'i.id_alumno',
                'asi.id_materia',
                'asi.id_gestion',
                'asi.id_curso',
                'asi.fecha',
                'asi.estado',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'm.nombre as materia',
                'c.nombre as curso',
                'g.nombre as gestion',
                DB::raw("CASE asi.estado
                    WHEN 'P' THEN 'Presente'
                    WHEN 'A' THEN 'Ausente'
                    WHEN 'L' THEN 'Tarde'
                    WHEN 'F' THEN 'Falta justificada'
                    ELSE asi.estado END as estado_texto")
            );
    }

    private function catalogos(): array
    {
        return [
            'gestiones' => DB::table('gestion')->orderByDesc('id_gestion')->get(),
            'cursos' => DB::table('curso')->orderBy('nombre')->get(),
            'materias' => DB::table('materia')->orderBy('nombre')->get(),
            'estados' => self::ESTADOS,
            'asignaciones' => $this->asignacionesDisponibles(),
        ];
    }

    private function asignacionesDisponibles()
    {
        return DB::table('materia_curso_gestion as mcg')
            ->join('materia as m', 'm.id_materia', '=', 'mcg.id_materia')
            ->join('curso as c', 'c.id_curso', '=', 'mcg.id_curso')
            ->join('gestion as g', 'g.id_gestion', '=', 'mcg.id_gestion')
            ->join('profesor as p', 'p.id_profesor', '=', 'mcg.id_profesor')
            ->select(
                'mcg.id_materia',
                'mcg.id_gestion',
                'mcg.id_curso',
                'm.nombre as materia',
                'c.nombre as curso',
                'g.nombre as gestion',
                DB::raw("CONCAT_WS(' ', p.nombre, p.ap_paterno, p.ap_materno) as docente")
            )
            ->orderByDesc('g.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('m.nombre')
            ->get();
    }

    private function parseAsignacion(string $asignacion): array
    {
        return array_map('intval', explode('|', $asignacion));
    }

    private function idMatriculaDelAlumno(int $idAlumno, int $idGestion, int $idCurso): ?int
    {
        return DB::table('inscripcion as i')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
            ->where('i.id_alumno', $idAlumno)
            ->where('icg.id_gestion', $idGestion)
            ->where('icg.id_curso', $idCurso)
            ->value('m.id_matricula');
    }
}
