<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NotaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $idMateria = $request->integer('id_materia') ?: null;
        $idTrimestre = $request->integer('id_trimestre') ?: null;

        $query = $this->consultaNotasBase($request)
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(m.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(c.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(g.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(n.descripcion) LIKE LOWER(?)', [$like]);
                });
            })
            ->when($idGestion, fn ($query) => $query->where('n.id_gestion', $idGestion))
            ->when($idCurso, fn ($query) => $query->where('n.id_curso', $idCurso))
            ->when($idMateria, fn ($query) => $query->where('n.id_materia', $idMateria))
            ->when($idTrimestre, fn ($query) => $query->where('n.id_trimestre', $idTrimestre));

        $notas = $query->paginate(15)->appends($request->except('page'));
        $filtros = $this->catalogos();

        return view('admin.notas.index', array_merge($filtros, compact(
            'notas',
            'search',
            'idGestion',
            'idCurso',
            'idMateria',
            'idTrimestre'
        )));
    }

    public function create(Request $request)
    {
        return view('admin.notas.create', array_merge(
            $this->catalogos(),
            ['asignaciones' => $this->asignacionesDisponibles($request)]
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validarNota($request, true);
        [$idMateria, $idGestion, $idCurso] = $this->parseAsignacion($data['asignacion']);

        $this->validarAsignacionDisponible($request, $idMateria, $idGestion, $idCurso);
        $this->validarAlumnoMatriculado((int) $data['id_alumno'], $idGestion, $idCurso);

        $existe = DB::table('nota')
            ->where('id_alumno', $data['id_alumno'])
            ->where('id_materia', $idMateria)
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->where('id_trimestre', $data['id_trimestre'])
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'id_alumno' => 'Ya existe una nota para este estudiante, materia, trimestre, curso y gestion.',
            ]);
        }

        DB::table('nota')->insert([
            'id_alumno' => $data['id_alumno'],
            'id_materia' => $idMateria,
            'id_gestion' => $idGestion,
            'id_curso' => $idCurso,
            'id_trimestre' => $data['id_trimestre'],
            'ser' => $data['ser'],
            'saber' => $data['saber'],
            'hacer' => $data['hacer'],
            'autoevaluacion' => $data['autoevaluacion'],
            'promediofinal' => $this->calcularPromedio($data),
            'descripcion' => $data['descripcion'] ?? '',
        ]);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'Nota registrada exitosamente')
            ->with('icono', 'success');
    }

    public function edit(Request $request, int $idAlumno, int $idMateria, int $idGestion, int $idCurso, int $idTrimestre)
    {
        $nota = $this->buscarNota($request, $idAlumno, $idMateria, $idGestion, $idCurso, $idTrimestre);

        return view('admin.notas.edit', compact('nota'));
    }

    public function update(Request $request, int $idAlumno, int $idMateria, int $idGestion, int $idCurso, int $idTrimestre)
    {
        $this->buscarNota($request, $idAlumno, $idMateria, $idGestion, $idCurso, $idTrimestre);

        $data = $this->validarNota($request, false);

        DB::table('nota')
            ->where('id_alumno', $idAlumno)
            ->where('id_materia', $idMateria)
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->where('id_trimestre', $idTrimestre)
            ->update([
                'ser' => $data['ser'],
                'saber' => $data['saber'],
                'hacer' => $data['hacer'],
                'autoevaluacion' => $data['autoevaluacion'],
                'promediofinal' => $this->calcularPromedio($data),
                'descripcion' => $data['descripcion'] ?? '',
            ]);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'Nota actualizada exitosamente')
            ->with('icono', 'success');
    }

    public function destroy(Request $request, int $idAlumno, int $idMateria, int $idGestion, int $idCurso, int $idTrimestre)
    {
        $this->buscarNota($request, $idAlumno, $idMateria, $idGestion, $idCurso, $idTrimestre);

        DB::table('nota')
            ->where('id_alumno', $idAlumno)
            ->where('id_materia', $idMateria)
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->where('id_trimestre', $idTrimestre)
            ->delete();

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'Nota eliminada exitosamente')
            ->with('icono', 'success');
    }

    private function consultaNotasBase(Request $request)
    {
        return DB::table('nota as n')
            ->join('alumno as a', 'a.id_alumno', '=', 'n.id_alumno')
            ->join('materia as m', 'm.id_materia', '=', 'n.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'n.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'n.id_curso')
            ->join('trimestre as t', 't.id_trimestre', '=', 'n.id_trimestre')
            ->join('materia_curso_gestion as mcg', function ($join) {
                $join->on('mcg.id_materia', '=', 'n.id_materia')
                    ->on('mcg.id_gestion', '=', 'n.id_gestion')
                    ->on('mcg.id_curso', '=', 'n.id_curso');
            })
            ->leftJoin('profesor as p', 'p.id_profesor', '=', 'mcg.id_profesor')
            ->when($this->esProfesor($request), function ($query) use ($request) {
                $idProfesor = $this->idProfesorAutenticado($request);

                $query->where('mcg.id_profesor', $idProfesor ?? 0);
            })
            ->select(
                'n.id_alumno',
                'n.id_materia',
                'n.id_gestion',
                'n.id_curso',
                'n.id_trimestre',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'm.nombre as materia',
                'g.nombre as gestion',
                'c.nombre as curso',
                DB::raw("CONCAT('Trimestre ', t.id_trimestre) as trimestre"),
                DB::raw("CONCAT_WS(' ', p.nombre, p.ap_paterno, p.ap_materno) as docente"),
                'n.ser',
                'n.saber',
                'n.hacer',
                'n.autoevaluacion',
                'n.promediofinal',
                'n.descripcion'
            )
            ->orderByDesc('n.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('m.nombre')
            ->orderBy('n.id_trimestre')
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres');
    }

    private function buscarNota(Request $request, int $idAlumno, int $idMateria, int $idGestion, int $idCurso, int $idTrimestre)
    {
        return $this->consultaNotasBase($request)
            ->where('n.id_alumno', $idAlumno)
            ->where('n.id_materia', $idMateria)
            ->where('n.id_gestion', $idGestion)
            ->where('n.id_curso', $idCurso)
            ->where('n.id_trimestre', $idTrimestre)
            ->firstOrFail();
    }

    private function validarNota(Request $request, bool $incluyeRegistro): array
    {
        $reglas = [
            'ser' => ['required', 'integer', 'min:0', 'max:100'],
            'saber' => ['required', 'integer', 'min:0', 'max:100'],
            'hacer' => ['required', 'integer', 'min:0', 'max:100'],
            'autoevaluacion' => ['required', 'integer', 'min:0', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:100'],
        ];

        if ($incluyeRegistro) {
            $reglas = array_merge([
                'id_alumno' => ['required', 'integer', 'exists:alumno,id_alumno'],
                'asignacion' => ['required', 'regex:/^\d+\|\d+\|\d+$/'],
                'id_trimestre' => ['required', 'integer', 'exists:trimestre,id_trimestre'],
            ], $reglas);
        }

        return $request->validate($reglas, [
            'id_alumno.required' => 'Seleccione un estudiante.',
            'asignacion.required' => 'Seleccione materia, curso y gestion.',
            'asignacion.regex' => 'La asignacion seleccionada no es valida.',
            'id_trimestre.required' => 'Seleccione un trimestre.',
            '*.required' => 'Este campo es obligatorio.',
            '*.integer' => 'Ingrese un valor numerico entero.',
            '*.min' => 'La calificacion no puede ser menor a 0.',
            '*.max' => 'La calificacion no puede ser mayor a 100.',
        ]);
    }

    private function validarAsignacionDisponible(Request $request, int $idMateria, int $idGestion, int $idCurso): void
    {
        $existe = $this->asignacionesDisponibles($request)
            ->contains(function ($asignacion) use ($idMateria, $idGestion, $idCurso) {
                return (int) $asignacion->id_materia === $idMateria
                    && (int) $asignacion->id_gestion === $idGestion
                    && (int) $asignacion->id_curso === $idCurso;
            });

        if (! $existe) {
            throw ValidationException::withMessages([
                'asignacion' => 'La asignacion docente, materia, curso y gestion no existe o no esta disponible para el usuario.',
            ]);
        }
    }

    private function validarAlumnoMatriculado(int $idAlumno, int $idGestion, int $idCurso): void
    {
        $matriculado = DB::table('inscripcion as i')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->where('i.id_alumno', $idAlumno)
            ->where('icg.id_gestion', $idGestion)
            ->where('icg.id_curso', $idCurso)
            ->exists();

        if (! $matriculado) {
            throw ValidationException::withMessages([
                'id_alumno' => 'El estudiante no esta matriculado en el curso y gestion seleccionados.',
            ]);
        }
    }

    private function asignacionesDisponibles(Request $request)
    {
        return DB::table('materia_curso_gestion as mcg')
            ->join('materia as m', 'm.id_materia', '=', 'mcg.id_materia')
            ->join('curso as c', 'c.id_curso', '=', 'mcg.id_curso')
            ->join('gestion as g', 'g.id_gestion', '=', 'mcg.id_gestion')
            ->join('profesor as p', 'p.id_profesor', '=', 'mcg.id_profesor')
            ->when($this->esProfesor($request), function ($query) use ($request) {
                $query->where('mcg.id_profesor', $this->idProfesorAutenticado($request) ?? 0);
            })
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

    private function catalogos(): array
    {
        return [
            'alumnos' => DB::table('alumno')
                ->select('id_alumno', DB::raw("CONCAT_WS(' ', nombres, ap_paterno, ap_materno) as nombre_completo"))
                ->orderBy('ap_paterno')
                ->orderBy('ap_materno')
                ->orderBy('nombres')
                ->get(),
            'gestiones' => DB::table('gestion')->orderByDesc('id_gestion')->get(),
            'cursos' => DB::table('curso')->orderBy('nombre')->get(),
            'materias' => DB::table('materia')->orderBy('nombre')->get(),
            'trimestres' => DB::table('trimestre')->orderBy('id_trimestre')->get(),
        ];
    }

    private function parseAsignacion(string $asignacion): array
    {
        return array_map('intval', explode('|', $asignacion));
    }

    private function calcularPromedio(array $data): float
    {
        return round(((int) $data['ser'] + (int) $data['saber'] + (int) $data['hacer'] + (int) $data['autoevaluacion']) / 4, 2);
    }

    private function esProfesor(Request $request): bool
    {
        return (int) $request->user()?->id_rol === Rol::PROFESOR->value;
    }

    private function idProfesorAutenticado(Request $request): ?int
    {
        if (! $request->user()) {
            return null;
        }

        return DB::table('profesor')
            ->where('id_user', $request->user()->id_user)
            ->value('id_profesor');
    }
}
