<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Nombre de la beca <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" maxlength="100"
                   value="{{ old('nombre', $beca->nombre ?? '') }}" required>
            @error('nombre')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Porcentaje de descuento <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="porcentaje" class="form-control" min="0" max="100" step="0.01"
                       value="{{ old('porcentaje', $beca->porcentaje ?? 0) }}" required>
                <div class="input-group-append">
                    <span class="input-group-text">%</span>
                </div>
            </div>
            @error('porcentaje')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Activo</label>
            <select name="activo" class="form-control">
                <option value="1" {{ old('activo', $beca->activo ?? true) == 1 ? 'selected' : '' }}>Si</option>
                <option value="0" {{ old('activo', $beca->activo ?? true) == 0 ? 'selected' : '' }}>No</option>
            </select>
            @error('activo')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label>Descripcion</label>
            <textarea name="descripcion" class="form-control" rows="2" maxlength="255"
                      placeholder="Describa el tipo de beca y sus condiciones...">{{ old('descripcion', $beca->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Tipo de asignacion</label>
            <select name="admin_only" class="form-control">
                <option value="0" {{ old('admin_only', $beca->admin_only ?? false) ? '' : 'selected' }}>General (cualquier rol)</option>
                <option value="1" {{ old('admin_only', $beca->admin_only ?? false) ? 'selected' : '' }}>Solo Admin</option>
            </select>
            @error('admin_only')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
