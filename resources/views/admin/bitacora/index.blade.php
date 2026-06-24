@extends('adminlte::page')

@section('title', 'Bitacora')

@section('content_header')
    <h1>Bitacora de Accesos</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $bitacoras->total() }} {{ $bitacoras->total() == 1 ? 'registro' : 'registros' }}</h4>
            <p>Historial de accesos y acciones en el sistema</p>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.bitacora.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-5">
                    <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.2rem;">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Accion, IP o usuario..."
                           value="{{ $search ?? '' }}" style="border-radius: 8px; font-size: 0.85rem;">
                </div>
                <div class="col-md-4">
                    <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.2rem;">Rol</label>
                    <select name="id_rol" class="form-control" style="border-radius: 8px; font-size: 0.85rem;">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $rol)
                            @php
                                $rolLabel = \App\Enums\Rol::tryFrom((int) $rol->id_rol)?->label() ?? $rol->tipo;
                            @endphp
                            <option value="{{ $rol->id_rol }}" {{ (string) $idRol === (string) $rol->id_rol ? 'selected' : '' }}>
                                {{ $rolLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 8px; padding: 0.45rem 1rem;">
                        <i class="fas fa-search mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.bitacora.index') }}" class="btn btn-sm btn-secondary" style="border-radius: 8px; padding: 0.45rem 1rem;">
                        <i class="fas fa-times mr-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th style="width: 155px;">Fecha y Hora</th>
                            <th>Accion</th>
                            <th style="width: 130px;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bitacoras as $bit)
                            @php
                                $icon = match(true) {
                                    stripos($bit->accion, 'inicio') !== false || stripos($bit->accion, 'login') !== false => ['fa-sign-in-alt', '#16a34a'],
                                    stripos($bit->accion, 'cerro') !== false || stripos($bit->accion, 'logout') !== false || stripos($bit->accion, 'cerró') !== false => ['fa-sign-out-alt', '#dc2626'],
                                    stripos($bit->accion, 'creó') !== false || stripos($bit->accion, 'creo') !== false || stripos($bit->accion, 'registró') !== false || stripos($bit->accion, 'crear') !== false => ['fa-plus-circle', '#2563eb'],
                                    stripos($bit->accion, 'editó') !== false || stripos($bit->accion, 'edito') !== false || stripos($bit->accion, 'actualizó') !== false || stripos($bit->accion, 'editar') !== false => ['fa-pen', '#7c3aed'],
                                    stripos($bit->accion, 'eliminó') !== false || stripos($bit->accion, 'elimino') !== false || stripos($bit->accion, 'eliminar') !== false => ['fa-trash', '#dc2626'],
                                    default => ['fa-circle', '#64748b'],
                                };
                            @endphp
                            <tr>
                                <td><span class="badge-chip">{{ $bit->id_bitacora }}</span></td>
                                <td>
                                    <span style="font-weight: 600; color: #1e293b;">
                                        {{ $bit->usuario ? $bit->usuario->username : 'Desconocido' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-chip">
                                        {{ $bit->usuario ? $bit->usuario->rol_nombre : '—' }}
                                    </span>
                                </td>
                                <td style="font-size: 0.84rem; white-space: nowrap;">
                                    <i class="far fa-calendar-alt mr-1" style="color: #94a3b8; font-size: 0.7rem;"></i>
                                    {{ $bit->fecha_hora->format('d/m/Y') }}
                                    <span style="color: #94a3b8; margin: 0 2px;">·</span>
                                    <i class="far fa-clock mr-1" style="color: #94a3b8; font-size: 0.7rem;"></i>
                                    {{ $bit->fecha_hora->format('H:i:s') }}
                                </td>
                                <td>
                                    <i class="fas {{ $icon[0] }} mr-1" style="color: {{ $icon[1] }}; font-size: 0.72rem;"></i>
                                    {{ $bit->accion }}
                                </td>
                                <td>
                                    <code style="background: #f1f5f9; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.78rem;">
                                        {{ $bit->ip }}
                                    </code>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon">📋</div>
                                        <h5>Sin registros</h5>
                                        <p>No se encontraron entradas con los filtros actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($bitacoras->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $bitacoras->firstItem() }}-{{ $bitacoras->lastItem() }} de {{ $bitacoras->total() }}
                </small>
                @include('admin.partials.list-pagination', ['paginator' => $bitacoras])
            </div>
        </div>
        @endif
    </div>
@stop
