<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MensualidadController extends Controller
{
    private const MESES = [
        'Febrero',
        'Marzo',
        'Abril',
        'Mayo',
        'Junio',
        'Julio',
        'Agosto',
        'Septiembre',
        'Octubre',
        'Noviembre',
    ];

    private const ESTADOS = ['Pendiente', 'Pagado', 'Vencido', 'Anulado'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $idGestion = $request->integer('id_gestion') ?: null;
        $idCurso = $request->integer('id_curso') ?: null;
        $mes = $request->input('mes');
        $estado = $request->input('estado');

        $totalPendiente = $this->consultaBase()
            ->when($search, function ($query) use ($search) { $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(a.ci) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(c.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(g.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(pm.mes) LIKE LOWER(?)', [$like]);
                });
            })
            ->when($idGestion, fn ($query) => $query->where('pm.id_gestion', $idGestion))
            ->when($idCurso, fn ($query) => $query->where('pm.id_curso', $idCurso))
            ->when($mes, fn ($query) => $query->where('pm.mes', $mes))
            ->when($estado, fn ($query) => $query->where($this->columnaEstado(), $estado))
            ->where(function ($q) {
                $q->where('pm.estado', 'Pendiente')->orWhere('pm.estado', 'Vencido');
            })
            ->sum(DB::raw('COALESCE(pm.saldo, pm.monto)'));

        $mensualidades = $this->consultaBase()
            ->when($search, function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->whereRaw("LOWER(CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno)) LIKE LOWER(?)", [$like])
                        ->orWhereRaw('LOWER(a.ci) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(c.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(g.nombre) LIKE LOWER(?)', [$like])
                        ->orWhereRaw('LOWER(pm.mes) LIKE LOWER(?)', [$like]);
                });
            })
            ->when($idGestion, fn ($query) => $query->where('pm.id_gestion', $idGestion))
            ->when($idCurso, fn ($query) => $query->where('pm.id_curso', $idCurso))
            ->when($mes, fn ($query) => $query->where('pm.mes', $mes))
            ->when($estado, fn ($query) => $query->where($this->columnaEstado(), $estado))
            ->paginate(15)
            ->appends($request->except('page'));

        return view('admin.mensualidades.index', array_merge($this->catalogos(), compact(
            'mensualidades',
            'search',
            'idGestion',
            'idCurso',
            'mes',
            'estado',
            'totalPendiente'
        )));
    }

    public function create()
    {
        return view('admin.mensualidades.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_inscripcion' => ['required', 'integer'],
            'monto' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'descuento' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'id_beca' => ['nullable', 'integer', 'exists:beca,id_beca'],
        ], [
            'id_inscripcion.required' => 'Seleccione una matricula activa.',
            'monto.required' => 'Ingrese el monto base.',
        ]);

        $matricula = $this->buscarMatriculaActiva((int) $data['id_inscripcion']);
        $this->validarGestionActiva((int) $matricula->id_gestion);

        $idBeca = $data['id_beca'] ?? null;
        if ($idBeca && ! auth()->user()->isAdmin()) {
            $beca = DB::table('beca')->where('id_beca', $idBeca)->first();
            if ($beca && $beca->admin_only) {
                throw ValidationException::withMessages([
                    'id_beca' => 'No tiene permisos para asignar esta beca.',
                ]);
            }
        }
        if (empty($idBeca)) {
            $idBeca = $this->autoAsignarBeca((int) $matricula->id_alumno, (int) $matricula->id_gestion, (int) $matricula->id_curso);
        }

        $descuento = (float) ($data['descuento'] ?? 0);
        if ($descuento == 0 && ! empty($idBeca)) {
            $beca = DB::table('beca')->where('id_beca', $idBeca)->first();
            if ($beca && $beca->activo) {
                $descuento = round((float) $data['monto'] * ((float) $beca->porcentaje / 100), 2);
            }
        }
        $montoFinal = max(0, (float) $data['monto'] - $descuento);

        DB::transaction(function () use ($data, $matricula, $descuento, $montoFinal, $idBeca) {
            foreach (self::MESES as $mes) {
                $existe = DB::table('pago_mensual')
                    ->where('id_alumno', $matricula->id_alumno)
                    ->where('id_gestion', $matricula->id_gestion)
                    ->where('id_curso', $matricula->id_curso)
                    ->where('mes', $mes)
                    ->exists();

                if ($existe) {
                    continue;
                }

                $fila = [
                    'monto' => $montoFinal,
                    'fecha' => $this->fechaVencimiento((int) $matricula->id_gestion, $mes),
                    'mes' => $mes,
                    'descuento' => $descuento,
                    'id_gestion' => $matricula->id_gestion,
                    'id_curso' => $matricula->id_curso,
                    'id_alumno' => $matricula->id_alumno,
                    'id_beca' => $idBeca,
                ];

                if (Schema::hasColumn('pago_mensual', 'estado')) {
                    $fila['estado'] = 'Pendiente';
                }

                if (Schema::hasColumn('pago_mensual', 'fecha_pago')) {
                    $fila['fecha_pago'] = null;
                }

                DB::table('pago_mensual')->insert($fila);
            }
        });

        return redirect()->route('admin.mensualidades.index')
            ->with('mensaje', 'Mensualidades generadas exitosamente')
            ->with('icono', 'success');
    }

    public function registrarPago(Request $request, int $idPagoMensual)
    {
        $mensualidad = DB::table('pago_mensual')->where('id_pago_mensual', $idPagoMensual)->firstOrFail();

        if ($this->estadoActual($mensualidad) === 'Pagado') {
            return redirect()->route('admin.mensualidades.index')
                ->with('mensaje', 'La mensualidad ya fue pagada')
                ->with('icono', 'error');
        }

        $data = $request->validate([
            'monto_recibido' => ['required', 'numeric', 'min:0.01'],
            'fecha_pago' => ['required', 'date'],
        ]);

        if ((float) $data['monto_recibido'] < (float) $mensualidad->monto) {
            throw ValidationException::withMessages([
                'monto_recibido' => 'El monto recibido no puede ser menor al monto establecido.',
            ]);
        }

        $actualizacion = [
            'monto' => $data['monto_recibido'],
        ];

        if (Schema::hasColumn('pago_mensual', 'estado')) {
            $actualizacion['estado'] = 'Pagado';
        }

        if (Schema::hasColumn('pago_mensual', 'fecha_pago')) {
            $actualizacion['fecha_pago'] = $data['fecha_pago'];
        } else {
            $actualizacion['fecha'] = $data['fecha_pago'];
        }

        DB::table('pago_mensual')->where('id_pago_mensual', $idPagoMensual)->update($actualizacion);

        return redirect()->route('admin.mensualidades.index')
            ->with('mensaje', 'Pago de mensualidad registrado')
            ->with('icono', 'success');
    }

    private function consultaBase()
    {
        return DB::table('pago_mensual as pm')
            ->join('alumno as a', 'a.id_alumno', '=', 'pm.id_alumno')
            ->join('gestion as g', 'g.id_gestion', '=', 'pm.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'pm.id_curso')
            ->leftJoin('beca as b', 'b.id_beca', '=', 'pm.id_beca')
            ->select(
                'pm.id_pago_mensual',
                'pm.id_alumno',
                'pm.id_gestion',
                'pm.id_curso',
                'pm.monto',
                'pm.descuento',
                'pm.fecha',
                'pm.mes',
                'pm.id_beca',
                DB::raw($this->columnaEstado() . ' as estado'),
                DB::raw(Schema::hasColumn('pago_mensual', 'fecha_pago') ? 'pm.fecha_pago' : 'pm.fecha as fecha_pago'),
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'g.nombre as gestion',
                'c.nombre as curso',
                'b.descripcion as beca',
                DB::raw("CASE WHEN " . $this->columnaEstado() . " IN ('Pendiente', 'Vencido', 'Anulado') THEN pm.monto ELSE 0 END as saldo")
            )
            ->orderByDesc('pm.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderByRaw("FIELD(pm.mes, 'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre')");
    }

    private function catalogos(): array
    {
        $becasQuery = DB::table('beca')->where('activo', true);

        if (! auth()->user()?->isAdmin()) {
            $becasQuery->where('admin_only', false);
        }

        return [
            'matriculasActivas' => $this->matriculasActivas(),
            'gestiones' => DB::table('gestion')->orderByDesc('id_gestion')->get(),
            'cursos' => DB::table('curso')->orderBy('nombre')->get(),
            'becas' => $becasQuery->orderBy('nombre')->get(),
            'meses' => self::MESES,
            'estados' => self::ESTADOS,
        ];
    }

    private function matriculasActivas()
    {
        $estadoMatricula = Schema::hasColumn('matricula', 'estado') ? 'm.estado' : "'Activa'";

        return DB::table('inscripcion as i')
            ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->whereRaw($estadoMatricula . " = 'Activa'")
            ->select(
                'i.id_inscripcion',
                'i.id_alumno',
                'icg.id_gestion',
                'icg.id_curso',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'g.nombre as gestion',
                'c.nombre as curso'
            )
            ->orderByDesc('icg.id_gestion')
            ->orderBy('c.nombre')
            ->orderBy('a.ap_paterno')
            ->get();
    }

    private function buscarMatriculaActiva(int $idInscripcion)
    {
        $matricula = $this->matriculasActivas()
            ->firstWhere('id_inscripcion', $idInscripcion);

        if (! $matricula) {
            throw ValidationException::withMessages([
                'id_inscripcion' => 'La matricula seleccionada no existe o no esta activa.',
            ]);
        }

        return $matricula;
    }

    private function validarGestionActiva(int $idGestion): void
    {
        $activa = DB::table('gestion')
            ->where('id_gestion', $idGestion)
            ->where('activo', true)
            ->exists();

        if (! $activa) {
            throw ValidationException::withMessages([
                'id_inscripcion' => 'No se pueden generar mensualidades. El anio lectivo no esta activo.',
            ]);
        }
    }

    private function autoAsignarBeca(int $idAlumno, int $idGestion, int $idCurso): ?int
    {
        $becaExcelenciaId = (int) DB::table('beca')->where('nombre', 'Excelencia')->value('id_beca');
        if ($becaExcelenciaId) {
            $mejorAlumno = DB::selectOne("
                SELECT id_alumno, AVG(promediofinal) as promedio
                FROM nota
                WHERE id_gestion = ? AND id_curso = ?
                GROUP BY id_alumno
                ORDER BY promedio DESC
                LIMIT 1
            ", [$idGestion, $idCurso]);

            if ($mejorAlumno && (int) $mejorAlumno->id_alumno === $idAlumno) {
                return $becaExcelenciaId;
            }
        }

        $becaHermanosId = (int) DB::table('beca')->where('nombre', 'Hermanos')->value('id_beca');
        if ($becaHermanosId) {
            $totalHermanos = DB::selectOne("
                SELECT COUNT(DISTINCT p2.id_alumno) as total
                FROM parentesco p1
                INNER JOIN parentesco p2 ON p1.id_apoderado = p2.id_apoderado
                INNER JOIN alumno a1 ON a1.id_alumno = p1.id_alumno
                INNER JOIN alumno a2 ON a2.id_alumno = p2.id_alumno
                WHERE p1.id_alumno = ?
                AND a2.ap_paterno = a1.ap_paterno
                AND a2.ap_materno = a1.ap_materno
            ", [$idAlumno]);

            if ($totalHermanos && (int) $totalHermanos->total >= 3) {
                $hermanoMenor = DB::selectOne("
                    SELECT a2.id_alumno
                    FROM parentesco p1
                    INNER JOIN parentesco p2 ON p1.id_apoderado = p2.id_apoderado
                    INNER JOIN alumno a1 ON a1.id_alumno = p1.id_alumno
                    INNER JOIN alumno a2 ON a2.id_alumno = p2.id_alumno
                    WHERE p1.id_alumno = ?
                    AND a2.ap_paterno = a1.ap_paterno
                    AND a2.ap_materno = a1.ap_materno
                    ORDER BY a2.fecha_nac DESC
                    LIMIT 1
                ", [$idAlumno]);

                if ($hermanoMenor && (int) $hermanoMenor->id_alumno === $idAlumno) {
                    return $becaHermanosId;
                }
            }
        }

        $alumno = DB::table('alumno')->find($idAlumno);
        if ($alumno && ! empty($alumno->id_beca)) {
            return (int) $alumno->id_beca;
        }

        return null;
    }

    private function fechaVencimiento(int $idGestion, string $mes): string
    {
        $gestion = DB::table('gestion')->where('id_gestion', $idGestion)->first();
        $anio = $gestion ? (int) Carbon::parse($gestion->fechainicio)->format('Y') : (int) now()->format('Y');
        $numeroMes = array_search($mes, self::MESES, true) + 2;

        return Carbon::create($anio, $numeroMes, 10)->toDateString();
    }

    private function estadoActual(object $mensualidad): string
    {
        if (Schema::hasColumn('pago_mensual', 'estado')) {
            return $mensualidad->estado;
        }

        return 'Pagado';
    }

    private function columnaEstado(): string
    {
        return Schema::hasColumn('pago_mensual', 'estado') ? 'pm.estado' : "'Pagado'";
    }
}
