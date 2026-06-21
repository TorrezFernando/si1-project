@extends('adminlte::page')

@section('title', 'Becas')

@section('content_header')
    <h1>Becas</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $becas->total() }} {{ $becas->total() == 1 ? 'beca' : 'becas' }}</h4>
            <p>Gestion de tipos de beca y descuentos para estudiantes</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.becas.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nueva Beca
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.becas.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Nombre, descripcion o porcentaje..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.becas.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Todo</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nombre</th>
                            <th>Descripcion</th>
                            <th style="width: 100px;">Descuento</th>
                            <th style="width: 90px;" class="text-center">Tipo</th>
                            <th style="width: 80px;">Activo</th>
                            <th style="width: 70px;">Asignados</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($becas as $beca)
                        <tr>
                            <td><span class="badge-chip">{{ $beca->id_beca }}</span></td>
                            <td><span style="font-weight: 600; color: #1e293b;">{{ $beca->nombre }}</span></td>
                            <td>{{ $beca->descripcion }}</td>
                            <td class="text-center">
                                <span class="badge-chip" style="background: #e0f2fe; color: #0369a1;">
                                    {{ $beca->porcentaje }}%
                                </span>
                            </td>
                            <td class="text-center">
                                @if($beca->admin_only)
                                    <span class="badge badge-warning">Admin</span>
                                @else
                                    <span class="badge badge-info">General</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($beca->activo)
                                    <span class="status-badge on">
                                        <span class="status-dot"></span> Si
                                    </span>
                                @else
                                    <span class="status-badge off">
                                        <span class="status-dot"></span> No
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">{{ $beca->alumnos_asignados ?? 0 }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.becas.edit', $beca->id_beca) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.becas.destroy', $beca->id_beca) }}"
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
                                    <div class="empty-icon">🏅</div>
                                    <h5>Sin becas</h5>
                                    <p>No se encontraron tipos de beca registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($becas->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $becas->firstItem() }}-{{ $becas->lastItem() }} de {{ $becas->total() }}
                </small>
                {{ $becas->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
