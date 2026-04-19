@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', 'Recuperar Contraseña')

@section('auth_body')
    <p class="login-box-msg">Ingresa el correo al que mandaremos tu nueva contraseña automáticamente (Módulo en desarrollo).</p>
    <form action="#" method="get">
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico">
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="button" onclick="alert('Función de envío de correo en construcción.')" class="btn btn-primary btn-block">Enviar nueva contraseña</button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <p class="my-0 text-center"><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
@stop
