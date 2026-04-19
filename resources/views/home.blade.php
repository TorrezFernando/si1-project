@extends('adminlte::page')

@section('title', 'Panel de Control')

@section('content_header')
    <h1>Datos del sistema </h1>
@stop

@section('content')
    <p>Bienvenido a la seccion de configuracion del sistema.</p>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Configuración del sistema</h3>
        </div>
        <div class="card-body">
            <p>En esta seccion puedes configurar las opciones del sistema.</p>
            <ul>
                @if ($configuracion)
                    <ul>
                        <li><strong>Nombre del sistema:</strong> {{ $configuracion->nombre }}</li>
                        <li><strong>Descripción:</strong> {{ $configuracion->descripcion }}</li>
                        <li><strong>Versión:</strong> {{ $configuracion->version ?? '1.0' }}</li>
                        <li><strong>Fecha de creación:</strong> {{ $configuracion->created_at }}</li>
                    </ul>
                @else
                    <p>Aún no has configurado los datos del sistema.</p>
                    <a href="{{ url('/admin/configuracion') }}" class="btn btn-primary">Ir a Configuración</a>
                @endif
            </ul>
        </div>
    </div>
@stop

@section('css')
    {{-- Aquí pondremos estilos más adelante si lo necesitas --}}
@stop

@section('js')
    <script>
        console.log("AdminLTE cargado correctamente");
    </script>
@stop
