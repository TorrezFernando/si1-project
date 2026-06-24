<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HorarioController extends Controller
{
    private const DIAS = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dia = $request->input('dia');

        $horarios = $this->consultaHorariosBase()
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($q) use ($like) {
                    $q->whereRaw('LOWER(m.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(c.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(g.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw("LOWER(CONCAT_WS(' ', p.nombre, p.ap_paterno, p.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(COALESCE(a.nombre, a.tipo)) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(pa.descripcion) LIKE LOWER(?)', [$like]);
                });
            })
            ->when(in_array($dia, self::DIAS, true), fn ($query) => $query->where('h.dia', $dia))
            ->orderByDesc('mcgp.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('pa.descripcion')
            ->orderByRaw("FIELD(h.dia, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes')")
            ->orderBy('h.hora_inicio')
            ->paginate(15)->appends($request->except('page'));

        $dias = self::DIAS;

        return view('admin.horarios.index', compact('horarios', 'search', 'dia', 'dias'));
    }

    public function create()
    {
        return view('admin.horarios.create', array_merge($this->catalogos(), [
            'dias' => self::DIAS,
        ]));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        [$idMateria, $idGestion, $idCurso] = $this->parseAsignacion($data['asignacion']);

        $this->validarAsignacionSinHorario($idMateria, $idGestion, $idCurso, (int) $data['id_paralelo']);
        $this->validarAulaDisponible((int) $data['id_aula']);
        $this->validarConflictos($idMateria, $idGestion, $idCurso, (int) $data['id_paralelo'], (int) $data['id_aula'], $data['dia'], $data['hora_inicio'], $data['hora_fin']);

        DB::transaction(function () use ($data, $idMateria, $idGestion, $idCurso) {
            $idHorario = DB::table('horario')->insertGetId([
                'dia' => $data['dia'],
                'hora_inicio' => $data['hora_inicio'],
                'hora_fin' => $data['hora_fin'],
            ]);

            DB::table('materia_curso_gestion_paralelo')->insert([
                'id_materia' => $idMateria,
                'id_gestion' => $idGestion,
                'id_curso' => $idCurso,
                'id_paralelo' => $data['id_paralelo'],
                'id_horario' => $idHorario,
                'id_aula' => $data['id_aula'],
            ]);
        });

        return redirect()->route('admin.horarios.index')
            ->with('mensaje', 'Horario registrado exitosamente')
            ->with('icono', 'success');
    }

    public function edit(int $idMateria, int $idGestion, int $idCurso, int $idParalelo)
    {
        $horario = $this->buscarHorario($idMateria, $idGestion, $idCurso, $idParalelo);

        return view('admin.horarios.edit', array_merge($this->catalogos(), [
            'dias' => self::DIAS,
            'horario' => $horario,
        ]));
    }

    public function update(Request $request, int $idMateria, int $idGestion, int $idCurso, int $idParalelo)
    {
        $horario = $this->buscarHorario($idMateria, $idGestion, $idCurso, $idParalelo);
        $data = $this->validar($request, false);

        $this->validarAulaDisponible((int) $data['id_aula']);
        $this->validarConflictos($idMateria, $idGestion, $idCurso, $idParalelo, (int) $data['id_aula'], $data['dia'], $data['hora_inicio'], $data['hora_fin'], [
            'id_materia' => $idMateria,
            'id_gestion' => $idGestion,
            'id_curso' => $idCurso,
            'id_paralelo' => $idParalelo,
        ]);

        DB::transaction(function () use ($data, $horario, $idMateria, $idGestion, $idCurso, $idParalelo) {
            $usosHorario = DB::table('materia_curso_gestion_paralelo')
                ->where('id_horario', $horario->id_horario)
                ->count();

            if ($usosHorario > 1) {
                $idHorario = DB::table('horario')->insertGetId([
                    'dia' => $data['dia'],
                    'hora_inicio' => $data['hora_inicio'],
                    'hora_fin' => $data['hora_fin'],
                ]);
            } else {
                $idHorario = $horario->id_horario;
                DB::table('horario')
                    ->where('id_horario', $idHorario)
                    ->update([
                        'dia' => $data['dia'],
                        'hora_inicio' => $data['hora_inicio'],
                        'hora_fin' => $data['hora_fin'],
                    ]);
            }

            DB::table('materia_curso_gestion_paralelo')
                ->where('id_materia', $idMateria)
                ->where('id_gestion', $idGestion)
                ->where('id_curso', $idCurso)
                ->where('id_paralelo', $idParalelo)
                ->update([
                    'id_horario' => $idHorario,
                    'id_aula' => $data['id_aula'],
                ]);
        });

        return redirect()->route('admin.horarios.index')
            ->with('mensaje', 'Horario actualizado exitosamente')
            ->with('icono', 'success');
    }

    public function destroy(int $idMateria, int $idGestion, int $idCurso, int $idParalelo)
    {
        $horario = $this->buscarHorario($idMateria, $idGestion, $idCurso, $idParalelo);

        DB::transaction(function () use ($horario, $idMateria, $idGestion, $idCurso, $idParalelo) {
            DB::table('materia_curso_gestion_paralelo')
                ->where('id_materia', $idMateria)
                ->where('id_gestion', $idGestion)
                ->where('id_curso', $idCurso)
                ->where('id_paralelo', $idParalelo)
                ->delete();

            $tieneMasUsos = DB::table('materia_curso_gestion_paralelo')
                ->where('id_horario', $horario->id_horario)
                ->exists();

            if (! $tieneMasUsos) {
                DB::table('horario')->where('id_horario', $horario->id_horario)->delete();
            }
        });

        return redirect()->route('admin.horarios.index')
            ->with('mensaje', 'Horario eliminado exitosamente')
            ->with('icono', 'success');
    }

    private function consultaHorariosBase()
    {
        return DB::table('materia_curso_gestion_paralelo as mcgp')
            ->join('horario as h', 'h.id_horario', '=', 'mcgp.id_horario')
            ->join('materia_curso_gestion as mcg', function ($join) {
                $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
                    ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
                    ->on('mcg.id_curso', '=', 'mcgp.id_curso');
            })
            ->join('materia as m', 'm.id_materia', '=', 'mcgp.id_materia')
            ->join('gestion as g', 'g.id_gestion', '=', 'mcgp.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'mcgp.id_curso')
            ->join('paralelo as pa', 'pa.id_paralelo', '=', 'mcgp.id_paralelo')
            ->join('profesor as p', 'p.id_profesor', '=', 'mcg.id_profesor')
            ->join('aula as a', 'a.id_aula', '=', 'mcgp.id_aula')
            ->select(
                'mcgp.id_materia',
                'mcgp.id_gestion',
                'mcgp.id_curso',
                'mcgp.id_paralelo',
                'h.id_horario',
                'h.dia',
                'h.hora_inicio',
                'h.hora_fin',
                'm.nombre as materia',
                'g.nombre as gestion',
                'c.nombre as curso',
                'pa.descripcion as paralelo',
                DB::raw("CONCAT_WS(' ', p.nombre, p.ap_paterno, p.ap_materno) as docente"),
                DB::raw('COALESCE(a.nombre, a.tipo) as aula'),
                'a.id_aula'
            );
    }

    private function buscarHorario(int $idMateria, int $idGestion, int $idCurso, int $idParalelo)
    {
        return $this->consultaHorariosBase()
            ->where('mcgp.id_materia', $idMateria)
            ->where('mcgp.id_gestion', $idGestion)
            ->where('mcgp.id_curso', $idCurso)
            ->where('mcgp.id_paralelo', $idParalelo)
            ->firstOrFail();
    }

    private function validar(Request $request, bool $incluyeAsignacion = true): array
    {
        $reglas = [
            'id_paralelo' => ['required', 'integer', 'exists:paralelo,id_paralelo'],
            'dia' => ['required', Rule::in(self::DIAS)],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'id_aula' => ['required', 'integer', 'exists:aula,id_aula'],
        ];

        if ($incluyeAsignacion) {
            $reglas = array_merge([
                'asignacion' => ['required', 'regex:/^\d+\|\d+\|\d+$/'],
            ], $reglas);
        }

        return $request->validate($reglas, [
            'asignacion.required' => 'Seleccione docente, materia, curso y gestion.',
            'asignacion.regex' => 'La asignacion seleccionada no es valida.',
            'id_paralelo.required' => 'Seleccione un paralelo.',
            'dia.required' => 'Seleccione un dia.',
            'dia.in' => 'El dia seleccionado no es valido.',
            'hora_inicio.required' => 'Ingrese la hora de inicio.',
            'hora_inicio.date_format' => 'La hora de inicio no es valida.',
            'hora_fin.required' => 'Ingrese la hora de fin.',
            'hora_fin.date_format' => 'La hora de fin no es valida.',
            'hora_fin.after' => 'La hora de fin debe ser mayor que la hora de inicio.',
            'id_aula.required' => 'Seleccione un aula.',
        ]);
    }

    private function validarAsignacionSinHorario(int $idMateria, int $idGestion, int $idCurso, int $idParalelo): void
    {
        $existe = DB::table('materia_curso_gestion_paralelo')
            ->where('id_materia', $idMateria)
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->where('id_paralelo', $idParalelo)
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'asignacion' => 'Ya existe un horario para esta materia, curso, gestion y paralelo.',
            ]);
        }
    }

    private function validarAulaDisponible(int $idAula): void
    {
        $aula = DB::table('aula')->where('id_aula', $idAula)->first();

        if ($aula && isset($aula->estado) && $aula->estado !== 'Activo') {
            throw ValidationException::withMessages([
                'id_aula' => 'No se puede asignar un aula inactiva o en mantenimiento.',
            ]);
        }
    }

    private function validarConflictos(
        int $idMateria,
        int $idGestion,
        int $idCurso,
        int $idParalelo,
        int $idAula,
        string $dia,
        string $horaInicio,
        string $horaFin,
        ?array $excluir = null
    ): void {
        $idProfesor = DB::table('materia_curso_gestion')
            ->where('id_materia', $idMateria)
            ->where('id_gestion', $idGestion)
            ->where('id_curso', $idCurso)
            ->value('id_profesor');

        $conflictos = $this->consultaConflictosBase($dia, $horaInicio, $horaFin, $excluir);

        if ((clone $conflictos)->where('mcgp.id_aula', $idAula)->exists()) {
            throw ValidationException::withMessages([
                'id_aula' => 'El aula ya esta ocupada en ese dia y horario.',
            ]);
        }

        if ($idProfesor && (clone $conflictos)->where('mcg.id_profesor', $idProfesor)->exists()) {
            throw ValidationException::withMessages([
                'asignacion' => 'El docente ya tiene una materia asignada en ese dia y horario.',
            ]);
        }

        if ((clone $conflictos)
            ->where('mcgp.id_gestion', $idGestion)
            ->where('mcgp.id_curso', $idCurso)
            ->where('mcgp.id_paralelo', $idParalelo)
            ->exists()) {
            throw ValidationException::withMessages([
                'id_paralelo' => 'El curso y paralelo ya tienen una clase en ese dia y horario.',
            ]);
        }
    }

    private function consultaConflictosBase(string $dia, string $horaInicio, string $horaFin, ?array $excluir)
    {
        $query = DB::table('materia_curso_gestion_paralelo as mcgp')
            ->join('horario as h', 'h.id_horario', '=', 'mcgp.id_horario')
            ->join('materia_curso_gestion as mcg', function ($join) {
                $join->on('mcg.id_materia', '=', 'mcgp.id_materia')
                    ->on('mcg.id_gestion', '=', 'mcgp.id_gestion')
                    ->on('mcg.id_curso', '=', 'mcgp.id_curso');
            })
            ->where('h.dia', $dia)
            ->where('h.hora_inicio', '<', $horaFin)
            ->where('h.hora_fin', '>', $horaInicio);

        if ($excluir) {
            $query->where(function ($q) use ($excluir) {
                $q->where('mcgp.id_materia', '!=', $excluir['id_materia'])
                    ->orWhere('mcgp.id_gestion', '!=', $excluir['id_gestion'])
                    ->orWhere('mcgp.id_curso', '!=', $excluir['id_curso'])
                    ->orWhere('mcgp.id_paralelo', '!=', $excluir['id_paralelo']);
            });
        }

        return $query;
    }

    private function catalogos(): array
    {
        return [
            'asignaciones' => DB::table('materia_curso_gestion as mcg')
                ->join('materia as m', 'm.id_materia', '=', 'mcg.id_materia')
                ->join('gestion as g', 'g.id_gestion', '=', 'mcg.id_gestion')
                ->join('curso as c', 'c.id_curso', '=', 'mcg.id_curso')
                ->join('profesor as p', 'p.id_profesor', '=', 'mcg.id_profesor')
                ->select(
                    'mcg.id_materia',
                    'mcg.id_gestion',
                    'mcg.id_curso',
                    'm.nombre as materia',
                    'g.nombre as gestion',
                    'c.nombre as curso',
                    DB::raw("CONCAT_WS(' ', p.nombre, p.ap_paterno, p.ap_materno) as docente")
                )
                ->orderByDesc('g.id_gestion')
                ->orderBy('c.nombre')
                ->orderBy('m.nombre')
                ->get(),
            'paralelos' => DB::table('paralelo')->orderBy('descripcion')->get(),
            'aulas' => DB::table('aula')
                ->select('id_aula', DB::raw('COALESCE(nombre, tipo) as nombre'), 'tipo', 'estado')
                ->orderBy('nombre')
                ->get(),
        ];
    }

    private function parseAsignacion(string $asignacion): array
    {
        return array_map('intval', explode('|', $asignacion));
    }
}
