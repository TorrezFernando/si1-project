<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PagoController extends Controller
{
    private const TIPOS = ['matricula', 'mensualidad'];
    private const ESTADOS = ['Pendiente', 'Pagado', 'Anulado'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $tipo = $request->input('tipo');
        $estado = $request->input('estado');

        $pagos = $this->estadoCuenta()
            ->when($search !== '', function (Collection $items) use ($search) {
                $needle = mb_strtolower($search);

                return $items->filter(function ($item) use ($needle) {
                    return str_contains(mb_strtolower($item->alumno), $needle)
                        || str_contains(mb_strtolower($item->ci_alumno), $needle)
                        || str_contains(mb_strtolower($item->curso), $needle)
                        || str_contains(mb_strtolower($item->gestion), $needle)
                        || str_contains(mb_strtolower($item->concepto), $needle);
                });
            })
            ->when($tipo, fn (Collection $items) => $items->where('tipo', $tipo))
            ->when($estado, fn (Collection $items) => $items->where('estado_pago', $estado))
            ->values();

        $totalPendiente = $pagos->where('estado_pago', 'Pendiente')->sum('monto_pendiente');
        $totalPagado = $pagos->where('estado_pago', 'Pagado')->sum('monto_pagado');

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $paginator = new LengthAwarePaginator(
            $pagos->forPage($page, $perPage),
            $pagos->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.pagos.index', compact(
            'paginator',
            'search',
            'tipo',
            'estado',
            'totalPendiente',
            'totalPagado'
        ) + [
            'tipos' => self::TIPOS,
            'estados' => self::ESTADOS,
        ]);
    }

    public function create(Request $request)
    {
        $tipo = $request->input('tipo');
        $referencia = $request->integer('referencia') ?: null;
        $obligaciones = $this->estadoCuenta()
            ->filter(fn ($item) => $item->estado_pago !== 'Pagado')
            ->values();
        $seleccionada = $tipo && $referencia
            ? $obligaciones->first(fn ($item) => $item->tipo === $tipo && (int) $item->id_referencia === $referencia)
            : null;

        return view('admin.pagos.create', compact('obligaciones', 'seleccionada'));
    }

    public function store(Request $request)
    {
        $data = $this->validarPago($request);
        $obligacion = $this->buscarObligacion($data['tipo'], (int) $data['id_referencia']);

        if ($obligacion->estado_pago === 'Pagado') {
            throw ValidationException::withMessages([
                'id_referencia' => 'El concepto seleccionado ya fue pagado.',
            ]);
        }

        if ((float) $data['monto_recibido'] < (float) $obligacion->monto_pendiente) {
            throw ValidationException::withMessages([
                'monto_recibido' => 'El monto recibido no puede ser menor al monto establecido.',
            ]);
        }

        $this->actualizarPago($data['tipo'], (int) $data['id_referencia'], [
            'monto' => $data['monto_recibido'],
            'fecha_pago' => $data['fecha_pago'],
            'estado' => 'Pagado',
            'motivo_anulacion' => null,
        ]);

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'Pago registrado exitosamente')
            ->with('icono', 'success');
    }

    public function edit(string $tipo, int $referencia)
    {
        $pago = $this->buscarObligacion($tipo, $referencia);

        if ($pago->estado_pago !== 'Pagado') {
            return redirect()->route('admin.pagos.index')
                ->with('mensaje', 'Solo se pueden editar pagos registrados.')
                ->with('icono', 'error');
        }

        return view('admin.pagos.edit', compact('pago'));
    }

    public function update(Request $request, string $tipo, int $referencia)
    {
        $pago = $this->buscarObligacion($tipo, $referencia);

        if ($pago->estado_pago !== 'Pagado') {
            throw ValidationException::withMessages([
                'monto_recibido' => 'Solo se pueden editar pagos registrados.',
            ]);
        }

        $data = $request->validate([
            'monto_recibido' => ['required', 'numeric', 'min:0.01'],
            'fecha_pago' => ['required', 'date'],
        ]);

        if ((float) $data['monto_recibido'] < (float) $pago->monto_pendiente) {
            throw ValidationException::withMessages([
                'monto_recibido' => 'El monto recibido no puede ser menor al monto establecido.',
            ]);
        }

        $this->actualizarPago($tipo, $referencia, [
            'monto' => $data['monto_recibido'],
            'fecha_pago' => $data['fecha_pago'],
            'estado' => 'Pagado',
            'motivo_anulacion' => null,
        ]);

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'Pago actualizado exitosamente')
            ->with('icono', 'success');
    }

    public function anular(Request $request, string $tipo, int $referencia)
    {
        $pago = $this->buscarObligacion($tipo, $referencia);

        if ($pago->estado_pago !== 'Pagado') {
            return redirect()->route('admin.pagos.index')
                ->with('mensaje', 'Solo se pueden anular pagos registrados.')
                ->with('icono', 'error');
        }

        $data = $request->validate([
            'motivo_anulacion' => ['required', 'string', 'max:255'],
        ]);

        $this->actualizarPago($tipo, $referencia, [
            'monto' => null,
            'fecha_pago' => null,
            'estado' => 'Anulado',
            'motivo_anulacion' => $data['motivo_anulacion'],
        ]);

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'Pago anulado exitosamente')
            ->with('icono', 'success');
    }

    private function validarPago(Request $request): array
    {
        return $request->validate([
            'tipo' => ['required', Rule::in(self::TIPOS)],
            'id_referencia' => ['required', 'integer'],
            'monto_recibido' => ['required', 'numeric', 'min:0.01'],
            'fecha_pago' => ['required', 'date'],
        ], [
            'tipo.required' => 'Seleccione el tipo de pago.',
            'id_referencia.required' => 'Seleccione una obligacion pendiente.',
            'monto_recibido.required' => 'Ingrese el monto recibido.',
            'fecha_pago.required' => 'Ingrese la fecha de pago.',
        ]);
    }

    private function estadoCuenta(): Collection
    {
        return $this->obligacionesMatricula()
            ->merge($this->obligacionesMensualidad())
            ->sortBy([
                ['gestion', 'desc'],
                ['curso', 'asc'],
                ['alumno', 'asc'],
                ['orden', 'asc'],
            ])
            ->values();
    }

    private function obligacionesMatricula(): Collection
    {
        if (! Schema::hasColumn('matricula', 'estado_pago')) {
            return collect();
        }

        return DB::table('inscripcion as i')
            ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
            ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
            ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
            ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
            ->select(
                DB::raw("'matricula' as tipo"),
                'm.id_matricula as id_referencia',
                'i.id_inscripcion',
                DB::raw("'Matricula' as concepto"),
                DB::raw('0 as orden'),
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'c.nombre as curso',
                'g.nombre as gestion',
                'm.monto as monto_pendiente',
                DB::raw('COALESCE(m.monto_pagado, 0) as monto_pagado'),
                'm.fecha as fecha_vencimiento',
                'm.fecha_pago',
                'm.estado_pago',
                'm.motivo_anulacion'
            )
            ->get();
    }

    private function obligacionesMensualidad(): Collection
    {
        return DB::table('pago_mensual as pm')
            ->join('alumno as a', 'a.id_alumno', '=', 'pm.id_alumno')
            ->join('gestion as g', 'g.id_gestion', '=', 'pm.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'pm.id_curso')
            ->select(
                DB::raw("'mensualidad' as tipo"),
                'pm.id_pago_mensual as id_referencia',
                DB::raw('NULL as id_inscripcion'),
                'pm.mes as concepto',
                DB::raw("FIELD(pm.mes, 'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre') as orden"),
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'c.nombre as curso',
                'g.nombre as gestion',
                'pm.monto as monto_pendiente',
                DB::raw("CASE WHEN pm.estado = 'Pagado' THEN pm.monto ELSE 0 END as monto_pagado"),
                'pm.fecha as fecha_vencimiento',
                'pm.fecha_pago',
                'pm.estado as estado_pago',
                DB::raw(Schema::hasColumn('pago_mensual', 'motivo_anulacion') ? 'pm.motivo_anulacion' : 'NULL as motivo_anulacion')
            )
            ->get();
    }

    private function buscarObligacion(string $tipo, int $referencia): object
    {
        if (! in_array($tipo, self::TIPOS, true)) {
            abort(404);
        }

        $obligacion = $this->estadoCuenta()
            ->first(fn ($item) => $item->tipo === $tipo && (int) $item->id_referencia === $referencia);

        if (! $obligacion) {
            abort(404);
        }

        return $obligacion;
    }

    private function actualizarPago(string $tipo, int $referencia, array $data): void
    {
        if ($tipo === 'matricula') {
            DB::table('matricula')
                ->where('id_matricula', $referencia)
                ->update([
                    'monto_pagado' => $data['monto'],
                    'fecha_pago' => $data['fecha_pago'],
                    'estado_pago' => $data['estado'],
                    'motivo_anulacion' => $data['motivo_anulacion'],
                ]);

            return;
        }

        $actualizacion = [
            'fecha_pago' => $data['fecha_pago'],
            'estado' => $data['estado'],
            'motivo_anulacion' => $data['motivo_anulacion'],
        ];

        if ($data['monto'] !== null) {
            $actualizacion['monto'] = $data['monto'];
        }

        DB::table('pago_mensual')
            ->where('id_pago_mensual', $referencia)
            ->update($actualizacion);
    }
}
