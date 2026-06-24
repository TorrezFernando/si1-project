<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol as RolEnum;
use App\Http\Controllers\Controller;
use App\Models\Funcionalidad;
use App\Models\Modulo;
use App\Models\Rol;
use Illuminate\Http\Request;

// CU09/CU10: Asigna funcionalidades a roles para controlar permisos dinamicos.
class PermisoRolController extends Controller
{
    // CU09 y CU10: Muestra matriz de permisos por rol, modulo y funcionalidad.
    public function index(Request $request)
    {
        // CU01: Carga roles del sistema y selecciona secretaria como rol por defecto.
        $roles = Rol::orderBy('id_rol')->get();
        $idRol = (int) $request->input('id_rol', RolEnum::SECRETARIA->value);
        $rol = Rol::findOrFail($idRol);

        // CU10 y CU09: Agrupa funcionalidades dentro de cada modulo para la vista.
        $modulos = Modulo::with(['funcionalidades' => fn ($query) => $query->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        // CU09: Obtiene ids de funcionalidades ya asignadas al rol seleccionado.
        $permisosAsignados = $rol->funcionalidades()
            ->pluck('funcionalidades.id_funcionalidad')
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('admin.permisos.index', compact('roles', 'rol', 'modulos', 'permisosAsignados'));
    }

    // CU09: Actualiza funcionalidades permitidas para un rol.
    public function update(Request $request, Rol $rol)
    {
        // CU01 y CU09: El administrador conserva todos los permisos sin configuracion manual.
        if ((int) $rol->id_rol === RolEnum::ADMIN->value) {
            return redirect()->route('admin.permisos.index', ['id_rol' => $rol->id_rol])
                ->with('mensaje', 'El Administrador tiene todos los permisos automaticamente.')
                ->with('icono', 'info');
        }

        // CU09: Valida que cada funcionalidad seleccionada exista.
        $data = $request->validate([
            'funcionalidades' => ['nullable', 'array'],
            'funcionalidades.*' => ['integer', 'exists:funcionalidades,id_funcionalidad'],
        ]);

        // CU09: Sincroniza la tabla pivote rol_funcionalidad con lo elegido en la vista.
        $rol->funcionalidades()->sync($data['funcionalidades'] ?? []);

        return redirect()->route('admin.permisos.index', ['id_rol' => $rol->id_rol])
            ->with('mensaje', 'Permisos actualizados exitosamente.')
            ->with('icono', 'success');
    }
}
