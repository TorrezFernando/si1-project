@extends('adminlte::page')

@section('title', 'Funcionalidades')

@section('content_header')
    <h1>Funcionalidades</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $funcionalidades->total() }} {{ $funcionalidades->total() == 1 ? 'funcionalidad' : 'funcionalidades' }}</h4>
            <p>Gestion de funcionalidades por modulo del sistema</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar funcionalidad...">
            </form>
            <a href="{{ route('admin.funcionalidades.create') }}" class="btn-add">
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
                            <th>Nombre</th>
                            <th>Modulo</th>
                            <th>Descripcion</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funcionalidades as $funcionalidad)
                        <tr>
                            <td><span style="font-weight: 700; color: #1e293b;">{{ $funcionalidad->nombre }}</span></td>
                            <td>
                                <span class="badge-chip">{{ $funcionalidad->modulo->nombre }}</span>
                            </td>
                            <td style="font-size: 0.85rem; color: #64748b;">{{ $funcionalidad->descripcion ?: '—' }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.funcionalidades.edit', $funcionalidad) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.funcionalidades.destroy', $funcionalidad) }}"
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
                                    <div class="empty-icon">🔧</div>
                                    <h5>Sin funcionalidades</h5>
                                    <p>No se encontraron funcionalidades registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($funcionalidades->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $funcionalidades->firstItem() }}-{{ $funcionalidades->lastItem() }} de {{ $funcionalidades->total() }}
                </small>
                {{ $funcionalidades->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
