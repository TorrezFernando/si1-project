<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

// CU08: Controlador para cambiar la contrasena del usuario autenticado desde configuracion.
class CambiarPasswordController extends Controller
{
    // CU08: Muestra el formulario de cambio de contrasena.
    public function edit()
    {
        return view('admin.configuracion.password');
    }

    // CU08: Valida contrasena actual y guarda una nueva contrasena segura.
    public function update(Request $request)
    {
        // CU08: Exige contrasena actual correcta y nueva contrasena confirmada.
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required', 
                'confirmed', 
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ]);

        // CU08: Obtiene usuario en sesion y guarda la contrasena encriptada.
        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.configuracion.index')->with('mensaje', 'Contraseña actualizada correctamente')->with('icono', 'success');
    }
}
