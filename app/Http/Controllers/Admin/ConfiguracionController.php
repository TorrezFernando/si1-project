<?php

namespace App\Http\Controllers\Admin; // Nota que este sí dice Admin

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {  $jsondata = file_get_contents('https://api.hilariweb.com/divisas');
        $divisas = json_decode($jsondata, true);
        $configuracion = Configuracion::first();
        return view('admin.configuracion.index', compact('configuracion', 'divisas'));
    }
    public function store(Request $request)
    {
       request()->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'divisa' => 'required',
            'correo_electronico' => 'required|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg',
        ]);

        $configuracion = Configuracion::first();

        if (!$configuracion) {
            $configuracion = new Configuracion();
        }

        $configuracion->nombre = $request->nombre;
        $configuracion->descripcion = $request->descripcion;
        $configuracion->direccion = $request->direccion;
        $configuracion->telefono = $request->telefono;
        $configuracion->divisa = $request->divisa;
        $configuracion->correo_electronico = $request->correo_electronico;
        $configuracion->web = $request->web;

        if($request->hasFile('logo')){
    //Guardar nuevo logo
    $logoPath = $request->file('logo');
    $nombreArchivo = time() . '_' . $logoPath->getClientOriginalName();
    $rutaDestenio = public_path('uploads/logos');
    $logoPath->move($rutaDestenio, $nombreArchivo);
    $configuracion->logo = 'uploads/logos/' . $nombreArchivo;
}

        $configuracion->save();

        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }
}