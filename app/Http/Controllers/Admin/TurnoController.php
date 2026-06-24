<?php

namespace App\Http\Controllers\Admin;

use App\Domain\GestionAcademica\Services\GestionAcademicaService;
use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;

// CU12 y CU14: Controlador para administrar turnos usados por cursos y horarios.
class TurnoController extends Controller
{
    // CU12: Inyecta el servicio academico que concentra operaciones de turnos.
    public function __construct(
        protected GestionAcademicaService $service,
    ) {}

    // CU12: Lista los turnos registrados para la gestion academica.
    public function index()
    {
        // CU12: Obtiene turnos desde el servicio del dominio academico.
        $turnos = $this->service->todosLosTurnos();
        return view('admin.turnos.index', compact('turnos'));
    }

    // CU12: Abre formulario para crear un turno.
    public function create()
    {
        return view('admin.turnos.create');
    }

    // CU12: Guarda un nuevo turno validando que no se duplique.
    public function store(Request $request)
    {
        // CU12: Valida nombre obligatorio y unico para organizar cursos por jornada.
        $request->validate([
            'nombre' => 'required|max:255|unique:turnos',
        ]);

        // CU12: Delega la creacion al servicio de gestion academica.
        $this->service->crearTurno($request->nombre);

        return redirect()->route('admin.turnos.index')
            ->with('mensaje', 'Turno creado exitosamente.')
            ->with('icono', 'success');
    }

    // CU12: Abre formulario de edicion del turno.
    public function edit($id)
    {
        // CU12: Busca el turno por identificador.
        $turno = Turno::find($id);

        // CU12: Si no existe, vuelve al listado con mensaje de error.
        if (!$turno) {
            return redirect()->route('admin.turnos.index')
                ->with('mensaje', 'Turno no encontrado.')
                ->with('icono', 'error');
        }

        return view('admin.turnos.edit', compact('turno'));
    }

    // CU12: Actualiza el nombre del turno.
    public function update(Request $request, $id)
    {
        // CU12: Valida nombre unico ignorando el registro actual.
        $request->validate([
            'nombre' => 'required|max:255|unique:turnos,nombre,'.$id,
        ]);

        // CU12: Busca el turno y delega el cambio al servicio.
        $turno = Turno::findOrFail($id);
        $this->service->actualizarTurno($turno, $request->nombre);

        return redirect()->route('admin.turnos.index')
            ->with('mensaje', 'Turno actualizado exitosamente.')
            ->with('icono', 'success');
    }

    // CU12: Elimina un turno cuando no esta relacionado con otros registros.
    public function destroy($id)
    {
        try {
            // CU12: Busca y elimina mediante el servicio academico.
            $turno = Turno::findOrFail($id);
            $this->service->eliminarTurno($turno);
        } catch (\Illuminate\Database\QueryException $e) {
            // CU12 y CU14: Protege cursos/horarios relacionados con este turno.
            return redirect()->route('admin.turnos.index')
                ->with('mensaje', 'No se puede eliminar el turno porque tiene registros relacionados.')
                ->with('icono', 'error');
        }

        return redirect()->route('admin.turnos.index')
            ->with('mensaje', 'Turno eliminado exitosamente.')
            ->with('icono', 'success');
    }
}
