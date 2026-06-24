@extends('adminlte::page')

@section('title', 'Años Escolares')

@section('content_header')
    <h1>Años Escolares</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $gestiones->total() }} {{ $gestiones->total() == 1 ? 'gestion' : 'gestiones' }}</h4>
            <p>Administracion de años escolares del colegio</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar año, fecha o estado...">
            </form>
            <a href="{{ route('admin.gestiones.create') }}" class="btn-add">
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
                            <th>Año</th>
                            <th>Fecha inicio</th>
                            <th>Fecha fin</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gestiones as $gestion)
                        <tr>
                            <td><span style="font-weight: 700; color: #1e293b;">{{ $gestion->nombre }}</span></td>
                            <td>{{ optional($gestion->fechainicio)->format('Y-m-d') }}</td>
                            <td>{{ optional($gestion->fechafin)->format('Y-m-d') }}</td>
                            <td>
                                <span class="status-badge {{ $gestion->activo ? 'on' : 'off' }}">
                                    <span class="status-dot"></span>
                                    {{ $gestion->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    @if(!$gestion->activo)
                                    <form action="{{ route('admin.gestiones.activar', $gestion->id_gestion) }}" method="POST" style="display:inline;">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn-icon btn-icon-key" title="Activar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('admin.gestiones.edit', $gestion->id_gestion) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.gestiones.destroy', $gestion->id_gestion) }}"
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
                                    <div class="empty-icon">📅</div>
                                    <h5>Sin gestiones</h5>
                                    <p>No se encontraron años escolares.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($gestiones->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $gestiones->firstItem() }}-{{ $gestiones->lastItem() }} de {{ $gestiones->total() }}
                </small>
                {{ $gestiones->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
