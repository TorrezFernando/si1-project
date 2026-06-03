@extends('adminlte::page')

@section('title', 'Editar Modulo')

@section('content_header')
    <h1>Editar Modulo</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Datos del modulo</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.modulos.update', $modulo) }}" method="POST">
                @csrf @method('PUT')
                @include('admin.modulos.form')
                <div class="mt-3">
                    <a href="{{ route('admin.modulos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button class="btn btn-success"><i class="fas fa-save mr-1"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@stop
