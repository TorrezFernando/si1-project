<?php

namespace App\Http\Controllers\Admin; // Nota que este sí dice Admin

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Configuracion institucional: datos generales del Colegio "Los Angeles" usados por el sistema.
class ConfiguracionController extends Controller
{
    // Muestra configuracion actual y catalogo de divisas disponible.
    public function index()
    {
        // Valor local por defecto para no depender completamente del servicio externo.
        $divisas = [
            ['name' => 'Boliviano', 'symbol' => 'Bs'],
        ];

        try {
            // Consulta divisas externas para completar opciones de configuracion.
            $response = Http::timeout(5)->withoutVerifying()->get('https://api.hilariweb.com/divisas');

            if ($response->successful()) {
                $divisas = $response->json() ?? [];
            }
        } catch (\Throwable $e) {
            $divisas = [
                ['name' => 'Boliviano', 'symbol' => 'Bs'],
            ];
        }

        // Obtiene la primera configuracion institucional registrada.
        $configuracion = Configuracion::first();
        return view('admin.configuracion.index', compact('configuracion', 'divisas'));
    }
    // Guarda o actualiza la configuracion general del colegio.
    public function store(Request $request)
    {
       // Valida datos institucionales obligatorios y formato del logo.
       request()->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'divisa' => 'required',
            'correo_electronico' => 'required|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg',
        ]);

        // Usa el primer registro como configuracion unica del sistema.
        $configuracion = Configuracion::first();

        // Si todavia no existe configuracion, crea una instancia nueva.
        if (!$configuracion) {
            $configuracion = new Configuracion();
        }

        // Asigna datos institucionales visibles en el panel.
        $configuracion->nombre = $request->nombre;
        $configuracion->descripcion = $request->descripcion;
        $configuracion->direccion = $request->direccion;
        $configuracion->telefono = $request->telefono;
        $configuracion->divisa = $request->divisa;
        $configuracion->correo_electronico = $request->correo_electronico;
        $configuracion->web = $request->web;

        // Si se cargo un logo nuevo, lo mueve a public/uploads/logos y guarda su ruta.
        if($request->hasFile('logo')){
    //Guardar nuevo logo
    $logoPath = $request->file('logo');
    $nombreArchivo = time() . '_' . $logoPath->getClientOriginalName();
    $rutaDestenio = public_path('uploads/logos');
    $logoPath->move($rutaDestenio, $nombreArchivo);
    $configuracion->logo = 'uploads/logos/' . $nombreArchivo;
}

        // Persiste la configuracion institucional.
        $configuracion->save();

        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }
}
