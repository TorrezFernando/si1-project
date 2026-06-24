@extends('adminlte::page')

@section('title', 'Registrar Nota')

@section('content_header')
    <h1><b>Registrar Nota</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos de la calificaci&oacute;n</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notas.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estudiante <span class="text-danger">*</span></label>
                                    <select name="id_alumno" class="form-control" required>
                                        <option value="">Seleccione un estudiante...</option>
                                        @foreach($alumnos as $alumno)
                                            <option value="{{ $alumno->id_alumno }}" {{ old('id_alumno') == $alumno->id_alumno ? 'selected' : '' }}>
                                                {{ $alumno->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_alumno')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Materia, curso y gesti&oacute;n <span class="text-danger">*</span></label>
                                    <select name="asignacion" class="form-control" required>
                                        <option value="">Seleccione una asignaci&oacute;n...</option>
                                        @foreach($asignaciones as $asignacion)
                                            @php($valor = $asignacion->id_materia . '|' . $asignacion->id_gestion . '|' . $asignacion->id_curso)
                                            <option value="{{ $valor }}" {{ old('asignacion') === $valor ? 'selected' : '' }}>
                                                {{ $asignacion->curso }} - {{ $asignacion->materia }} - {{ $asignacion->gestion }} | Docente: {{ $asignacion->docente }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asignacion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Trimestre <span class="text-danger">*</span></label>
                                    <select name="id_trimestre" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($trimestres as $trimestre)
                                            <option value="{{ $trimestre->id_trimestre }}" {{ old('id_trimestre') == $trimestre->id_trimestre ? 'selected' : '' }}>
                                                Trimestre {{ $trimestre->id_trimestre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_trimestre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Descripci&oacute;n</label>
                                    <input type="text" name="descripcion" class="form-control" maxlength="100" value="{{ old('descripcion') }}" placeholder="Observaci&oacute;n breve de la calificaci&oacute;n">
                                    @error('descripcion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ser <span class="text-danger">*</span></label>
                                    <input type="number" name="ser" class="form-control nota-input" min="0" max="100" value="{{ old('ser') }}" required>
                                    @error('ser')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Saber <span class="text-danger">*</span></label>
                                    <input type="number" name="saber" class="form-control nota-input" min="0" max="100" value="{{ old('saber') }}" required>
                                    @error('saber')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hacer <span class="text-danger">*</span></label>
                                    <input type="number" name="hacer" class="form-control nota-input" min="0" max="100" value="{{ old('hacer') }}" required>
                                    @error('hacer')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Autoevaluaci&oacute;n <span class="text-danger">*</span></label>
                                    <input type="number" name="autoevaluacion" class="form-control nota-input" min="0" max="100" value="{{ old('autoevaluacion') }}" required>
                                    @error('autoevaluacion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            Promedio estimado: <strong id="promedio-preview">0.00</strong>
                        </div>

                        <a href="{{ route('admin.notas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Nota
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    const inputs = document.querySelectorAll('.nota-input');
    const preview = document.getElementById('promedio-preview');

    function actualizarPromedio() {
        let total = 0;
        inputs.forEach((input) => {
            total += Number(input.value || 0);
        });

        preview.textContent = (total / inputs.length).toFixed(2);
    }

    inputs.forEach((input) => input.addEventListener('input', actualizarPromedio));
    actualizarPromedio();
</script>
@stop
