<?php

namespace App\Http\Controllers\Admin;

use App\Domain\GestionAcademica\Services\GestionAcademicaService;
use App\Http\Controllers\Controller;
use App\Models\Nivel;
use Illuminate\Http\Request;

// CU12: Controlador para administrar niveles academicos usados por cursos.
class NivelController extends Controller
{
    // CU12: Inyecta el servicio de gestion academica para operaciones de niveles.
    public function __construct(
        protected GestionAcademicaService $service,
    ) {}

    // CU12: Lista los niveles registrados.
    public function index()
    {
        // CU12: Obtiene niveles desde el servicio del dominio academico.
        $niveles = $this->service->todosLosNiveles();
        return view('admin.niveles.index', compact('niveles'));
    }

    // CU12: Crea un nuevo nivel academico.
    public function store(Request $request)
    {
        // CU12: Valida que el nombre sea obligatorio y no se repita.
        $request->validate([
            'nombre' => 'required|max:255|unique:nivels',
        ]);

        // CU12: Delega la creacion al servicio academico.
        $this->service->crearNivel($request->nombre);

        return redirect()->route('admin.nivels.index')
            ->with('mensaje', 'Nivel creado exitosamente.')
            ->with('icono', 'success');
    }

    // CU12: Actualiza el nombre de un nivel.
    public function update(Request $request, $id)
    {
        // CU12: Valida nombre unico ignorando el nivel actual.
        $request->validate([
            'nombre' => 'required|max:255|unique:nivels,nombre,'.$id,
        ]);

        // CU12: Busca el nivel y delega la actualizacion al servicio.
        $nivel = Nivel::findOrFail($id);
        $this->service->actualizarNivel($nivel, $request->nombre);

        return redirect()->route('admin.nivels.index')
            ->with('mensaje', 'Nivel actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU12: Elimina un nivel si no esta relacionado con cursos.
    public function destroy($id)
    {
        try {
            // CU12: Busca y elimina mediante el servicio academico.
            $nivel = Nivel::findOrFail($id);
            $this->service->eliminarNivel($nivel);
        } catch (\Illuminate\Database\QueryException $e) {
            // CU12: Evita eliminar niveles usados por cursos existentes.
            return redirect()->route('admin.nivels.index')
                ->with('mensaje', 'No se puede eliminar el nivel porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.nivels.index')
            ->with('mensaje', 'Nivel eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
