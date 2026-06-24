@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')

    {{-- Global dashboard styles --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style type="text/css">
    body { font-family: 'Inter', sans-serif; }
    .zoomP{
        transition: width 1.1s, height 1.1s, transform 1.1s;
        -moz-transition: width 1.1s, height 1.1s, -moz-transform 1.1s;
        -webkit-transition: width 1.1s, height 1.1s, -webkit-transform 1.1s;
        -o-transition: width 1.1s, height 1.1s, -o-transform 1.1s;
        border: 1px solid #c0c0c0;
        box-shadow: #c0c0c0 0px 5px 5px 0px;
    }
    .zoomP:hover{
        transform: scale(1.05);
        -webkit-transform:scale(1.05);transform:scale(1.05);
    }
</style>
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Preloader Animation (fullscreen mode) --}}
        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')

    {{-- Global dashboard scripts --}}
    <script src="{{ asset('js/dashboard.js') }}"></script>

@if( (($mensaje = Session::get('mensaje')) && ($icono = Session::get('icono'))) )
    <script>
        Swal.fire({
            position: "top-end",
            icon: "{{$icono}}",
            title: "{{$mensaje}}",
            showConfirmButton: false,
            timer: 4000
        });
    </script>
@endif
@stop
