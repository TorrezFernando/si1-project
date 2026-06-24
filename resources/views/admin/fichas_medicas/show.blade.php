@extends('adminlte::page')

@section('title', 'Consultar Ficha Medica')

@section('content_header')
    <h1><b>Consultar ficha medica</b></h1>
    <hr>
@stop

@section('content')
    @php
        $alumno = $ficha->alumno;
        $nombreCompleto = $alumno ? trim($alumno->nombres . ' ' . $alumno->ap_paterno . ' ' . $alumno->ap_materno) : 'Estudiante no encontrado';
    @endphp

    <div class="row">
        <div class="col-lg-5">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-graduate mr-1"></i>
                        Estudiante
                    </h3>
                </div>
                <div class="card-body">
                    <strong>Nombre completo</strong>
                    <p class="text-muted">{{ $nombreCompleto }}</p>

                    <strong>CI</strong>
                    <p class="text-muted">{{ $alumno->ci ?? 'N/A' }}</p>

                    <strong>Fecha de nacimiento</strong>
                    <p class="text-muted mb-0">{{ $alumno->fecha_nac ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-notes-medical mr-1"></i>
                        Informacion medica
                    </h3>
                </div>
                <div class="card-body">
                    <strong>Tipo de sangre</strong>
                    <p class="text-muted"><span class="badge badge-danger">{{ $ficha->tipo_sangre }}</span></p>

                    <strong>Alergias</strong>
                    <p class="text-muted">{{ $ficha->alergias ?: 'Sin alergias registradas' }}</p>

                    <strong>Contacto de emergencia</strong>
                    <p class="text-muted">{{ $ficha->contacto_emergencia }}</p>

                    <strong>Telefono de emergencia</strong>
                    <p class="text-muted mb-0">{{ $ficha->telf_emerg }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.fichas-medicas.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('admin.fichas-medicas.edit', $ficha) }}" class="btn btn-success">
                        <i class="fas fa-pencil-alt"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
