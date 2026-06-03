@extends('adminlte::page')

@section('title', 'Editar Datos del Profesor')

@section('content_header')
    <h1>Editar datos del profesor</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-edit mr-1"></i>
                Información personal del profesor
            </h3>
        </div>
        <div class="card-body">

            <form action="{{ route('admin.profesores.updateInfo', $profesor->id_profesor) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Nombre --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombre <b>(*)</b></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   name="nombre" value="{{ old('nombre', $profesor->nombre) }}" required>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Apellido Paterno --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Paterno <b>(*)</b></label>
                            <input type="text" class="form-control @error('ap_paterno') is-invalid @enderror"
                                   name="ap_paterno" value="{{ old('ap_paterno', $profesor->ap_paterno) }}" required>
                            @error('ap_paterno')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Apellido Materno --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Materno</label>
                            <input type="text" class="form-control @error('ap_materno') is-invalid @enderror"
                                   name="ap_materno" value="{{ old('ap_materno', $profesor->ap_materno) }}">
                            @error('ap_materno')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- CI --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>C.I.</label>
                            <input type="text" class="form-control @error('ci') is-invalid @enderror"
                                   name="ci" value="{{ old('ci', $profesor->ci) }}">
                            @error('ci')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Correo --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Correo electrónico</label>
                            <input type="email" class="form-control @error('correo') is-invalid @enderror"
                                   name="correo" value="{{ old('correo', $profesor->correo) }}">
                            @error('correo')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                   name="telefono" value="{{ old('telefono', $profesor->telefono) }}">
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Género --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Género</label>
                            <select name="genero" class="form-control @error('genero') is-invalid @enderror">
                                <option value="">-- Seleccionar --</option>
                                <option value="M" {{ old('genero', $profesor->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('genero', $profesor->genero) === 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('genero')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Fecha de nacimiento --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de nacimiento</label>
                            <input type="date" class="form-control @error('fecha_nac') is-invalid @enderror"
                                   name="fecha_nac" value="{{ old('fecha_nac', $profesor->fecha_nac) }}">
                            @error('fecha_nac')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Dirección --}}
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                                   name="direccion" value="{{ old('direccion', $profesor->direccion) }}">
                            @error('direccion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <a href="{{ route('admin.profesores.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </form>

        </div>
    </div>
@stop

@if (Session::has('mensaje'))
    @section('js')
    <script>
        Swal.fire({
            icon: "{{ Session::get('icono') }}",
            title: "{{ Session::get('mensaje') }}",
            showConfirmButton: false,
            timer: 4000
        });
    </script>
    @endsection
@endif
