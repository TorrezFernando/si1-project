@extends('adminlte::page')

@section('title', 'Infraestructura')

@section('content_header')
    <h1>Infraestructura</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $aulas->total() }} {{ $aulas->total() == 1 ? 'ambiente' : 'ambientes' }}</h4>
            <p>Gestion de aulas y recursos fisicos del colegio</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.infraestructura.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nuevo Ambiente
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.infraestructura.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Nombre, capacidad, ubicacion o tipo..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $opcion)
                            <option value="{{ $opcion }}" {{ ($estado ?? '') === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.infraestructura.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Todo</a>
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
                            <th>Tipo</th>
                            <th style="width: 80px;">Capacidad</th>
                            <th>Ubicacion</th>
                            <th style="width: 90px;">Estado</th>
                            <th style="width: 70px;">Horarios</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aulas as $aula)
                        @php
                            $estadoBadge = match($aula->estado) {
                                'Activo' => 'on',
                                'Mantenimiento' => 'off',
                                default => 'off',
                            };
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $aula->id_aula }}</span></td>
                            <td><span style="font-weight: 600; color: #1e293b;">{{ $aula->nombre }}</span></td>
                            <td>{{ $aula->tipo }}</td>
                            <td class="text-center">{{ $aula->capacidad }}</td>
                            <td style="font-size: 0.85rem;">{{ $aula->ubicacion }}</td>
                            <td>
                                <span class="status-badge {{ $estadoBadge }}">
                                    <span class="status-dot"></span>
                                    {{ $aula->estado }}
                                </span>
                            </td>
                            <td class="text-center">{{ $aula->horarios_asignados }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.infraestructura.edit', $aula->id_aula) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.infraestructura.destroy', $aula->id_aula) }}"
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
                                    <div class="empty-icon">🏫</div>
                                    <h5>Sin ambientes</h5>
                                    <p>No se encontraron recursos de infraestructura.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($aulas->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $aulas->firstItem() }}-{{ $aulas->lastItem() }} de {{ $aulas->total() }}
                </small>
                {{ $aulas->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
