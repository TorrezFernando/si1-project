<?php

namespace App\Http\Controllers;

use App\Domain\GestionAcademica\Services\GestionAcademicaService;
use App\Models\Gestion;
use Illuminate\Http\Request;

// CU22: Controlador para gestionar el anio escolar o gestion academica.
class GestionController extends Controller
{
    // CU22: Inyecta servicio con reglas de gestion academica.
    public function __construct(
        protected GestionAcademicaService $service,
    ) {}

    // CU22: Lista gestiones y permite buscar por anio, fechas o estado.
    public function index(Request $request)
    {
        // CU22: Permite buscar por anio, fechas o estado.
        $search = trim((string) $request->input('search'));
        $gestiones = Gestion::query()
            ->when($search, function ($query) use ($search) {
                $estado = strtolower($search);
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('fechainicio', 'like', "%{$search}%")
                    ->orWhere('fechafin', 'like', "%{$search}%");

                if ($estado === 'activo') {
                    $query->orWhere('activo', true);
                }

                if ($estado === 'inactivo') {
                    $query->orWhere('activo', false);
                }
            })
            ->orderByDesc('nombre')
            ->paginate(15)->appends(['search' => $search]);

        // CU22: Envia gestiones filtradas a la vista administrativa.
        return view('admin.gestiones.index', compact('gestiones', 'search'));
    }

    // CU22: Abre formulario para registrar una nueva gestion.
    public function create()
    {
        return view('admin.gestiones.create');
    }

    // CU22: Guarda una nueva gestion escolar.
    public function store(Request $request)
    {
        // CU22: Valida nombre unico y rango de fechas coherente.
        $request->validate([
            'nombre' => 'required|max:255|unique:gestions',
            'fechainicio' => 'required|date',
            'fechafin' => 'required|date|after:fechainicio',
        ]);

        // CU22: Crea la gestion desde el servicio de dominio.
        $this->service->crearGestion($request->nombre, $request->fechainicio, $request->fechafin);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion creada exitosamente.')
            ->with('icono', 'success');
    }

    public function show(Gestion $gestion)
    {
        //
    }

    // CU22: Abre formulario de edicion de gestion.
    public function edit($id)
    {
        // CU22: Busca gestion por id.
        $gestion = Gestion::find($id);

        // CU22: Si no existe, regresa al listado.
        if (!$gestion) {
            return redirect()->route('admin.gestiones.index')
                ->with('mensaje', 'Gestion no encontrada.')
                ->with('icono', 'error');
        }

        return view('admin.gestiones.edit', compact('gestion'));
    }

    // CU22: Actualiza datos basicos de la gestion.
    public function update(Request $request, $id)
    {
        // CU22: Valida nombre unico y fechas.
        $request->validate([
            'nombre' => 'required|max:255|unique:gestions,nombre,'.$id,
            'fechainicio' => 'required|date',
            'fechafin' => 'required|date|after:fechainicio',
        ]);

        // CU22: Busca y actualiza mediante el servicio.
        $gestion = Gestion::findOrFail($id);
        $this->service->actualizarGestion($gestion, $request->nombre, $request->fechainicio, $request->fechafin);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion actualizada exitosamente.')
            ->with('icono', 'success');
    }

    // CU22: Activa una gestion y desactiva las demas.
    public function activar($id)
    {
        // CU22: Busca la gestion elegida y aplica regla de gestion unica activa.
        $gestion = Gestion::findOrFail($id);
        $this->service->activarGestion($gestion);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion activada exitosamente.')
            ->with('icono', 'success');
    }

    // CU22: Elimina una gestion si no tiene registros academicos relacionados.
    public function destroy($id)
    {
        try {
            // CU22: Busca y elimina mediante el servicio.
            $gestion = Gestion::findOrFail($id);
            $this->service->eliminarGestion($gestion);
        } catch (\Illuminate\Database\QueryException $e) {
            // CU22: Protege notas, cursos u horarios que dependan de esta gestion.
            return redirect()->route('admin.gestiones.index')
                ->with('mensaje', 'No se puede eliminar la gestion porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestion eliminada exitosamente.')
            ->with('icono', 'success');
    }
}
