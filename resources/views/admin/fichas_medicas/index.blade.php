@extends('adminlte::page')

@section('title', 'Fichas Medicas')

@section('content_header')
    <h1>Fichas Medicas</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $fichas->total() }} {{ $fichas->total() == 1 ? 'ficha' : 'fichas' }}</h4>
            <p>Registro medico de estudiantes del colegio</p>
        </div>
        <div class="list-toolbar">
            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar estudiante, sangre, alergias...">
            </form>
            <a href="{{ route('admin.fichas-medicas.index') }}" class="btn btn-sm btn-secondary" style="border-radius: 10px;">
                <i class="fas fa-list mr-1"></i> Todo
            </a>
            <a href="{{ route('admin.fichas-medicas.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nueva
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th style="width: 90px;">CI</th>
                            <th style="width: 90px;">Sangre</th>
                            <th>Alergias</th>
                            <th>Emergencia</th>
                            <th style="width: 130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fichas as $ficha)
                        @php
                            $alumno = $ficha->alumno;
                            $nombreCompleto = $alumno ? trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) : 'N/A';
                        @endphp
                        <tr>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">{{ $nombreCompleto }}</span>
                            </td>
                            <td><span class="badge-chip">{{ $alumno->ci ?? 'N/A' }}</span></td>
                            <td class="text-center">
                                <span class="badge-chip" style="background: #fef2f2; color: #991b1b; font-weight: 700;">
                                    {{ $ficha->tipo_sangre }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">{{ $ficha->alergias ?: 'Sin alergias' }}</td>
                            <td style="font-size: 0.82rem;">
                                <div>{{ $ficha->contacto_emergencia }}</div>
                                <small style="color: #94a3b8;">{{ $ficha->telf_emerg }}</small>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.fichas-medicas.show', $ficha) }}"
                                       class="btn-icon btn-icon-key" title="Consultar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.fichas-medicas.edit', $ficha) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.fichas-medicas.destroy', $ficha) }}"
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
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">🏥</div>
                                    <h5>Sin fichas medicas</h5>
                                    <p>No se encontraron fichas medicas registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($fichas->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $fichas->firstItem() }}-{{ $fichas->lastItem() }} de {{ $fichas->total() }}
                </small>
                {{ $fichas->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
