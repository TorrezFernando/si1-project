@extends('adminlte::page')

@section('title', 'Materias')

@section('content_header')
    <h1>Materias</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $materias->total() }} {{ $materias->total() == 1 ? 'materia' : 'materias' }}</h4>
            <p>Gestion de materias y campos de saberes</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar materia...">
            </form>
            <a href="{{ route('admin.materias.index') }}" class="btn btn-sm btn-secondary" style="border-radius: 10px;">
                <i class="fas fa-list mr-1"></i> Todo
            </a>
            <a href="{{ route('admin.materias.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nueva Materia
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nombre</th>
                            <th>Distintivo</th>
                            <th>Campo de Saberes</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materias as $materia)
                        <tr>
                            <td><span class="badge-chip">{{ $materia->id_materia }}</span></td>
                            <td><span style="font-weight: 600; color: #1e293b;">{{ $materia->nombre }}</span></td>
                            <td>{{ $materia->distintivo ?: '—' }}</td>
                            <td>{{ $materia->campo ? $materia->campo->descripcion : 'N/A' }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.materias.edit', $materia->id_materia) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.materias.destroy', $materia->id_materia) }}"
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
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">📖</div>
                                    <h5>Sin materias</h5>
                                    <p>No se encontraron materias registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($materias->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $materias->firstItem() }}-{{ $materias->lastItem() }} de {{ $materias->total() }}
                </small>
                {{ $materias->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
