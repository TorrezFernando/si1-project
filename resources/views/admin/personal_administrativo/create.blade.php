@extends('adminlte::page')

@section('title', 'Nuevo Personal Administrativo')

@section('content_header')
    <h1>Nuevo Personal Administrativo</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-plus mr-1"></i> Datos personales y laborales</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.personal-administrativo.store') }}" method="POST">
                @csrf
                @include('admin.personal_administrativo.form')
                <div class="mt-3">
                    <a href="{{ route('admin.personal-administrativo.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
@stop
