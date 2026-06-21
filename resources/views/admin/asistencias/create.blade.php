@extends('adminlte::page')

@section('title', 'Registrar Asistencia')

@section('content_header')
    <h1><b>Registrar Asistencia</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">CU16: Registrar asistencia de estudiantes</h3>
                </div>
                <div class="card-body">
                    <form id="form-asistencia" action="{{ route('admin.asistencias.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Asignacion (Materia / Curso / Gestion) <span class="text-danger">*</span></label>
                                    <select id="asignacion" name="asignacion" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($asignaciones as $a)
                                            <option value="{{ $a->id_materia }}|{{ $a->id_gestion }}|{{ $a->id_curso }}" {{ old('asignacion') === ($a->id_materia . '|' . $a->id_gestion . '|' . $a->id_curso) ? 'selected' : '' }}>
                                                {{ $a->materia }} - {{ $a->curso }} - {{ $a->gestion }} ({{ $a->docente }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asignacion') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fecha <span class="text-danger">*</span></label>
                                    <input type="date" id="fecha" name="fecha" class="form-control"
                                           value="{{ old('fecha', date('Y-m-d')) }}" required>
                                    @error('fecha') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" id="btn-cargar" class="btn btn-info" disabled>
                                    <i class="fas fa-users mr-1"></i> Cargar Estudiantes
                                </button>
                            </div>
                        </div>

                        <hr>
                        <div id="contenedor-alumnos" class="row" style="display: none;">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="data-table" id="tabla-alumnos">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">
                                                    <input type="checkbox" id="check-todos" checked>
                                                </th>
                                                <th>Estudiante</th>
                                                <th style="width: 200px;">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-alumnos"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 text-right mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Guardar Asistencia
                                </button>
                                <a href="{{ route('admin.asistencias.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
let alumnosData = [];

function cargarAlumnos() {
    const asignacion = document.getElementById('asignacion').value;
    const fecha = document.getElementById('fecha').value;
    const tbody = document.getElementById('tbody-alumnos');
    const contenedor = document.getElementById('contenedor-alumnos');

    if (!asignacion || !fecha) {
        Swal.fire({ icon: 'warning', title: 'Seleccione asignacion y fecha' });
        return;
    }

    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3"><i class="fas fa-spinner fa-spin mr-1"></i> Cargando...</td></tr>';
    contenedor.style.display = 'block';

    fetch('{{ route('admin.asistencias.alumnos') }}?asignacion=' + encodeURIComponent(asignacion) + '&fecha=' + encodeURIComponent(fecha))
        .then(r => r.json())
        .then(data => {
            alumnosData = data;
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3">No hay estudiantes matriculados en esta asignacion.</td></tr>';
                return;
            }
            data.forEach(a => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">
                        <input type="checkbox" name="alumnos[${a.id_alumno}][id_alumno]" value="${a.id_alumno}" checked ${a.ya_registrado == 1 ? 'disabled' : ''} class="check-alumno">
                    </td>
                    <td>
                        <span style="font-weight:600;color:#1e293b;">${a.alumno}</span>
                        ${a.ya_registrado == 1 ? '<small class="text-muted ml-2">(ya registrado)</small>' : ''}
                    </td>
                    <td>
                        <select name="alumnos[${a.id_alumno}][estado]" class="form-control">
                            <option value="P" ${a.estado === 'P' ? 'selected' : ''}>Presente</option>
                            <option value="A" ${a.estado === 'A' ? 'selected' : ''}>Ausente</option>
                            <option value="L" ${a.estado === 'L' ? 'selected' : ''}>Tarde</option>
                            <option value="F" ${a.estado === 'F' ? 'selected' : ''}>Falta justificada</option>
                        </select>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-danger">Error al cargar estudiantes.</td></tr>';
        });
}

document.getElementById('asignacion').addEventListener('change', function() {
    document.getElementById('btn-cargar').disabled = !this.value;
});

document.getElementById('btn-cargar').addEventListener('click', cargarAlumnos);

document.getElementById('check-todos').addEventListener('change', function() {
    document.querySelectorAll('.check-alumno:not(:disabled)').forEach(cb => cb.checked = this.checked);
});

document.getElementById('form-asistencia').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.check-alumno:checked');
    if (checked.length === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Seleccione al menos un estudiante' });
    }
});
</script>
@stop
