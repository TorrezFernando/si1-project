@extends('adminlte::page')

@section('title', 'Alumnos')

@section('content_header')
    <h1>Alumnos</h1>
@stop

@section('content')
    <div class="list-header">
        <div class="list-info">
            <h4>{{ $alumnos->total() }} {{ $alumnos->total() == 1 ? 'alumno' : 'alumnos' }}</h4>
            <p>Gestion de estudiantes registrados en el sistema</p>
        </div>
        <div class="list-toolbar">
            <a href="{{ route('admin.alumnos.create') }}" class="btn-add">
                <i class="fas fa-plus mr-1"></i> Nuevo Alumno
            </a>
        </div>
    </div>

    <div class="card" style="overflow: hidden;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('admin.alumnos.index') }}" method="GET" class="row align-items-end" style="row-gap: 0.5rem;">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Nombre, apellido o CI..." value="{{ $search ?? '' }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-2">
                    <select name="id_curso" class="form-control" style="border-radius: 8px;">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id_curso }}" {{ (int) $idCurso === (int) $curso->id_curso ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="id_materia" class="form-control" style="border-radius: 8px;">
                        <option value="">Todas las materias</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id_materia }}" {{ (int) $idMateria === (int) $materia->id_materia ? 'selected' : '' }}>{{ $materia->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check" style="padding-top: 8px;">
                        <input type="checkbox" name="becados" value="1" id="becados" class="form-check-input" {{ $becados ? 'checked' : '' }}>
                        <label class="form-check-label" for="becados">Solo becados</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search mr-1"></i> Buscar</button>
                    <a href="{{ route('admin.alumnos.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-list mr-1"></i> Limpiar</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Alumno</th>
                            <th style="width: 100px;">CI</th>
                            <th style="width: 90px;">Genero</th>
                            <th>Fecha Nac.</th>
                            <th>Telefono</th>
                            <th>Usuario</th>
                            <th style="width: 100px;">Beca</th>
                            <th style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alumnos as $index => $alumno)
                        @php
                            $fullName = trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . ($alumno->ap_materno ?? ''));
                            $initials = strtoupper(substr($alumno->nombres, 0, 1) . substr($alumno->ap_paterno, 0, 1));
                        @endphp
                        <tr>
                            <td><span class="badge-chip">{{ $alumnos->firstItem() + $index }}</span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar" style="background: linear-gradient(135deg, #2563eb, #3b82f6);">{{ $initials }}</div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $fullName }}</div>
                                        <div class="user-detail">CI: {{ $alumno->ci }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $alumno->ci }}</td>
                            <td><span class="badge-chip">{{ $alumno->genero === 'F' ? 'Femenino' : 'Masculino' }}</span></td>
                            <td style="font-size: 0.85rem;">{{ $alumno->fecha_nac }}</td>
                            <td style="font-size: 0.85rem;">{{ $alumno->telefono ?: '&mdash;' }}</td>
                            <td>
                                <span style="font-weight: 600; color: #1e293b;">
                                    {{ optional($alumno->usuario)->username ?? 'Sin usuario' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($alumno->beca)
                                    <span class="badge badge-success">{{ $alumno->beca->nombre }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.alumnos.edit', $alumno->id_alumno) }}"
                                       class="btn-icon btn-icon-edit" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.alumnos.destroy', $alumno->id_alumno) }}"
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
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">👨‍🎓</div>
                                    <h5>Sin alumnos</h5>
                                    <p>No hay estudiantes registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($alumnos->hasPages())
        <div class="card-footer list-card-footer">
            <div class="list-pagination-bar">
                <small class="pagination-summary">
                    Mostrando {{ $alumnos->firstItem() }}-{{ $alumnos->lastItem() }} de {{ $alumnos->total() }}
                </small>
                {{ $alumnos->links('admin.partials.list-pagination') }}
            </div>
        </div>
        @endif
    </div>
@stop

@section('js')
    @include('admin.partials.crud-alerts')
@stop
