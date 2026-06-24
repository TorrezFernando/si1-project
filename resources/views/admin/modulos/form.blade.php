{{-- CU10: Formulario para crear o editar modulos del sistema. --}}
<div class="form-group">
    <label>Nombre <span class="text-danger">*</span></label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $modulo->nombre ?? '') }}" required>
    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
</div>
<div class="form-group">
    <label>Descripcion</label>
    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $modulo->descripcion ?? '') }}</textarea>
    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
</div>
