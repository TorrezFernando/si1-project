@extends('adminlte::page')

@section('title', 'Bitácora')

@section('content_header')
    <h1>Bitácora de Accesos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha y Hora</th>
                        <th>Acción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bitacoras as $bit)
                        <tr>
                            <td>{{ $bit->id_bitacora }}</td>
                            <td>{{ $bit->usuario ? $bit->usuario->username : 'Desconocido' }}</td>
                            <td>{{ $bit->fecha_hora->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $bit->accion }}</td>
                            <td>{{ $bit->ip }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
