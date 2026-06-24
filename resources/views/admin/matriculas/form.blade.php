@php
    $fecha = old('fecha', isset($matricula) ? $matricula->fecha : now()->toDateString());
    $estadoActual = old('estado', $matricula->estado ?? 'Pendiente');
    $gestionSeleccionada = old('id_gestion', $matricula->id_gestion ?? optional($gestionActiva)->id_gestion);
@endphp

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Estudiante <span class="text-danger">*</span></label>
            <select name="id_alumno" id="id_alumno" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($alumnos as $alumno)
                    <option value="{{ $alumno->id_alumno }}" {{ (int) old('id_alumno', $matricula->id_alumno ?? 0) === (int) $alumno->id_alumno ? 'selected' : '' }}>
                        {{ $alumno->nombre_completo }} - CI: {{ $alumno->ci }}
                    </option>
                @endforeach
            </select>
            @error('id_alumno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Tutor <span class="text-danger">*</span></label>
            <select name="id_apoderado" id="id_apoderado" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($apoderados as $apoderado)
                    <option value="{{ $apoderado->id_apoderado }}" {{ (int) old('id_apoderado', $matricula->id_apoderado ?? 0) === (int) $apoderado->id_apoderado ? 'selected' : '' }}>
                        {{ $apoderado->nombre_completo }}
                    </option>
                @endforeach
            </select>
            @error('id_apoderado') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Gestion <span class="text-danger">*</span></label>
            <select name="id_gestion" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($gestiones as $gestion)
                    <option value="{{ $gestion->id_gestion }}" {{ (int) $gestionSeleccionada === (int) $gestion->id_gestion ? 'selected' : '' }}>
                        {{ $gestion->nombre }}{{ $gestion->activo ? ' (Activa)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('id_gestion') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Curso <span class="text-danger">*</span></label>
            <select name="id_curso" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($cursos as $curso)
                    <option value="{{ $curso->id_curso }}" {{ (int) old('id_curso', $matricula->id_curso ?? 0) === (int) $curso->id_curso ? 'selected' : '' }}>
                        {{ $curso->nombre }}
                    </option>
                @endforeach
            </select>
            @error('id_curso') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Monto matricula <span class="text-danger">*</span></label>
            <input type="number" name="monto" step="0.01" min="0" class="form-control" value="{{ old('monto', $matricula->monto ?? '0.00') }}" required>
            @error('monto') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Fecha <span class="text-danger">*</span></label>
            <input type="date" name="fecha" class="form-control" value="{{ $fecha }}" required>
            @error('fecha') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label>Estado <span class="text-danger">*</span></label>
            <select name="estado" class="form-control" required>
                @foreach($estados as $estado)
                    <option value="{{ $estado }}" {{ $estadoActual === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                @endforeach
            </select>
            @error('estado') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Guardar
    </button>
    <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancelar
    </a>
</div>

@section('js')
<script>
    const parentescos = @json($parentescos);
    const opcionesTutor = Array.from(document.querySelectorAll('#id_apoderado option')).map(option => ({
        value: option.value,
        text: option.textContent,
        selected: option.selected,
    }));

    function filtrarTutores() {
        const alumno = document.getElementById('id_alumno').value;
        const tutor = document.getElementById('id_apoderado');
        const seleccionado = tutor.value;
        const permitidos = parentescos
            .filter(item => String(item.id_alumno) === String(alumno))
            .map(item => String(item.id_apoderado));

        tutor.innerHTML = '';
        opcionesTutor.forEach(option => {
            if (option.value === '' || permitidos.includes(String(option.value))) {
                const nueva = document.createElement('option');
                nueva.value = option.value;
                nueva.textContent = option.text;
                nueva.selected = String(option.value) === String(seleccionado);
                tutor.appendChild(nueva);
            }
        });
    }

    document.getElementById('id_alumno').addEventListener('change', filtrarTutores);
    filtrarTutores();

    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 3500
        });
    @endif
</script>
@stop
