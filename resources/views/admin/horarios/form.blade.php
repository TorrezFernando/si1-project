@if($errors->any())
    <div class="alert alert-danger">
        <strong>Revise los datos ingresados.</strong>
    </div>
@endif

@if($modo === 'crear')
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Asignaci&oacute;n docente, materia, curso y gesti&oacute;n <span class="text-danger">*</span></label>
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
        <div class="col-md-4">
            <div class="form-group">
                <label>Paralelo <span class="text-danger">*</span></label>
                <select name="id_paralelo" class="form-control" required>
                    <option value="">Seleccione...</option>
                    @foreach($paralelos as $paralelo)
                        <option value="{{ $paralelo->id_paralelo }}" {{ old('id_paralelo') == $paralelo->id_paralelo ? 'selected' : '' }}>
                            {{ $paralelo->descripcion }}
                        </option>
                    @endforeach
                </select>
                @error('id_paralelo')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
@else
    <div class="callout callout-info">
        <strong>{{ $horario->curso }} - {{ $horario->paralelo }}</strong><br>
        {{ $horario->materia }} | {{ $horario->gestion }} | Docente: {{ $horario->docente }}
        <input type="hidden" name="id_paralelo" value="{{ $horario->id_paralelo }}">
    </div>
@endif

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>D&iacute;a <span class="text-danger">*</span></label>
            <select name="dia" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($dias as $opcion)
                    <option value="{{ $opcion }}" {{ old('dia', $horario->dia ?? '') === $opcion ? 'selected' : '' }}>
                        {{ $opcion }}
                    </option>
                @endforeach
            </select>
            @error('dia')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Hora inicio <span class="text-danger">*</span></label>
            <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio', isset($horario) ? substr($horario->hora_inicio, 0, 5) : '') }}" required>
            @error('hora_inicio')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Hora fin <span class="text-danger">*</span></label>
            <input type="time" name="hora_fin" class="form-control" value="{{ old('hora_fin', isset($horario) ? substr($horario->hora_fin, 0, 5) : '') }}" required>
            @error('hora_fin')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Aula <span class="text-danger">*</span></label>
            <select name="id_aula" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($aulas as $aula)
                    <option value="{{ $aula->id_aula }}" {{ old('id_aula', $horario->id_aula ?? '') == $aula->id_aula ? 'selected' : '' }}>
                        {{ $aula->nombre }} @if(isset($aula->estado)) ({{ $aula->estado }}) @endif
                    </option>
                @endforeach
            </select>
            @error('id_aula')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
