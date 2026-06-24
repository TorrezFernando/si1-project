<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

// CU06: Controlador para recuperacion de contrasena del administrador por codigo de correo.
class AdminResetPasswordController extends Controller
{
    // CU06: Muestra formulario para solicitar codigo de recuperacion.
    // Muestra el formulario para pedir el código (Olvidé mi contraseña)
    public function showForgotForm()
    {
        return view('auth.admin-forgot-password');
    }

    // Procesa la petición y envía el código por correo
    // CU06: Valida usuario administrador, genera codigo y lo envia por correo.
    public function sendResetCode(Request $request)
    {
        // CU06: Requiere username para buscar la cuenta administradora.
        $request->validate([
            'username' => 'required|string',
        ]);

        // Verificamos que sea el administrador
        // CU01: Solo usuarios con rol administrador pueden usar este flujo.
        $user = User::where('username', $request->username)->where('id_rol', 1)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'El usuario no existe o no tiene permisos de administrador.']);
        }

        // Generar código de 6 dígitos aleatorio
        // CU06: El codigo temporal se usa como segunda verificacion.
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar el código en Caché por 15 minutos, asociado al ID del admin
        // CU06: Guarda codigo temporal para validarlo luego.
        Cache::put('password_reset_admin_' . $user->id_user, $code, now()->addMinutes(15));

        // Enviar correo al correo especificado en la configuración
        // CU06: Usa correo institucional configurado o un fallback.
        $config = \App\Models\Configuracion::first();
        $adminEmail = $config && $config->correo_electronico ? $config->correo_electronico : 'usotodo629@gmail.com'; 
        
        try {
            // CU06: Envia correo con la clase Mailable.
            Mail::to($adminEmail)->send(new AdminPasswordResetMail($code));
        } catch (\Exception $e) {
            return back()->withErrors(['username' => 'Error al enviar el correo electrónico. Verifica la configuración SMTP.']);
        }

        // Redirigir a la vista de verificación del código
        return redirect()->route('admin.password.reset.form', ['user_id' => $user->id_user])
                         ->with('status', 'Te hemos enviado un código de verificación por correo electrónico.');
    }

    // Muestra el formulario para ingresar el código y nueva contraseña
    // CU06: Muestra formulario para ingresar codigo recibido y nueva contrasena.
    public function showResetForm(Request $request)
    {
        // CU06: Recupera el id del usuario administrador desde la URL.
        $user_id = $request->query('user_id');
        
        // CU06: Sin id no se puede validar codigo, vuelve al inicio del flujo.
        if (!$user_id) {
            return redirect()->route('admin.password.request');
        }

        return view('auth.admin-reset-password', ['user_id' => $user_id]);
    }

    // Valida el código y restablece la contraseña
    // CU06: Valida codigo temporal y restablece la contrasena.
    public function resetPassword(Request $request)
    {
        // CU06: Valida usuario, codigo de 6 digitos y nueva contrasena segura.
        $request->validate([
            'user_id'  => 'required|exists:usuario,id_user',
            'code'     => 'required|string|size:6',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::defaults()
            ],
        ]);

        // CU06: Obtiene el codigo guardado temporalmente.
        $cachedCode = Cache::get('password_reset_admin_' . $request->user_id);

        // CU06: Rechaza codigo incorrecto o expirado.
        if (!$cachedCode || $cachedCode !== $request->code) {
            return back()->withErrors(['code' => 'El código de verificación es inválido o ha expirado.']);
        }

        // Si el código es correcto, actualizar la contraseña
        // CU01 y CU06: Guarda la nueva contrasena cifrada.
        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar el código de la caché
        // CU06: El codigo se borra para que no pueda reutilizarse.
        Cache::forget('password_reset_admin_' . $user->id_user);

        return redirect()->route('login')
                         ->with('status', '¡Tu contraseña ha sido restablecida exitosamente! Ya puedes iniciar sesión.');
    }
}
