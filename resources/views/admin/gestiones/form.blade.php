{{-- CU22: El documento pide anio, fecha de inicio, fecha de fin y estado. --}}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Año <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $gestion->nombre ?? '') }}" required>
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de inicio <span class="text-danger">*</span></label>
            <input type="date" name="fechainicio" class="form-control" value="{{ old('fechainicio', isset($gestion) ? optional($gestion->fechainicio)->format('Y-m-d') : '') }}" required>
            @error('fechainicio') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Fecha de fin <span class="text-danger">*</span></label>
            <input type="date" name="fechafin" class="form-control" value="{{ old('fechafin', isset($gestion) ? optional($gestion->fechafin)->format('Y-m-d') : '') }}" required>
            @error('fechafin') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
