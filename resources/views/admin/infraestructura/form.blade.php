<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Nombre del ambiente <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" maxlength="100" value="{{ old('nombre', $aula->nombre ?? '') }}" required>
            @error('nombre')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Tipo de ambiente <span class="text-danger">*</span></label>
            <input type="text" name="tipo" class="form-control" maxlength="60" value="{{ old('tipo', $aula->tipo ?? '') }}" placeholder="Aula comun, laboratorio, biblioteca..." required>
            @error('tipo')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Capacidad <span class="text-danger">*</span></label>
            <input type="number" name="capacidad" class="form-control" min="1" max="999" value="{{ old('capacidad', $aula->capacidad ?? 30) }}" required>
            @error('capacidad')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Ubicaci&oacute;n <span class="text-danger">*</span></label>
            <input type="text" name="ubicacion" class="form-control" maxlength="100" value="{{ old('ubicacion', $aula->ubicacion ?? 'Por definir') }}" required>
            @error('ubicacion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Estado <span class="text-danger">*</span></label>
            <select name="estado" class="form-control" required>
                @foreach($estados as $opcion)
                    <option value="{{ $opcion }}" {{ old('estado', $aula->estado ?? 'Activo') === $opcion ? 'selected' : '' }}>
                        {{ $opcion }}
                    </option>
                @endforeach
            </select>
            @error('estado')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
