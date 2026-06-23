{{-- CU01: Formulario para crear o editar credenciales de usuario. --}}
<div class="form-group">
    <label>Usuario <span class="text-danger">*</span></label>
    <input type="text" name="username" class="form-control" value="{{ old('username', $usuario->username ?? '') }}" maxlength="50" required>
    @error('username') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label>Rol <span class="text-danger">*</span></label>
    <select name="id_rol" class="form-control" required>
        <option value="">Seleccione un rol</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->value }}" @selected((int) old('id_rol', $usuario->id_rol ?? '') === $rol->value)>
                {{ $rol->label() }}
            </option>
        @endforeach
    </select>
    @error('id_rol') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label>Contrasena {{ isset($usuario) ? '' : '*' }}</label>
    <input type="password" name="password" class="form-control" {{ isset($usuario) ? '' : 'required' }}>
    <small class="form-text text-muted">Minimo 8 caracteres, con mayusculas, minusculas, numeros y simbolos.</small>
    @if(isset($usuario))
        <small class="form-text text-muted">Dejar vacio para mantener la contrasena actual.</small>
    @endif
    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label>Confirmar contrasena {{ isset($usuario) ? '' : '*' }}</label>
    <input type="password" name="password_confirmation" class="form-control" {{ isset($usuario) ? '' : 'required' }}>
</div>
