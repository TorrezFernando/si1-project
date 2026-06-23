<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Controlador para la integracion con la pasarela de pagos Libelula.
// Permite enviar una obligacion a la pasarela y confirmar el pago manualmente.
class PagoPasarelaController extends Controller
{
    // Envia una obligacion pendiente a la pasarela de pagos.
    // Crea un registro en pasarela_transacciones y redirige a la URL de Libelula.
    public function pagarEnLinea(Request $request)
    {
        $tipo = $request->input('tipo');
        $idReferencia = (int) $request->input('id_referencia');

        // Validar que el tipo sea matricula o mensualidad
        if (! in_array($tipo, ['matricula', 'mensualidad'], true)) {
            return back()->with('mensaje', 'Tipo de pago invalido.')->with('icono', 'error');
        }

        // Buscar la obligacion en la BD
        $obligacion = $this->buscarObligacion($tipo, $idReferencia);

        if (! $obligacion) {
            return back()->with('mensaje', 'Obligacion no encontrada.')->with('icono', 'error');
        }

        if ($obligacion->estado_pago !== 'Pendiente') {
            return back()->with('mensaje', 'Esta obligacion ya no esta pendiente.')->with('icono', 'error');
        }

        // Si ya hay una transaccion pendiente, solo redirigir a la pasarela
        $pendiente = DB::table('pasarela_transacciones')
            ->where('tipo', $tipo)
            ->where('id_referencia', $idReferencia)
            ->where('estado', 'pendiente')
            ->first();

        if ($pendiente) {
            return redirect()->away($pendiente->url_pasarela);
        }

        // Registrar la intencion de pago en la base de datos
        DB::table('pasarela_transacciones')->insert([
            'tipo' => $tipo,
            'id_referencia' => $idReferencia,
            'identificador_deuda' => strtoupper($tipo[0]) . $idReferencia . '_' . time(),
            'url_pasarela' => 'https://libelula.bo',
            'monto' => $obligacion->monto_pendiente,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->away('https://libelula.bo');
    }

    // Confirma manualmente un pago que fue enviado a la pasarela.
    // Marca la transaccion como pagada y actualiza la obligacion original.
    public function confirmarPago(Request $request)
    {
        $idTransaccion = (int) $request->input('id');

        $transaccion = DB::table('pasarela_transacciones')->where('id', $idTransaccion)->first();

        if (! $transaccion) {
            return back()->with('mensaje', 'Transaccion no encontrada.')->with('icono', 'error');
        }

        if ($transaccion->estado !== 'pendiente') {
            return back()->with('mensaje', 'Esta transaccion ya fue procesada.')->with('icono', 'error');
        }

        // Actualizar transaccion y obligacion dentro de una transaccion de BD
        DB::transaction(function () use ($transaccion) {
            DB::table('pasarela_transacciones')
                ->where('id', $transaccion->id)
                ->update([
                    'estado' => 'pagado',
                    'fecha_pago_pasarela' => now(),
                    'updated_at' => now(),
                ]);

            if ($transaccion->tipo === 'matricula') {
                DB::table('matricula')
                    ->where('id_matricula', $transaccion->id_referencia)
                    ->update([
                        'estado_pago' => 'Pagado',
                        'monto_pagado' => $transaccion->monto,
                        'fecha_pago' => now()->toDateString(),
                        'motivo_anulacion' => null,
                    ]);
            } elseif ($transaccion->tipo === 'mensualidad') {
                DB::table('pago_mensual')
                    ->where('id_pago_mensual', $transaccion->id_referencia)
                    ->update([
                        'estado' => 'Pagado',
                        'monto' => $transaccion->monto,
                        'fecha_pago' => now()->toDateString(),
                        'motivo_anulacion' => null,
                    ]);
            }
        });

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'Pago confirmado exitosamente.')
            ->with('icono', 'success');
    }

    // Busca una obligacion (matricula o mensualidad) con datos del alumno, curso y gestion.
    private function buscarObligacion(string $tipo, int $referencia): ?object
    {
        if ($tipo === 'matricula') {
            return DB::table('inscripcion as i')
                ->join('matricula as m', 'm.id_matricula', '=', 'i.id_matricula')
                ->join('alumno as a', 'a.id_alumno', '=', 'i.id_alumno')
                ->join('inscripcion_curso_gestion as icg', 'icg.id_inscripcion', '=', 'i.id_inscripcion')
                ->join('gestion as g', 'g.id_gestion', '=', 'icg.id_gestion')
                ->join('curso as c', 'c.id_curso', '=', 'icg.id_curso')
                ->select(
                    DB::raw("'matricula' as tipo"),
                    'm.id_matricula as id_referencia',
                    DB::raw("'Matricula' as concepto"),
                    DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                    'a.ci as ci_alumno',
                    'c.nombre as curso',
                    'g.nombre as gestion',
                    'm.monto as monto_pendiente',
                    'm.fecha as fecha_vencimiento',
                    'm.estado_pago'
                )
                ->where('m.id_matricula', $referencia)
                ->first();
        }

        return DB::table('pago_mensual as pm')
            ->join('alumno as a', 'a.id_alumno', '=', 'pm.id_alumno')
            ->join('gestion as g', 'g.id_gestion', '=', 'pm.id_gestion')
            ->join('curso as c', 'c.id_curso', '=', 'pm.id_curso')
            ->select(
                DB::raw("'mensualidad' as tipo"),
                'pm.id_pago_mensual as id_referencia',
                'pm.mes as concepto',
                DB::raw("CONCAT_WS(' ', a.nombres, a.ap_paterno, a.ap_materno) as alumno"),
                'a.ci as ci_alumno',
                'c.nombre as curso',
                'g.nombre as gestion',
                'pm.monto as monto_pendiente',
                'pm.fecha as fecha_vencimiento',
                'pm.estado as estado_pago'
            )
            ->where('pm.id_pago_mensual', $referencia)
            ->first();
    }
}
