@extends('adminlte::page')

@section('title', 'Cursos')

@section('content_header')
    <h1>Cursos</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $cursos->total() }} {{ $cursos->total() == 1 ? 'curso' : 'cursos' }}</h4>
            <p>Gestion de cursos y paralelos del colegio</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.cursos.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nuevo Curso
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.cursos.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-4">
                    <select name="curso_id" class="form-control" onchange="this.form.submit()" style="border-radius: 8px;">
                        <option value="">Todos los cursos</option>
                        @foreach($all_cursos as $c)
                            <option value="{{ $c->id_curso }}" {{ (isset($curso_id) && $curso_id == $c->id_curso) ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="paralelo_id" class="form-control" onchange="this.form.submit()" style="border-radius: 8px;">
                        <option value="">Todos los paralelos</option>
                        @foreach($all_paralelos as $p)
                            <option value="{{ $p->id_paralelo }}" {{ (isset($paralelo_id) && $paralelo_id == $p->id_paralelo) ? 'selected' : '' }}>{{ $p->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <a href="{{ route('admin.cursos.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Mostrar todo</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Grado</th>
                            <th>Paralelos</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cursos as $curso)
                        <tr>
                            <td><span class="badge-chip">{{ $curso->id_curso }}</span></td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">
                                    {{ $curso->grado ?? $curso->nombre }}
                                </span>
                            </td>
                            <td>
                                @forelse($curso->paralelos as $paralelo)
                                    <span class="badge-chip">{{ $paralelo->descripcion }}</span>
                                @empty
                                    <span style="color: #cbd5e1;">Sin paralelos</span>
                                @endforelse
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.cursos.edit', $curso->id_curso) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.cursos.destroy', $curso->id_curso) }}"
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
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon">📚</div>
                                    <h5>Sin cursos</h5>
                                    <p>No se encontraron cursos registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($cursos->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $cursos->firstItem() }}-{{ $cursos->lastItem() }} de {{ $cursos->total() }}
                </small>
                {{ $cursos->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
