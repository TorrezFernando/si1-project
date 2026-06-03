@extends('adminlte::page')

@section('title', 'Apoderados')

@section('content_header')
    <h1>Apoderados</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $apoderados->total() }} {{ $apoderados->total() == 1 ? 'apoderado' : 'apoderados' }}</h4>
            <p>Gestion de tutores y sus estudiantes vinculados</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar por CI, nombre, usuario...">
            </form>
            <a href="{{ route('admin.apoderados.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">CI</th>
                            <th>Apoderado</th>
                            <th>Contacto</th>
                            <th>Usuario</th>
                            <th style="min-width: 180px;">Estudiantes</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apoderados as $apoderado)
                        @php
                            $fullName = trim($apoderado->nombres . ' ' . $apoderado->ap_paterno . ' ' . ($apoderado->ap_materno ?? ''));
                            $initials = strtoupper(substr($apoderado->nombres, 0, 1) . substr($apoderado->ap_paterno, 0, 1));
                            $estudiantes = $apoderado->alumnos ?? collect();
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $apoderado->ci }}</span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">{{ $initials }}</div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $fullName }}</div>
                                        <div class="user-detail">{{ $apoderado->ocupacion ?: 'Sin ocupacion' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem; color: #64748b;">
                                    @if($apoderado->telefono)
                                        <i class="fas fa-phone-alt mr-1" style="font-size: 0.7rem;"></i> {{ $apoderado->telefono }}
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">
                                    {{ $apoderado->usuario->username ?? '—' }}
                                </span>
                            </td>
                            <td>
                                @if($estudiantes->isEmpty())
                                    <span style="color: #cbd5e1; font-size: 0.85rem;">Sin estudiantes</span>
                                @else
                                    @foreach($estudiantes as $estudiante)
                                        <span class="badge-chip">
                                            {{ $estudiante->nombre_completo ?? ($estudiante->nombres . ' ' . $estudiante->ap_paterno) }}
                                            @if($estudiante->pivot && $estudiante->pivot->descripcion)
                                                <small style="opacity:0.7;">({{ $estudiante->pivot->descripcion }})</small>
                                            @endif
                                        </span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.apoderados.edit', $apoderado->id_apoderado) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.apoderados.destroy', $apoderado->id_apoderado) }}"
                                          method="POST" class="form-delete" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-delete" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">👪</div>
                                    <h5>Sin apoderados</h5>
                                    <p>No se encontraron tutores registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($apoderados->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $apoderados->firstItem() }}-{{ $apoderados->lastItem() }} de {{ $apoderados->total() }}
                </small>
                {{ $apoderados->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
