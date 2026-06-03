@extends('adminlte::page')

@section('title', 'Alumnos')

@section('content_header')
    <h1>Alumnos</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $alumnos->total() }} {{ $alumnos->total() == 1 ? 'alumno' : 'alumnos' }}</h4>
            <p>Gestion de estudiantes registrados en el sistema</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.alumnos.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nuevo Alumno
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Alumno</th>
                            <th style="width: 100px;">CI</th>
                            <th style="width: 90px;">Genero</th>
                            <th>Fecha Nac.</th>
                            <th>Telefono</th>
                            <th>Usuario</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alumnos as $index => $alumno)
                        @php
                            $fullName = trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . ($alumno->ap_materno ?? ''));
                            $initials = strtoupper(substr($alumno->nombres, 0, 1) . substr($alumno->ap_paterno, 0, 1));
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $alumnos->firstItem() + $index }}</span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar" style="background: linear-gradient(135deg, #2563eb, #3b82f6);">{{ $initials }}</div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $fullName }}</div>
                                        <div class="user-detail">CI: {{ $alumno->ci }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $alumno->ci }}</td>
                            <td><span class="badge-chip">{{ $alumno->genero === 'F' ? 'Femenino' : 'Masculino' }}</span></td>
                            <td style="font-size: 0.85rem;">{{ $alumno->fecha_nac }}</td>
                            <td style="font-size: 0.85rem;">{{ $alumno->telefono ?: '—' }}</td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">
                                    {{ optional($alumno->usuario)->username ?? 'Sin usuario' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.alumnos.edit', $alumno->id_alumno) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.alumnos.destroy', $alumno->id_alumno) }}"
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
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon">👨‍🎓</div>
                                    <h5>Sin alumnos</h5>
                                    <p>No hay estudiantes registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($alumnos->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $alumnos->firstItem() }}-{{ $alumnos->lastItem() }} de {{ $alumnos->total() }}
                </small>
                {{ $alumnos->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
