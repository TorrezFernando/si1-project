@extends('adminlte::page')

@section('title', 'Asistencias')

@section('content_header')
    <h1>Asistencias</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $asistencias->total() }} {{ $asistencias->total() == 1 ? 'registro' : 'registros' }}</h4>
            <p>Gestion de asistencia de estudiantes por materia, curso y gestion</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.asistencias.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Registrar Asistencia
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.asistencias.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar estudiante, materia..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-2">
                    <select name="id_gestion" class="form-control" style="border-radius: 8px;">
                        <option value="">Todas las gestiones</option>
                        @foreach($gestiones as $g)
                            <option value="{{ $g->id_gestion }}" {{ ($idGestion ?? '') == $g->id_gestion ? 'selected' : '' }}>{{ $g->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_curso" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id_curso }}" {{ ($idCurso ?? '') == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_materia" class="form-control" style="border-radius: 8px;">
                        <option value="">Todas las materias</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id_materia }}" {{ ($idMateria ?? '') == $m->id_materia ? 'selected' : '' }}>{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.asistencias.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Todo</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Estudiante</th>
                            <th>Materia</th>
                            <th>Curso</th>
                            <th>Gestion</th>
                            <th style="width: 110px;">Fecha</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asistencias as $asi)
                        @php
                            $estadoBadge = match($asi->estado) {
                                'P' => 'on',
                                'A' => 'off',
                                'L' => 'off',
                                'F' => 'off',
                                default => 'off',
                            };
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $asi->id_asistencia }}</span></td>
                            <td><span style="font-weight: 600; color: #1e293b;">{{ $asi->alumno }}</span></td>
                            <td>{{ $asi->materia }}</td>
                            <td>{{ $asi->curso }}</td>
                            <td>{{ $asi->gestion }}</td>
                            <td>{{ \Carbon\Carbon::parse($asi->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <span class="status-badge {{ $estadoBadge }}">
                                    <span class="status-dot"></span>
                                    {{ $asi->estado_texto }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <form action="{{ route('admin.asistencias.destroy', $asi->id_asistencia) }}"
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
                                    <div class="empty-icon">📋</div>
                                    <h5>Sin registros</h5>
                                    <p>No se encontraron registros de asistencia.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($asistencias->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $asistencias->firstItem() }}-{{ $asistencias->lastItem() }} de {{ $asistencias->total() }}
                </small>
                {{ $asistencias->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
