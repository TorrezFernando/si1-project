<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Rol;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $idRol = $request->input('id_rol');

        $bitacoras = Bitacora::with('usuario')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('accion', 'like', "%{$search}%")
                      ->orWhere('ip', 'like', "%{$search}%")
                      ->orWhereHas('usuario', function ($u) use ($search) {
                          $u->where('username', 'like', "%{$search}%");
                      });
                });
            })
            ->when($idRol, function ($query) use ($idRol) {
                $query->whereHas('usuario', function ($u) use ($idRol) {
                    $u->where('id_rol', $idRol);
                });
            })
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));

        $roles = Rol::orderBy('id_rol')->get();

        return view('admin.bitacora.index', compact(
            'bitacoras', 'search', 'idRol', 'roles'
        ));
    }
}
