@extends('adminlte::page')

@section('title', 'Gestionar Notas')

@section('content_header')
    <h1>Notas</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $notas->total() }} {{ $notas->total() == 1 ? 'calificacion' : 'calificaciones' }}</h4>
            <p>Gestion de calificaciones por alumno, materia y trimestre</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.notas.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Registrar Nota
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.notas.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Estudiante, materia, curso..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-2">
                    <select name="id_gestion" class="form-control" style="border-radius: 8px;">
                        <option value="">Gestion</option>
                        @foreach($gestiones as $g)
                            <option value="{{ $g->id_gestion }}" {{ (string) $idGestion === (string) $g->id_gestion ? 'selected' : '' }}>{{ $g->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_curso" class="form-control" style="border-radius: 8px;">
                        <option value="">Curso</option>
                        @foreach($cursos as $c)
                            <option value="{{ $c->id_curso }}" {{ (string) $idCurso === (string) $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_materia" class="form-control" style="border-radius: 8px;">
                        <option value="">Materia</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->id_materia }}" {{ (string) $idMateria === (string) $m->id_materia ? 'selected' : '' }}>{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_trimestre" class="form-control" style="border-radius: 8px;">
                        <option value="">Trimestre</option>
                        @foreach($trimestres as $t)
                            <option value="{{ $t->id_trimestre }}" {{ (string) $idTrimestre === (string) $t->id_trimestre ? 'selected' : '' }}>{{ $t->id_trimestre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    <a href="{{ route('admin.notas.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Curso</th>
                            <th>Materia</th>
                            <th style="width: 80px;">Gestion</th>
                            <th style="width: 80px;">Trim.</th>
                            <th class="text-center">Ser</th>
                            <th class="text-center">Saber</th>
                            <th class="text-center">Hacer</th>
                            <th class="text-center">Autoev.</th>
                            <th class="text-center">Promedio</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notas as $nota)
                        <tr>
                            <td><span style="font-weight: 600; color: #1e293b;">{{ $nota->alumno }}</span></td>
                            <td>{{ $nota->curso }}</td>
                            <td>{{ $nota->materia }}</td>
                            <td>{{ $nota->gestion }}</td>
                            <td><span class="badge-chip">{{ $nota->trimestre }}</span></td>
                            <td class="text-center">{{ $nota->ser }}</td>
                            <td class="text-center">{{ $nota->saber }}</td>
                            <td class="text-center">{{ $nota->hacer }}</td>
                            <td class="text-center">{{ $nota->autoevaluacion }}</td>
                            <td class="text-center">
                                <span class="badge-chip" style="background: #dbeafe; color: #1e40af; font-weight: 700;">
                                    {{ number_format((float) $nota->promediofinal, 2) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.notas.edit', [$nota->id_alumno, $nota->id_materia, $nota->id_gestion, $nota->id_curso, $nota->id_trimestre]) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.notas.destroy', [$nota->id_alumno, $nota->id_materia, $nota->id_gestion, $nota->id_curso, $nota->id_trimestre]) }}"
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
                            <td colspan="11">
                                <div class="empty-state">
                                    <div class="empty-icon">📊</div>
                                    <h5>Sin calificaciones</h5>
                                    <p>No se encontraron notas registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($notas->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $notas->firstItem() }}-{{ $notas->lastItem() }} de {{ $notas->total() }}
                </small>
                {{ $notas->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
