@extends('adminlte::page')

@section('title', 'Editar Matricula')

@section('content_header')
    <h1><b>Editar Matricula</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">CU11: Editar matricula</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.matriculas.update', $matricula->id_inscripcion) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.matriculas.form')
            </form>
        </div>
    </div>
@stop
