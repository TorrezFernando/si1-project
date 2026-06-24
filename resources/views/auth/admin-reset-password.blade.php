@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', 'Restablecer Contraseña')

@section('auth_body')
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('admin.password.reset.submit') }}" method="post">
        @csrf

        <input type="hidden" name="user_id" value="{{ $user_id }}">

        <p class="login-box-msg text-sm text-muted">Ingresa el código de 6 dígitos enviado a tu correo electrónico junto con tu nueva contraseña.</p>

        <div class="input-group mb-3">
            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                   value="{{ old('code') }}" placeholder="Código de Verificación (6 dígitos)" autofocus required autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-hashtag"></span>
                </div>
            </div>
            @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                   placeholder="Nueva Contraseña" required autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control" 
                   placeholder="Confirmar Contraseña" required autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Guardar Nueva Contraseña
                </button>
            </div>
        </div>
    </form>

    <p class="mt-3 mb-1">
        <a href="{{ route('login') }}">Volver al login</a>
    </p>
@stop
