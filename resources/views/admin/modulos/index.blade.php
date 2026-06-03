@extends('adminlte::page')

@section('title', 'Modulos')

@section('content_header')
    <h1>Modulos</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $modulos->total() }} {{ $modulos->total() == 1 ? 'modulo' : 'modulos' }}</h4>
            <p>Gestion de modulos del sistema</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar modulo...">
            </form>
            <a href="{{ route('admin.modulos.create') }}" class="btn-add">
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
                            <th>Descripcion</th>
                            <th style="width: 100px;">Funcionalidades</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modulos as $modulo)
                        <tr>
                            <td><span style="font-weight: 700; color: #1e293b;">{{ $modulo->nombre }}</span></td>
                            <td style="font-size: 0.85rem; color: #64748b;">{{ $modulo->descripcion ?: '—' }}</td>
                            <td class="text-center">
                                <span class="badge-chip">{{ $modulo->funcionalidades_count }}</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.modulos.edit', $modulo) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.modulos.destroy', $modulo) }}"
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
                                    <div class="empty-icon">📦</div>
                                    <h5>Sin modulos</h5>
                                    <p>No se encontraron modulos registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($modulos->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $modulos->firstItem() }}-{{ $modulos->lastItem() }} de {{ $modulos->total() }}
                </small>
                {{ $modulos->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
