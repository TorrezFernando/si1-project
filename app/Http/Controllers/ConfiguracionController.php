<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Configuracion institucional: controlador alterno para crear o actualizar datos del colegio.
class ConfiguracionController extends Controller
{
    // Muestra la vista de configuracion general.
    public function index()
    {
        return view('admin.configuracion.index');
    }

    // Guarda configuracion institucional y administra el archivo de logo.
    public function store(Request $request)
    {
        // 1. Buscamos si ya existe una configuración
        $configuracion = \App\Models\Configuracion::first();

        // Si existe configuracion, actualiza el registro unico del sistema.
        if ($configuracion) {
            // --- ACTUALIZAR CONFIGURACIÓN EXISTENTE ---
            $configuracion->nombre = $request->nombre;
            $configuracion->descripcion = $request->descripcion;
            $configuracion->direccion = $request->direccion;
            $configuracion->telefono = $request->telefono;
            $configuracion->divisa = $request->divisa;
            $configuracion->web = $request->web;
            $configuracion->correo_electronico = $request->correo_electronico;

            // Si viene un logo nuevo, reemplaza el logo anterior.
            if($request->hasFile('logo')){
                //Eliminar logo anterior para no llenar el servidor de basura
                if($configuracion->logo && file_exists(public_path($configuracion->logo))){
                    unlink(public_path($configuracion->logo));
                }
                
                $logoPath = $request->file('logo');
                $nombreArchivo = time() . '_' . $logoPath->getClientOriginalName();
                $rutaDestenio = public_path('uploads/logos');
                $logoPath->move($rutaDestenio, $nombreArchivo);
                $configuracion->logo = 'uploads/logos/' . $nombreArchivo;
            }

            $configuracion->save();
            return redirect()->route('admin.configuracion.index')
                ->with('mensaje', 'Configuración actualizada correctamente.')
                ->with('icono', 'success');

        } else {
            // --- CREAR NUEVA CONFIGURACIÓN ---
            $configuracion = new \App\Models\Configuracion();
            $configuracion->nombre = $request->nombre;
            $configuracion->descripcion = $request->descripcion;
            $configuracion->direccion = $request->direccion;
            $configuracion->telefono = $request->telefono;
            $configuracion->divisa = $request->divisa;
            $configuracion->web = $request->web;
            $configuracion->correo_electronico = $request->correo_electronico;

            // Guarda logo inicial si se envio archivo.
            if($request->hasFile('logo')){
                //Guardar nuevo logo
                $logoPath = $request->file('logo');
                $nombreArchivo = time() . '_' . $logoPath->getClientOriginalName();
                $rutaDestenio = public_path('uploads/logos');
                $logoPath->move($rutaDestenio, $nombreArchivo);
                $configuracion->logo = 'uploads/logos/' . $nombreArchivo;
            }

            $configuracion->save();
            return redirect()->route('admin.configuracion.index')
                ->with('mensaje', 'Configuración creada correctamente.')
                ->with('icono', 'success');
        }
    }
}
