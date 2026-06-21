@extends('adminlte::page')

@section('title', 'Editar Asistencia')

@section('content_header')
    <h1><b>Editar Asistencia</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $materia }} - {{ $curso }} - {{ $gestion }}
                        <small class="ml-3 text-muted">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</small>
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.asistencias.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id_materia" value="{{ $idMateria }}">
                        <input type="hidden" name="id_gestion" value="{{ $idGestion }}">
                        <input type="hidden" name="id_curso" value="{{ $idCurso }}">
                        <input type="hidden" name="fecha" value="{{ $fecha }}">

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Estudiante</th>
                                        <th style="width: 250px;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alumnos as $i => $a)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>
                                            <span style="font-weight:600;color:#1e293b;">{{ $a->alumno }}</span>
                                        </td>
                                        <td>
                                            <input type="hidden" name="alumnos[{{ $a->id_alumno }}][id_alumno]" value="{{ $a->id_alumno }}">
                                            <select name="alumnos[{{ $a->id_alumno }}][estado]" class="form-control">
                                                <option value="P" {{ ($a->estado ?? '') === 'P' ? 'selected' : '' }}>Presente</option>
                                                <option value="A" {{ ($a->estado ?? '') === 'A' ? 'selected' : '' }}>Ausente</option>
                                                <option value="L" {{ ($a->estado ?? '') === 'L' ? 'selected' : '' }}>Tarde</option>
                                                <option value="F" {{ ($a->estado ?? '') === 'F' ? 'selected' : '' }}>Falta justificada</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">No hay estudiantes en esta asignacion.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-1"></i> Actualizar Asistencia
                            </button>
                            <a href="{{ route('admin.asistencias.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
