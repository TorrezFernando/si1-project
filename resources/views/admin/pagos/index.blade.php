@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <h1>Pagos</h1>
@stop

@section('content')
    {{-- Stats cards --}}
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card orange">
            <div class="stat-icon si-orange">⏳</div>
            <div class="stat-number">Bs. {{ number_format((float) $totalPendiente, 2) }}</div>
            <div class="stat-label">Total pendiente</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon si-green">✅</div>
            <div class="stat-number">Bs. {{ number_format((float) $totalPagado, 2) }}</div>
            <div class="stat-label">Total pagado</div>
        </div>
    </div>

    {{-- List --}}
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $paginator->total() }} {{ $paginator->total() == 1 ? 'obligacion' : 'obligaciones' }}</h4>
            <p>Gestion de pagos de matricula y mensualidades</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.pagos.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Registrar Pago
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.pagos.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Estudiante, CI, curso, concepto..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <select name="tipo" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $item)
                            <option value="{{ $item }}" {{ (string) $tipo === (string) $item ? 'selected' : '' }}>{{ ucfirst($item) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $item)
                            <option value="{{ $item }}" {{ (string) $estado === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th style="width: 90px;">CI</th>
                            <th>Curso</th>
                            <th style="width: 80px;">Gestion</th>
                            <th>Concepto</th>
                            <th style="width: 100px;">Vencimiento</th>
                            <th style="width: 100px;">Monto</th>
                            <th style="width: 140px;">Estado</th>
                            <th style="width: 100px;">Pago</th>
                            <th style="width: 110px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginator as $pago)
                            <tr>
                                <td><span style="font-weight: 600; color: #1e293b;">{{ $pago->alumno }}</span></td>
                                <td><span class="badge-chip">{{ $pago->ci_alumno }}</span></td>
                                <td>{{ $pago->curso }}</td>
                                <td>{{ $pago->gestion }}</td>
                                <td style="font-size: 0.85rem;">{{ ucfirst($pago->tipo) }} — {{ $pago->concepto }}</td>
                                <td style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($pago->fecha_vencimiento)->format('d/m/Y') }}</td>
                                <td style="font-weight: 600;">Bs. {{ number_format((float) $pago->monto_pendiente, 2) }}</td>
                                <td>
                                    @php
                                        $estadoColor = match ($pago->estado_pago) {
                                            'Pagado' => 'on',
                                            'Anulado' => 'off',
                                            default => '',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $estadoColor }}" style="{{ $pago->estado_pago === 'Pendiente' ? 'background: #fef3c7; color: #92400e;' : '' }}">
                                        @if($pago->estado_pago !== 'Pendiente')
                                            <span class="status-dot"></span>
                                        @endif
                                        {{ $pago->estado_pago }}
                                    </span>
                                    @if($pago->motivo_anulacion)
                                        <br><small style="color: #94a3b8; font-size: 0.72rem;">{{ $pago->motivo_anulacion }}</small>
                                    @endif
                                </td>
                                <td style="font-size: 0.82rem;">
                                    @if($pago->fecha_pago)
                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}<br>
                                        <strong style="color: #166534;">Bs. {{ number_format((float) $pago->monto_pagado, 2) }}</strong>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pago->estado_pago === 'Pagado')
                                        <div class="action-btns">
                                            <a href="{{ route('admin.pagos.edit', [$pago->tipo, $pago->id_referencia]) }}"
                                               class="btn-icon btn-icon-edit" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.pagos.anular', [$pago->tipo, $pago->id_referencia]) }}"
                                                  method="POST" style="display: inline-flex; gap: 3px; align-items: center;">
                                                @csrf @method('PATCH')
                                                <input type="text" name="motivo_anulacion" placeholder="Motivo"
                                                       style="width: 70px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.25rem 0.4rem; font-size: 0.72rem;" required>
                                                <button type="submit" class="btn-icon btn-icon-delete" title="Anular">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('admin.pagos.create', ['tipo' => $pago->tipo, 'referencia' => $pago->id_referencia]) }}"
                                           class="btn btn-sm btn-primary" style="border-radius: 8px; font-size: 0.78rem; padding: 0.3rem 0.6rem;">
                                            <i class="fas fa-cash-register mr-1"></i> Pagar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-icon">💰</div>
                                        <h5>Sin obligaciones</h5>
                                        <p>No se encontraron pagos registrados con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($paginator->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} de {{ $paginator->total() }}
                </small>
                {{ $paginator->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
<script>
    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 3500
        });
    @endif
</script>
@stop
