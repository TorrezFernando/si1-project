<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 1. Traemos los datos de la tabla configuraciones
        $configuracion = \App\Models\Configuracion::first();

        // 2. Se los enviamos a la vista 'home' usando compact
        return view('home', compact('configuracion'));
    }
}
