@extends('adminlte::page')

@section('title', 'Añadir Profesor')

@section('content_header')
    <h1>Registrar nuevo profesor</h1>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-plus mr-1"></i>
                Datos del nuevo profesor
            </h3>
        </div>
        <div class="card-body">

            <form action="{{ route('admin.profesores.store') }}" method="POST">
                @csrf

                {{-- ─── Datos personales ─────────────────────────────────────── --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-id-card mr-1"></i> Datos personales</h5>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombre <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Paterno <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('ap_paterno') is-invalid @enderror"
                                   name="ap_paterno" value="{{ old('ap_paterno') }}" required>
                            @error('ap_paterno') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Materno <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('ap_materno') is-invalid @enderror"
                                   name="ap_materno" value="{{ old('ap_materno') }}" required>
                            @error('ap_materno') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>C.I. <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('ci') is-invalid @enderror"
                                   name="ci" value="{{ old('ci') }}" required>
                            @error('ci') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Género <b class="text-danger">(*)</b></label>
                            <select name="genero" class="form-control @error('genero') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('genero') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de Nacimiento <b class="text-danger">(*)</b></label>
                            <input type="date" class="form-control @error('fecha_nac') is-invalid @enderror"
                                   name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                            @error('fecha_nac') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>RDA</label>
                            <input type="text" class="form-control @error('rda') is-invalid @enderror"
                                   name="rda" value="{{ old('rda') }}">
                            @error('rda') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Teléfono <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                   name="telefono" value="{{ old('telefono') }}" required>
                            @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Correo electrónico <b class="text-danger">(*)</b></label>
                            <input type="email" class="form-control @error('correo') is-invalid @enderror"
                                   name="correo" value="{{ old('correo') }}" required>
                            @error('correo') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Dirección <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                                   name="direccion" value="{{ old('direccion') }}" required>
                            @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <hr>

                {{-- ─── Datos de acceso ──────────────────────────────────────── --}}
                <h5 class="mb-3 text-success"><i class="fas fa-key mr-1"></i> Datos de acceso al sistema</h5>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Usuario <b class="text-danger">(*)</b></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username') }}" required>
                            @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contraseña <b class="text-danger">(*)</b></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required>
                            <small class="form-text text-muted">Minimo 8 caracteres, con mayusculas, minusculas, numeros y simbolos.</small>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Confirmar contraseña <b class="text-danger">(*)</b></label>
                            <input type="password" class="form-control"
                                   name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <hr>
                <a href="{{ route('admin.profesores.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Registrar profesor
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
