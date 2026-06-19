@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Credenciales de acceso</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.usuarios.form')
                <div class="mt-3">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button class="btn btn-success"><i class="fas fa-save mr-1"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@stop
