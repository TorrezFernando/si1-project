@extends('adminlte::page')

@section('title', 'Matriculas')

@section('content_header')
    <h1>Matriculas</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $matriculas->total() }} {{ $matriculas->total() == 1 ? 'matricula' : 'matriculas' }}</h4>
            <p>Gestion de inscripciones y matriculas del colegio</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.matriculas.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Registrar Matricula
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.matriculas.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Estudiante, CI, curso, tutor..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-2">
                    <select name="id_gestion" class="form-control" style="border-radius: 8px;">
                        <option value="">Todas las gestiones</option>
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion->id_gestion }}" {{ (string) $idGestion === (string) $gestion->id_gestion ? 'selected' : '' }}>{{ $gestion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_curso" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id_curso }}" {{ (string) $idCurso === (string) $curso->id_curso ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $item)
                            <option value="{{ $item }}" {{ (string) $estado === (string) $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.matriculas.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Todo</a>
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
                            <th>Gestion</th>
                            <th>Tutor</th>
                            <th style="width: 100px;">Fecha</th>
                            <th style="width: 100px;">Monto</th>
                            <th style="width: 180px;">Estado</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matriculas as $matricula)
                        <tr>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">{{ $matricula->alumno }}</span>
                            </td>
                            <td><span class="badge-chip">{{ $matricula->ci_alumno }}</span></td>
                            <td>{{ $matricula->curso }}</td>
                            <td>{{ $matricula->gestion }}</td>
                            <td style="font-size: 0.85rem;">{{ $matricula->apoderado ?: '—' }}</td>
                            <td style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($matricula->fecha)->format('d/m/Y') }}</td>
                            <td style="font-weight: 600;">Bs. {{ number_format((float) $matricula->monto, 2) }}</td>
                            <td>
                                <form action="{{ route('admin.matriculas.estado', $matricula->id_inscripcion) }}" method="POST" style="display: flex; gap: 4px; align-items: center;">
                                    @csrf @method('PATCH')
                                    <select name="estado" class="form-control form-control-sm" style="border-radius: 6px; width: auto; min-width: 110px;">
                                        @foreach($estados as $item)
                                            <option value="{{ $item }}" {{ $matricula->estado === $item ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-icon btn-icon-key" title="Cambiar estado" style="flex-shrink: 0;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.matriculas.edit', $matricula->id_inscripcion) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.matriculas.destroy', $matricula->id_inscripcion) }}"
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
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">📋</div>
                                    <h5>Sin matriculas</h5>
                                    <p>No se encontraron matriculas registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($matriculas->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $matriculas->firstItem() }}-{{ $matriculas->lastItem() }} de {{ $matriculas->total() }}
                </small>
                {{ $matriculas->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
