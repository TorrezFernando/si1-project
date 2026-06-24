@extends('adminlte::page')

@section('title', 'Nuevo Tutor')

@section('content_header')
    <h1>Nuevo tutor</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-1"></i> Buscar estudiantes existentes</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.apoderados.create') }}" method="GET" class="form-inline">
                <input type="text" name="alumno" class="form-control mr-2 mb-2 mb-sm-0" placeholder="CI, nombre o apellido" value="{{ request('alumno') }}">
                <button type="submit" class="btn btn-info mr-2"><i class="fas fa-search"></i> Buscar</button>
                <a href="{{ route('admin.apoderados.create') }}" class="btn btn-secondary"><i class="fas fa-list"></i> Mostrar todos</a>
            </form>
        </div>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-tie mr-1"></i> Datos del tutor</h3>
        </div>
        <form action="{{ route('admin.apoderados.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @include('admin.apoderados.form')
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.apoderados.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar tutor</button>
            </div>
        </form>
    </div>
@stop
