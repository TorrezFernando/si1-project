<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MatriculaController extends Controller
{
    private const ESTADOS = ['Pendiente', 'Activa', 'Retirada', 'Aprobada', 'Reprobada'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $estado = $request->input('estado');

        $matriculas = $this->consultaBase()
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(a.ci) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(c.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(g.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw("LOWER(CONCAT_WS(' ', ap.nombres, ap.ap_paterno, ap.ap_materno)) LIKE LOWER(?)", [$like]);
                });
            })
            ->when($idGestion, fn ($query) => $query->where('icg.id_gestion', $idGestion))
            ->when($idCurso, fn ($query) => $query->where('icg.id_curso', $idCurso))
            ->when($estado, fn ($query) => $query->where('m.estado', $estado))
            ->paginate(15)->appends($request->except('page'));

        return view('admin.matriculas.index', array_merge($this->catalogos(), compact(
            'matriculas',
            'search',
            'idGestion',
            'idCurso',
            'estado'
        )));
    }

    public function create()
    {
        return view('admin.matriculas.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $this->validarTutorDelAlumno((int) $data['id_alumno'], (int) $data['id_apoderado']);
        $this->validarGestionActiva((int) $data['id_gestion']);
        $this->validarMatriculaDuplicada((int) $data['id_alumno'], (int) $data['id_gestion'], (int) $data['id_curso']);
        $this->validarCupoDisponible((int) $data['id_gestion'], (int) $data['id_curso']);

        DB::transaction(function () use ($data, $request) {
            $idSecretaria = $this->idSecretariaAutenticada($request);
            $this->asegurarCursoGestion((int) $data['id_gestion'], (int) $data['id_curso']);

            $matricula = [
                'monto' => $data['monto'],
                'fecha' => $data['fecha'],
            ];

            if (Schema::hasColumn('matricula', 'estado')) {
                $matricula['estado'] = $data['estado'];
            }

            $idMatricula = DB::table('matricula')->insertGetId($matricula);

            $inscripcion = [
                'fecha' => $data['fecha'],
                'id_alumno' => $data['id_alumno'],
                'id_secretaria' => $idSecretaria,
                'id_matricula' => $idMatricula,
            ];

            if (Schema::hasColumn('inscripcion', 'id_apoderado')) {
                $inscripcion['id_apoderado'] = $data['id_apoderado'];
            }

            $idInscripcion = DB::table('inscripcion')->insertGetId($inscripcion);

            DB::table('inscripcion_curso_gestion')->insert([
                'id_inscripcion' => $idInscripcion,
                'id_gestion' => $data['id_gestion'],
                'id_curso' => $data['id_curso'],
                'paralelo' => $this->paraleloCurso((int) $data['id_curso']),
            ]);
        });

        return redirect()->route('admin.matriculas.index')
            ->with('mensaje', 'Matricula registrada exitosamente')
            ->with('icono', 'success');
    }

    public function edit(int $idInscripcion)
    {
        $matricula = $this->buscarMatricula($idInscripcion);

        return view('admin.matriculas.edit', array_merge($this->catalogos(), compact('matricula')));
    }

    public function update(Request $request, int $idInscripcion)
    {
        $matricula = $this->buscarMatricula($idInscripcion);
        $data = $this->validar($request, $idInscripcion);

        $this->validarTutorDelAlumno((int) $data['id_alumno'], (int) $data['id_apoderado']);
        $this->validarGestionActiva((int) $data['id_gestion']);
        $this->validarMatriculaDuplicada((int) $data['id_alumno'], (int) $data['id_gestion'], (int) $data['id_curso'], $idInscripcion);

        if ((int) $matricula->id_curso !== (int) $data['id_curso'] || (int) $matricula->id_gestion !== (int) $data['id_gestion']) {
            $this->validarCupoDisponible((int) $data['id_gestion'], (int) $data['id_curso']);
        }

        DB::transaction(function () use ($data, $matricula, $idInscripcion, $request) {
            $this->asegurarCursoGestion((int) $data['id_gestion'], (int) $data['id_curso']);

            $matriculaData = [
                'monto' => $data['monto'],
                'fecha' => $data['fecha'],
            ];

            if (Schema::hasColumn('matricula', 'estado')) {
                $matriculaData['estado'] = $data['estado'];
            }

            DB::table('matricula')
                ->where('id_matricula', $matricula->id_matricula)
                ->update($matriculaData);

            $inscripcionData = [
                'fecha' => $data['fecha'],
                'id_alumno' => $data['id_alumno'],
                'id_secretaria' => $this->idSecretariaAutenticada($request),
            ];

            if (Schema::hasColumn('inscripcion', 'id_apoderado')) {
                $inscripcionData['id_apoderado'] = $data['id_apoderado'];
            }

            DB::table('inscripcion')
                ->where('id_inscripcion', $idInscripcion)
                ->update($inscripcionData);

            DB::table('inscripcion_curso_gestion')
                ->where('id_inscripcion', $idInscripcion)
                ->update([
                    'id_gestion' => $data['id_gestion'],
                    'id_curso' => $data['id_curso'],
                    'paralelo' => $this->paraleloCurso((int) $data['id_curso']),
                ]);
        });

        return redirect()->route('admin.matriculas.index')
            ->with('mensaje', 'Matricula actualizada exitosamente')
            ->with('icono', 'success');
    }

    public function cambiarEstado(Request $request, int $idInscripcion)
    {
        if (! Schema::hasColumn('matricula', 'estado')) {
            return redirect()->route('admin.matriculas.index')
                ->with('mensaje', 'La columna estado no existe. Ejecute las migraciones.')
                ->with('icono', 'error');
        }

        $matricula = $this->buscarMatricula($idInscripcion);
        $data = $request->validate([
            'estado' => ['required', Rule::in(self::ESTADOS)],
        ]);

        DB::table('matricula')
            ->where('id_matricula', $matricula->id_matricula)
            ->update(['estado' => $data['estado']]);

        return redirect()->route('admin.matriculas.index')
            ->with('mensaje', 'Estado de matricula actualizado')
            ->with('icono', 'success');
    }

    public function destroy(int $idInscripcion)
    {
        $matricula = $this->buscarMatricula($idInscripcion);

        if ($this->tieneRegistrosAsociados($matricula)) {
            return redirect()->route('admin.matriculas.index')
                ->with('mensaje', 'No se puede eliminar. La matricula tiene notas, asistencias o pagos registrados.')
                ->with('icono', 'error');
        }

        DB::transaction(function () use ($matricula, $idInscripcion) {
            DB::table('inscripcion_curso_gestion')->where('id_inscripcion', $idInscripcion)->delete();
            DB::table('inscripcion')->where('id_inscripcion', $idInscripcion)->delete();
            DB::table('matricula')->where('id_matricula', $matricula->id_matricula)->delete();
        });

        return redirect()->route('admin.matriculas.index')
            ->with('mensaje', 'Matricula eliminada exitosamente')
            ->with('icono', 'success');
    }

    private function consultaBase()
    {
        return DB::table('inscripcion as i')
            ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->leftJoin('apoderado as ap', function ($join) {
                if (Schema::hasColumn('inscripcion', 'id_apoderado')) {
                    $join->on('ap.id_apoderado', '=', 'i.id_apoderado');
                } else {
                    $join->whereRaw('1 = 0');
                }
            })
            ->leftJoin('secretaria as s', 's.id_secretaria', '=', 'i.id_secretaria')
            ->select(
                'i.id_inscripcion',
                'i.id_alumno',
                'i.id_secretaria',
                'i.id_matricula',
                'icg.id_gestion',
                'icg.id_curso',
                'icg.paralelo',
                'm.monto',
                'm.fecha',
                DB::raw(Schema::hasColumn('matricula', 'estado') ? 'm.estado' : "'Pendiente' as estado"),
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'c.nombre as curso',
                'g.nombre as gestion',
                DB::raw("CONCAT_WS(' ', ap.nombres, ap.ap_paterno, ap.ap_materno) as apoderado"),
                DB::raw(Schema::hasColumn('inscripcion', 'id_apoderado') ? 'i.id_apoderado' : 'NULL as id_apoderado'),
                DB::raw("CONCAT_WS(' ', s.nombre, s.ap_paterno, s.ap_materno) as secretaria")
            )
            ->orderByDesc('g.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres');
    }

    private function buscarMatricula(int $idInscripcion)
    {
        return $this->consultaBase()
            ->where('i.id_inscripcion', $idInscripcion)
            ->firstOrFail();
    }

    private function validar(Request $request, ?int $idInscripcion = null): array
    {
        return $request->validate([
            'id_alumno' => ['required', 'integer', 'exists:alumno,id_alumno'],
            'id_apoderado' => ['required', 'integer', 'exists:apoderado,id_apoderado'],
            'id_gestion' => ['required', 'integer', 'exists:gestion,id_gestion'],
            'id_curso' => ['required', 'integer', 'exists:curso,id_curso'],
            'monto' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', Rule::in(self::ESTADOS)],
        ], [
            'id_alumno.required' => 'Seleccione un estudiante.',
            'id_apoderado.required' => 'Seleccione el tutor del estudiante.',
            'id_gestion.required' => 'Seleccione una gestion.',
            'id_curso.required' => 'Seleccione un curso.',
            '*.required' => 'Este campo es obligatorio.',
        ]);
    }

    private function catalogos(): array
    {
        return [
            'alumnos' => DB::table('alumno')
                ->select('id_alumno', 'ci', DB::raw("CONCAT_WS(' ', nombres, ap_paterno, ap_materno) as nombre_completo"))
                ->orderBy('ap_paterno')
                ->orderBy('ap_materno')
                ->orderBy('nombres')
                ->get(),
            'apoderados' => DB::table('apoderado')
                ->select('id_apoderado', DB::raw("CONCAT_WS(' ', nombres, ap_paterno, ap_materno) as nombre_completo"))
                ->orderBy('ap_paterno')
                ->orderBy('ap_materno')
                ->orderBy('nombres')
                ->get(),
            'parentescos' => DB::table('parentesco')->get(),
            'gestiones' => DB::table('gestion')->orderByDesc('id_gestion')->get(),
            'gestionActiva' => DB::table('gestion')->where('activo', true)->orderByDesc('id_gestion')->first(),
            'cursos' => DB::table('curso')->orderBy('nombre')->get(),
            'estados' => self::ESTADOS,
        ];
    }

    private function validarTutorDelAlumno(int $idAlumno, int $idApoderado): void
    {
        $existe = DB::table('parentesco')
            ->where('id_alumno', $idAlumno)
            ->where('id_apoderado', $idApoderado)
            ->exists();

        if (! $existe) {
            throw ValidationException::withMessages([
                'id_apoderado' => 'El tutor seleccionado no esta vinculado con el estudiante.',
            ]);
        }
    }

    private function validarMatriculaDuplicada(int $idAlumno, int $idGestion, int $idCurso, ?int $exceptoIdInscripcion = null): void
    {
        $existe = DB::table('inscripcion as i')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->where('i.id_alumno', $idAlumno)
            ->where('icg.id_gestion', $idGestion)
            ->where('icg.id_curso', $idCurso)
            ->when($exceptoIdInscripcion, fn ($query) => $query->where('i.id_inscripcion', '!=', $exceptoIdInscripcion))
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'id_alumno' => 'El estudiante ya esta matriculado en este curso y gestion.',
            ]);
        }
    }

    private function validarGestionActiva(int $idGestion): void
    {
        $activa = DB::table('gestion')
            ->where('id_gestion', $idGestion)
            ->where('activo', true)
            ->exists();

        if (! $activa) {
            throw ValidationException::withMessages([
                'id_gestion' => 'La gestion seleccionada no esta activa.',
            ]);
        }
    }

    private function validarCupoDisponible(int $idGestion, int $idCurso): void
    {
        $inscritos = DB::table('inscripcion_curso_gestion')
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->count();

        $cupoMaximo = 35;

        if ($inscritos >= $cupoMaximo) {
            throw ValidationException::withMessages([
                'id_curso' => 'Curso sin cupo disponible.',
            ]);
        }
    }

    private function asegurarCursoGestion(int $idGestion, int $idCurso): void
    {
        $existe = DB::table('curso_gestion')
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->exists();

        if (! $existe) {
            DB::table('curso_gestion')->insert([
                'id_gestion' => $idGestion,
                'id_curso' => $idCurso,
            ]);
        }
    }

    private function paraleloCurso(int $idCurso): string
    {
        $curso = DB::table('curso')->where('id_curso', $idCurso)->first();

        if ($curso && isset($curso->id_paralelo)) {
            $paralelo = DB::table('paralelo')->where('id_paralelo', $curso->id_paralelo)->value('descripcion');

            if ($paralelo) {
                return (string) $paralelo;
            }
        }

        return 'A';
    }

    private function idSecretariaAutenticada(Request $request): int
    {
        if ((int) $request->user()?->id_rol === Rol::SECRETARIA->value) {
            $id = DB::table('secretaria')->where('id_user', $request->user()->id_user)->value('id_secretaria');

            if ($id) {
                return (int) $id;
            }
        }

        $id = DB::table('secretaria')->orderBy('id_secretaria')->value('id_secretaria');

        if (! $id) {
            throw ValidationException::withMessages([
                'id_secretaria' => 'Debe existir al menos un personal administrativo registrado.',
            ]);
        }

        return (int) $id;
    }

    private function tieneRegistrosAsociados(object $matricula): bool
    {
        $tieneAsistencia = Schema::hasTable('asistencia')
            && DB::table('asistencia')->where('id_matricula', $matricula->id_matricula)->exists();

        $tieneNotas = Schema::hasTable('nota')
            && DB::table('nota')
                ->where('id_alumno', $matricula->id_alumno)
                ->where('id_gestion', $matricula->id_gestion)
                ->where('id_curso', $matricula->id_curso)
                ->exists();

        $tienePagos = Schema::hasTable('pago_mensual')
            && DB::table('pago_mensual')
                ->where('id_alumno', $matricula->id_alumno)
                ->where('id_gestion', $matricula->id_gestion)
                ->where('id_curso', $matricula->id_curso)
                ->exists();

        return $tieneAsistencia || $tieneNotas || $tienePagos;
    }
}
