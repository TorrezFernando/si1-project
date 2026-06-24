@extends('adminlte::page')

@section('title', 'Editar Curso')

@section('content_header')
    <h1><b>Editar Curso</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title"> Grado, Paralelo </h3></div>
        <div class="card-body">
            <form action="{{ route('admin.cursos.update', $curso->id_curso) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.cursos.form')
                <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar</button>
            </form>
        </div>
    </div>
@stop
