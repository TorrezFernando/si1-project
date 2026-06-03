{{-- Datos personales --}}
<h5 class="mb-3" style="color: #1e40af; font-weight: 700;">
    <i class="fas fa-id-card mr-1"></i> Datos personales
</h5>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $personalAdministrativo->nombre ?? '') }}" required>
            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Apellido paterno <span class="text-danger">*</span></label>
            <input type="text" name="ap_paterno" class="form-control" value="{{ old('ap_paterno', $personalAdministrativo->ap_paterno ?? '') }}" required>
            @error('ap_paterno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Apellido materno</label>
            <input type="text" name="ap_materno" class="form-control" value="{{ old('ap_materno', $personalAdministrativo->ap_materno ?? '') }}">
            @error('ap_materno') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Telefono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $personalAdministrativo->telefono ?? '') }}">
            @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Correo electronico</label>
            <input type="email" name="correo" class="form-control" value="{{ old('correo', $personalAdministrativo->correo ?? '') }}">
            @error('correo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Direccion</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $personalAdministrativo->direccion ?? '') }}">
            @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>
</div>

<hr>
<h5 class="mb-3" style="color: #1e40af; font-weight: 700;">
    <i class="fas fa-key mr-1"></i> Datos de acceso al sistema
</h5>

@if (! isset($personalAdministrativo))
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Usuario <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}" required>
                @error('username') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Confirmar contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Usuario <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', optional($personalAdministrativo->usuario)->username) }}" required>
                @error('username') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Nueva contraseña</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                <small class="text-muted">Dejar en blanco para mantener la actual</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>
    </div>
@endif
