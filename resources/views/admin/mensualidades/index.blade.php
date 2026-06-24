@extends('adminlte::page')

@section('title', 'Mensualidades')

@section('content_header')
    <h1>Mensualidades</h1>
@stop

@section('content')
    {{-- Stats card --}}
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card orange">
            <div class="stat-icon si-orange">⏳</div>
            <div class="stat-number">Bs. {{ number_format((float) $totalPendiente, 2) }}</div>
            <div class="stat-label">Total pendiente</div>
        </div>
    </div>

    {{-- List --}}
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $mensualidades->total() }} {{ $mensualidades->total() == 1 ? 'mensualidad' : 'mensualidades' }}</h4>
            <p>Gestion de pagos mensuales del colegio</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.mensualidades.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Generar Mensualidades
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.mensualidades.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Estudiante, CI, curso..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-2">
                    <select name="id_gestion" class="form-control" style="border-radius: 8px;">
                        <option value="">Gestion</option>
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion->id_gestion }}" {{ (string) $idGestion === (string) $gestion->id_gestion ? 'selected' : '' }}>{{ $gestion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_curso" class="form-control" style="border-radius: 8px;">
                        <option value="">Curso</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id_curso }}" {{ (string) $idCurso === (string) $curso->id_curso ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="mes" class="form-control" style="border-radius: 8px;">
                        <option value="">Mes</option>
                        @foreach($meses as $item)
                            <option value="{{ $item }}" {{ (string) $mes === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-control" style="border-radius: 8px;">
                        <option value="">Estado</option>
                        @foreach($estados as $item)
                            <option value="{{ $item }}" {{ (string) $estado === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    <a href="{{ route('admin.mensualidades.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list"></i></a>
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
                            <th style="width: 90px;">Mes</th>
                            <th style="width: 100px;">Vencimiento</th>
                            <th style="width: 90px;">Monto</th>
                            <th style="width: 80px;">Desc.</th>
                            <th style="width: 90px;">Estado</th>
                            <th style="width: 100px;">Pago</th>
                            <th style="width: 150px;">Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mensualidades as $mensualidad)
                            <tr>
                                <td><span style="font-weight: 600; color: #1e293b;">{{ $mensualidad->alumno }}</span></td>
                                <td><span class="badge-chip">{{ $mensualidad->ci_alumno }}</span></td>
                                <td>{{ $mensualidad->curso }}</td>
                                <td>{{ $mensualidad->gestion }}</td>
                                <td>{{ $mensualidad->mes }}</td>
                                <td style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($mensualidad->fecha)->format('d/m/Y') }}</td>
                                <td style="font-weight: 600;">Bs. {{ number_format((float) $mensualidad->monto, 2) }}</td>
                                <td style="font-size: 0.85rem;">Bs. {{ number_format((float) $mensualidad->descuento, 2) }}</td>
                                <td>
                                    @php
                                        $estColor = match ($mensualidad->estado) {
                                            'Pagado' => 'on',
                                            'Vencido' => 'off',
                                            default => '',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $estColor }}" style="{{ $mensualidad->estado === 'Pendiente' ? 'background: #fef3c7; color: #92400e;' : '' }}">
                                        @if(in_array($mensualidad->estado, ['Pagado', 'Vencido']))
                                            <span class="status-dot"></span>
                                        @endif
                                        {{ $mensualidad->estado }}
                                    </span>
                                </td>
                                <td style="font-size: 0.82rem;">
                                    @if($mensualidad->fecha_pago)
                                        {{ \Carbon\Carbon::parse($mensualidad->fecha_pago)->format('d/m/Y') }}
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mensualidad->estado === 'Pagado')
                                        <span style="color: #94a3b8; font-size: 0.82rem;">
                                            <i class="fas fa-check-circle mr-1" style="color: #16a34a;"></i>Completado
                                        </span>
                                    @else
                                        <form action="{{ route('admin.mensualidades.pago', $mensualidad->id_pago_mensual) }}" method="POST"
                                              style="display: flex; gap: 3px; align-items: center;">
                                            @csrf @method('PATCH')
                                            <input type="number" name="monto_recibido" step="0.01" min="0.01"
                                                   value="{{ number_format((float) $mensualidad->monto, 2, '.', '') }}"
                                                   style="width: 75px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.25rem 0.3rem; font-size: 0.75rem;">
                                            <input type="date" name="fecha_pago" value="{{ now()->toDateString() }}"
                                                   style="width: 115px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.25rem 0.3rem; font-size: 0.72rem;">
                                            <button type="submit" class="btn-icon btn-icon-key" title="Registrar pago">
                                                <i class="fas fa-cash-register"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state">
                                        <div class="empty-icon">📅</div>
                                        <h5>Sin mensualidades</h5>
                                        <p>No se encontraron mensualidades con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($mensualidades->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $mensualidades->firstItem() }}-{{ $mensualidades->lastItem() }} de {{ $mensualidades->total() }}
                </small>
                {{ $mensualidades->links('admin.partials.list-pagination') }}
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
