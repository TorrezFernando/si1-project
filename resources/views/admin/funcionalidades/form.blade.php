{{-- CU09: Formulario para funcionalidades asociadas a un modulo existente. --}}
<div class="form-group">
    <label>Modulo <span class="text-danger">*</span></label>
    <select name="id_modulo" class="form-control" required>
        <option value="">Seleccione un modulo...</option>
        @foreach($modulos as $modulo)
            <option value="{{ $modulo->id_modulo }}" {{ (int) old('id_modulo', $funcionalidad->id_modulo ?? 0) === $modulo->id_modulo ? 'selected' : '' }}>
                {{ $modulo->nombre }}
            </option>
        @endforeach
    </select>
    @error('id_modulo') <small class="text-danger">{{ $message }}</small> @enderror
</div>
<div class="form-group">
    <label>Nombre <span class="text-danger">*</span></label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $funcionalidad->nombre ?? '') }}" required>
    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
</div>
<div class="form-group">
    <label>Descripcion</label>
    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $funcionalidad->descripcion ?? '') }}</textarea>
    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
</div>
