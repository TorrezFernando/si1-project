@extends('adminlte::page')

@section('title', 'Nueva Matricula')

@section('content_header')
    <h1><b>Nueva Matricula</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">CU11: Registrar matricula</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.matriculas.store') }}" method="POST">
                @csrf
                @include('admin.matriculas.form')
            </form>
        </div>
    </div>
@stop
