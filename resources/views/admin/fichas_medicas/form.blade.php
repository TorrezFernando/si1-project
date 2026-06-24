{{-- CU23: Formulario comun para registrar o editar fichas medicas. --}}
@php($fichaActual = $ficha ?? null)

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Estudiante <span class="text-danger">*</span></label>
            <select name="id_alumno" class="form-control @error('id_alumno') is-invalid @enderror" required>
                <option value="">Seleccione estudiante...</option>
                @foreach ($alumnos as $alumno)
                    @php($nombreCompleto = trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno))
                    <option value="{{ $alumno->id_alumno }}" {{ (int) old('id_alumno', $fichaActual->id_alumno ?? 0) === $alumno->id_alumno ? 'selected' : '' }}>
                        {{ $alumno->ci }} - {{ $nombreCompleto }}
                    </option>
                @endforeach
            </select>
            @error('id_alumno') <small class="text-danger">{{ $message }}</small> @enderror
            <small class="form-text text-muted">Solo se listan estudiantes sin ficha medica, salvo el estudiante de la ficha en edicion.</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de sangre <span class="text-danger">*</span></label>
            <select name="tipo_sangre" class="form-control @error('tipo_sangre') is-invalid @enderror" required>
                <option value="">Seleccione...</option>
                @foreach ($tiposSangre as $tipo)
                    <option value="{{ $tipo }}" {{ old('tipo_sangre', $fichaActual->tipo_sangre ?? '') === $tipo ? 'selected' : '' }}>
                        {{ $tipo }}
                    </option>
                @endforeach
            </select>
            @error('tipo_sangre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Contacto de emergencia <span class="text-danger">*</span></label>
            <input type="text"
                   name="contacto_emergencia"
                   class="form-control @error('contacto_emergencia') is-invalid @enderror"
                   value="{{ old('contacto_emergencia', $fichaActual->contacto_emergencia ?? '') }}"
                   placeholder="Nombre completo" required>
            @error('contacto_emergencia') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Telefono de emergencia <span class="text-danger">*</span></label>
            <input type="text"
                   name="telf_emerg"
                   class="form-control @error('telf_emerg') is-invalid @enderror"
                   value="{{ old('telf_emerg', $fichaActual->telf_emerg ?? '') }}"
                   placeholder="Ej: 78000000" required>
            @error('telf_emerg') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label>Alergias</label>
            <textarea name="alergias"
                      class="form-control @error('alergias') is-invalid @enderror"
                      rows="3"
                      placeholder="Registrar alergias conocidas o dejar vacio si no tiene">{{ old('alergias', $fichaActual->alergias ?? '') }}</textarea>
            @error('alergias') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
