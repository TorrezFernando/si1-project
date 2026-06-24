@extends('adminlte::page')

@section('title', 'Nuevo Curso')

@section('content_header')
    <h1><b>Nuevo Curso</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title"> grado, paralelo</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.cursos.store') }}" method="POST">
                @csrf
                @include('admin.cursos.form')
                <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
@stop
