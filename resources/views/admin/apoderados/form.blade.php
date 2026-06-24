{{-- CU04: Formulario comun para crear o editar tutores/apoderados. --}}
@php
    $apoderadoActual = $apoderado ?? null;
    $alumnosSeleccionados = collect(old('alumnos', $apoderadoActual ? $apoderadoActual->alumnos->pluck('id_alumno')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $descripcionParentesco = old('descripcion_parentesco', optional($apoderadoActual?->alumnos->first()?->pivot)->descripcion ?? 'Tutor');
@endphp

<h5 class="mb-3 text-primary"><i class="fas fa-id-card mr-1"></i> Datos personales</h5>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>CI <span class="text-danger">*</span></label>
            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $apoderadoActual->ci ?? '') }}" required>
            @error('ci') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Nombres <span class="text-danger">*</span></label>
            <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" value="{{ old('nombres', $apoderadoActual->nombres ?? '') }}" required>
            @error('nombres') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Apellido paterno <span class="text-danger">*</span></label>
            <input type="text" name="ap_paterno" class="form-control @error('ap_paterno') is-invalid @enderror" value="{{ old('ap_paterno', $apoderadoActual->ap_paterno ?? '') }}" required>
            @error('ap_paterno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Apellido materno <span class="text-danger">*</span></label>
            <input type="text" name="ap_materno" class="form-control @error('ap_materno') is-invalid @enderror" value="{{ old('ap_materno', $apoderadoActual->ap_materno ?? '') }}" required>
            @error('ap_materno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Genero <span class="text-danger">*</span></label>
            <select name="genero" class="form-control @error('genero') is-invalid @enderror" required>
                <option value="">Seleccione...</option>
                <option value="M" {{ old('genero', $apoderadoActual->genero ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('genero', $apoderadoActual->genero ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
            @error('genero') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Fecha de nacimiento <span class="text-danger">*</span></label>
            <input type="date" name="fecha_nac" class="form-control @error('fecha_nac') is-invalid @enderror" value="{{ old('fecha_nac', $apoderadoActual->fecha_nac ?? '') }}" required>
            @error('fecha_nac') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Ocupacion <span class="text-danger">*</span></label>
            <input type="text" name="ocupacion" class="form-control @error('ocupacion') is-invalid @enderror" value="{{ old('ocupacion', $apoderadoActual->ocupacion ?? '') }}" required>
            @error('ocupacion') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Telefono <span class="text-danger">*</span></label>
            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $apoderadoActual->telefono ?? '') }}" required>
            @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<hr>
<h5 class="mb-3 text-info"><i class="fas fa-user-graduate mr-1"></i> Estudiantes vinculados</h5>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Parentesco <span class="text-danger">*</span></label>
            <input type="text" name="descripcion_parentesco" class="form-control @error('descripcion_parentesco') is-invalid @enderror" value="{{ $descripcionParentesco }}" required>
            @error('descripcion_parentesco') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label>Seleccionar estudiantes <span class="text-danger">*</span></label>
            <div class="border rounded p-2 @error('alumnos') border-danger @enderror" style="max-height: 280px; overflow-y: auto;">
                @forelse ($alumnos as $alumno)
                    @php
                        $nombreCompleto = trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno);
                        $checked = in_array($alumno->id_alumno, $alumnosSeleccionados, true);
                    @endphp
                    <div class="custom-control custom-checkbox py-1">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="alumno_{{ $alumno->id_alumno }}"
                               name="alumnos[]"
                               value="{{ $alumno->id_alumno }}"
                               {{ $checked ? 'checked' : '' }}>
                        <label class="custom-control-label w-100" for="alumno_{{ $alumno->id_alumno }}">
                            <span class="{{ $checked ? 'font-weight-bold text-primary' : '' }}">
                                {{ $alumno->ci }} - {{ $nombreCompleto }}
                            </span>
                            @if ($checked)
                                <span class="badge badge-info ml-1">Vinculado</span>
                            @endif
                        </label>
                    </div>
                @empty
                    <p class="text-muted mb-0">No se encontraron estudiantes.</p>
                @endforelse
            </div>
            @error('alumnos') <small class="text-danger">{{ $message }}</small> @enderror
            @error('alumnos.*') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<hr>
<h5 class="mb-3 text-success"><i class="fas fa-key mr-1"></i> Datos de acceso al sistema</h5>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Usuario <span class="text-danger">*</span></label>
            <input type="text"
                   name="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $usuario->username ?? '') }}"
                   required>
            @error('username') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    @if (! isset($apoderado))
        <div class="col-md-4">
            <div class="form-group">
                <label>Contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                <small class="form-text text-muted">Minimo 8 caracteres, con mayusculas, minusculas, numeros y simbolos.</small>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Confirmar contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
    @else
        <div class="col-md-4">
            <div class="form-group">
                <label>Nueva contraseña</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                <small class="form-text text-muted">Minimo 8 caracteres, con mayusculas, minusculas, numeros y simbolos.</small>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>
    @endif
</div>
