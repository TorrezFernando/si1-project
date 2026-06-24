@extends('adminlte::page')

@section('title', 'Editar Tutor')

@section('content_header')
    <h1>Editar tutor</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-users mr-1"></i> Alumnos vinculados actualmente</h3>
        </div>
        <div class="card-body">
            @forelse ($apoderado->alumnos as $alumno)
                <span class="badge badge-info mr-1 mb-1">
                    {{ trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) }}
                    ({{ $alumno->pivot->descripcion }})
                </span>
            @empty
                <span class="text-muted">Este tutor no tiene alumnos vinculados.</span>
            @endforelse
        </div>
    </div>

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i> Datos actuales del tutor</h3>
        </div>
        <form action="{{ route('admin.apoderados.update', $apoderado) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('admin.apoderados.form')
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.apoderados.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Cancelar</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
@stop
