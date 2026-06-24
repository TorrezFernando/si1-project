@extends('adminlte::page')

@section('title', 'Nueva Funcionalidad')

@section('content_header')
    <h1>Nueva Funcionalidad</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Datos de la funcionalidad</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.funcionalidades.store') }}" method="POST">
                @csrf
                @include('admin.funcionalidades.form')
                <div class="mt-3">
                    <a href="{{ route('admin.funcionalidades.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
@stop
