@extends('adminlte::page')

@section('title', 'Editar Acceso - Profesor')

@section('content_header')
    <h1>Configuracion de acceso del profesor</h1>
@stop

@section('content')
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Actualice el acceso del profesor</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Profesor:</strong>
                {{ trim($profesor->nombre . ' ' . $profesor->ap_paterno . ' ' . $profesor->ap_materno) }}
            </div>

            <form action="{{ route('admin.profesores.update', $profesor->id_profesor) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Usuario</label><b> (*)</b>
                            <input type="text" class="form-control" name="username" value="{{ old('username', optional($profesor->usuario)->username ?? 'profesor_' . $profesor->id_profesor) }}" required>
                            @error('username')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nueva contraseña</label>
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Deje este campo vacio para mantener la contraseña actual.</small>
                            @error('password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mt-4 pt-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="puede_ver_horario" name="puede_ver_horario" value="1"
                                    {{ old('puede_ver_horario', optional($profesor->permiso)->puede_ver_horario) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="puede_ver_horario">Habilitar acceso al horario</label>
                            </div>
                            <small class="text-muted">Si lo desactivas, el profesor podra iniciar sesion pero no ver el modulo de horario.</small>
                        </div>
                    </div>
                </div>

                <hr>
                <a href="{{ route('admin.profesores.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    @if (Session::has('mensaje'))
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 4000
        });
    @endif
</script>
@endsection
