@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1>Horarios</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $horarios->total() }} {{ $horarios->total() == 1 ? 'horario' : 'horarios' }}</h4>
            <p>Asignacion de dias, horas y aulas para cada materia</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.horarios.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nuevo Horario
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.horarios.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Curso, docente, materia, aula o paralelo..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <select name="dia" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los dias</option>
                        @foreach($dias as $opcion)
                            <option value="{{ $opcion }}" {{ ($dia ?? '') === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.horarios.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Todo</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Paralelo</th>
                            <th>Materia</th>
                            <th>Docente</th>
                            <th style="width: 70px;">Gestion</th>
                            <th style="width: 80px;">Dia</th>
                            <th style="width: 70px;">Inicio</th>
                            <th style="width: 70px;">Fin</th>
                            <th>Aula</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($horarios as $horario)
                        <tr>
                            <td><span style="font-weight: 600; color: #1e293b;">{{ $horario->curso }}</span></td>
                            <td><span class="badge-chip">{{ $horario->paralelo }}</span></td>
                            <td>{{ $horario->materia }}</td>
                            <td style="font-size: 0.85rem;">{{ $horario->docente }}</td>
                            <td>{{ $horario->gestion }}</td>
                            <td><span class="badge-chip">{{ $horario->dia }}</span></td>
                            <td class="text-center">{{ substr($horario->hora_inicio, 0, 5) }}</td>
                            <td class="text-center">{{ substr($horario->hora_fin, 0, 5) }}</td>
                            <td style="font-size: 0.85rem;">{{ $horario->aula }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.horarios.edit', [$horario->id_materia, $horario->id_gestion, $horario->id_curso, $horario->id_paralelo]) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.horarios.destroy', [$horario->id_materia, $horario->id_gestion, $horario->id_curso, $horario->id_paralelo]) }}"
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
                            <td colspan="10">
                                <div class="empty-state">
                                    <div class="empty-icon">📅</div>
                                    <h5>Sin horarios</h5>
                                    <p>No se encontraron horarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($horarios->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $horarios->firstItem() }}-{{ $horarios->lastItem() }} de {{ $horarios->total() }}
                </small>
                {{ $horarios->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
