@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Usuarios</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $usuarios->total() }} {{ $usuarios->total() == 1 ? 'usuario' : 'usuarios' }}</h4>
            <p>Gestion de credenciales de acceso al sistema</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar usuario, id o rol...">
            </form>
            <a href="{{ route('admin.usuarios.create') }}" class="btn-add">
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
                            <th style="width: 90px;">ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td><span class="badge-chip">{{ $usuario->id_user }}</span></td>
                            <td><span style="font-weight: 700; color: #1e293b;">{{ $usuario->username }}</span></td>
                            <td>{{ $usuario->rol_nombre }}</td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}"
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
                                    <div class="empty-icon"><i class="fas fa-users"></i></div>
                                    <h5>Sin usuarios</h5>
                                    <p>No se encontraron usuarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($usuarios->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $usuarios->firstItem() }}-{{ $usuarios->lastItem() }} de {{ $usuarios->total() }}
                </small>
                {{ $usuarios->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
