@extends('adminlte::page')

@section('title', 'Personal Administrativo')

@section('content_header')
    <h1>Personal Administrativo</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $personal->total() }} {{ $personal->total() == 1 ? 'registro' : 'registros' }}</h4>
            <p>Gestion del personal administrativo del colegio</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar personal...">
            </form>
            <a href="{{ route('admin.personal-administrativo.create') }}" class="btn-add">
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
                            <th style="width: 70px;">ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Contacto</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personal as $item)
                        @php
                            $fullName = trim($item->nombre . ' ' . $item->ap_paterno . ' ' . ($item->ap_materno ?? ''));
                            $initials = strtoupper(substr($item->nombre, 0, 1) . substr($item->ap_paterno, 0, 1));
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $item->id_secretaria }}</span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar" style="background: linear-gradient(135deg, #2563eb, #3b82f6);">{{ $initials }}</div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $fullName }}</div>
                                        <div class="user-detail">{{ $item->correo ?: 'Sin correo' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">
                                    {{ $item->usuario->username ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem; color: #64748b;">
                                    @if($item->telefono)
                                        <i class="fas fa-phone-alt mr-1" style="font-size: 0.7rem;"></i> {{ $item->telefono }}<br>
                                    @endif
                                    <i class="fas fa-map-marker-alt mr-1" style="font-size: 0.7rem;"></i>
                                    {{ $item->direccion ?: 'Sin direccion' }}
                                </div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.personal-administrativo.edit', $item->id_secretaria) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.personal-administrativo.destroy', $item->id_secretaria) }}"
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
                                    <div class="empty-icon">📋</div>
                                    <h5>Sin registros</h5>
                                    <p>No se encontro personal administrativo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($personal->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $personal->firstItem() }}-{{ $personal->lastItem() }} de {{ $personal->total() }}
                </small>
                {{ $personal->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
