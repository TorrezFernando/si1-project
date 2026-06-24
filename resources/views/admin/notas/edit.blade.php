@extends('adminlte::page')

@section('title', 'Editar Nota')

@section('content_header')
    <h1><b>Editar Nota</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Modificar calificaci&oacute;n registrada</h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-info">
                        <strong>{{ $nota->alumno }}</strong><br>
                        {{ $nota->curso }} - {{ $nota->materia }} - {{ $nota->gestion }} - {{ $nota->trimestre }}
                    </div>

                    <form action="{{ route('admin.notas.update', [$nota->id_alumno, $nota->id_materia, $nota->id_gestion, $nota->id_curso, $nota->id_trimestre]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ser <span class="text-danger">*</span></label>
                                    <input type="number" name="ser" class="form-control nota-input" min="0" max="100" value="{{ old('ser', $nota->ser) }}" required>
                                    @error('ser')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Saber <span class="text-danger">*</span></label>
                                    <input type="number" name="saber" class="form-control nota-input" min="0" max="100" value="{{ old('saber', $nota->saber) }}" required>
                                    @error('saber')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hacer <span class="text-danger">*</span></label>
                                    <input type="number" name="hacer" class="form-control nota-input" min="0" max="100" value="{{ old('hacer', $nota->hacer) }}" required>
                                    @error('hacer')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Autoevaluaci&oacute;n <span class="text-danger">*</span></label>
                                    <input type="number" name="autoevaluacion" class="form-control nota-input" min="0" max="100" value="{{ old('autoevaluacion', $nota->autoevaluacion) }}" required>
                                    @error('autoevaluacion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripci&oacute;n</label>
                            <input type="text" name="descripcion" class="form-control" maxlength="100" value="{{ old('descripcion', $nota->descripcion) }}">
                            @error('descripcion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            Promedio estimado: <strong id="promedio-preview">0.00</strong>
                        </div>

                        <a href="{{ route('admin.notas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Actualizar Nota
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
