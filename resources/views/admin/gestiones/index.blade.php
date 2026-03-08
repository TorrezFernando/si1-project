@extends('adminlte::page')


@section('content_header')
    <h1><b>Listado de Gestiones Educativas</b> </h1>
    <hr>
    <a href="{{url('admin/gestiones/create')  }}" class="btn btn-primary">Crear Nueva Gestion</a>
    
@stop

@section('content')

    <div class="row">
        @foreach ($gestiones as $gestion)
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box zoomP">
                    <img src="{{ url('/img/calendario.gif') }}" width="70px" alt="">
                    <div class="info-box-content">
                        <span class="info-box-text"><b>Gestiones Educativas</b></span>
                        <span class="info-box-number" style="color: rgb(216, 219, 40);font-size:20pt"> {{ $gestion->nombre }} </span>
                       <div class= "row">
                            <a href ="{{ url('/admin/gestiones/'.$gestion->id.'/edit') }}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Editar</a>
                        
                            <form action="{{ url('/admin/gestiones/'.$gestion->id) }}" method="post" id="miFormulario{{ $gestion->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="preguntar{{ $gestion->id }}(event)">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>

                            
                            <script>
                                function preguntar{{ $gestion->id }}(event) {
                                    event.preventDefault();

                                    Swal.fire({
                                        title: '¿Desea eliminar este registro?',
                                        text: '',
                                        icon: 'question',
                                        showDenyButton: true,
                                        confirmButtonText: 'Eliminar',
                                        confirmButtonColor: '#a5161d',
                                        denyButtonColor: '#270a0a',
                                        denyButtonText: 'Cancelar',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // JavaScript puro para enviar el formulario
                                            document.getElementById('miFormulario{{ $gestion->id }}').submit();
                                        }
                                    });
                                }
                            </script>
                       </div>
                        </div>
                </div>
            </div>
            
        @endforeach
        
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
