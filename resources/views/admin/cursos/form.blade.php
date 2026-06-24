{{-- CU12: Captura el curso como combinacion unica de grado, nivel, paralelo y turno. --}}
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Grado <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $curso->nombre ?? '') }}" placeholder="Ej: 1ro" required>
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Paralelo <span class="text-danger">*</span></label>
            <select name="id_paralelo" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($paralelos as $paralelo)
                    <option value="{{ $paralelo->id_paralelo }}" {{ (int) old('id_paralelo', $curso->id_paralelo ?? 0) === $paralelo->id_paralelo ? 'selected' : '' }}>{{ $paralelo->descripcion }}</option>
                @endforeach
            </select>
            @error('id_paralelo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
