@extends('adminlte::page')

@section('title', 'Cambiar Contraseña')

@section('content_header')
    <h1>Cambiar Contraseña</h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Ingrese sus nuevos datos de acceso</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Contraseña Actual</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Nueva Contraseña</label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                            <small class="form-text text-muted">Debe tener mínimo 8 caracteres, contener una mayúscula, minúsculas, números y símbolos especiales.</small>
                            @error('new_password')
                                <small style="color: red">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Confirmar Nueva Contraseña</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                        
                        <hr>
                        <a href="{{ route('admin.configuracion.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Cancelar</a>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
