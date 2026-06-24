<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\BitacoraLogger;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;

// CU06 y CU07: Controlador principal para iniciar y cerrar sesion.
class LoginController extends Controller
{
    // CU06: Limita los intentos fallidos de inicio de sesion.
    protected $maxAttempts = 2;
    // CU06: Tiempo de bloqueo temporal despues de superar los intentos.
    protected $decayMinutes = 1;

    // CU06: Trait de Laravel que contiene la logica base de autenticacion.
    use AuthenticatesUsers;

    // CU06: Ruta por defecto si no se aplica una redireccion personalizada.
    protected $redirectTo = '/home';

    // CU06 y CU07: Inyecta AuthService y define middlewares de invitado/autenticado.
    public function __construct(
        protected AuthService $authService,
    ) {
        // CU06: Solo usuarios no autenticados pueden entrar al login.
        $this->middleware('guest')->except('logout');
        // CU07: Solo usuarios autenticados pueden cerrar sesion.
        $this->middleware('auth')->only('logout');
    }

    // CU06: Indica que el campo usado para iniciar sesion es username.
    public function username()
    {
        return 'username';
    }

    // CU06 y CU01: Permite login con passwords antiguas en texto plano y las migra a Bcrypt.
    protected function attemptLogin(Request $request)
    {
        // CU06: Primero intenta el login normal de Laravel con passwords Bcrypt.
        try {
            if ($this->guard()->attempt($this->credentials($request), $request->boolean('remember'))) {
                return true;
            }
        } catch (RuntimeException $exception) {
            // CU06 y CU01: Si Laravel rechaza un hash antiguo/no Bcrypt, se prueba compatibilidad legacy.
            Log::warning('Login con password legacy detectado', [
                'username' => $request->input($this->username()),
                'mensaje' => $exception->getMessage(),
            ]);
        }

        // CU06 y CU01: Si falla, busca el usuario para revisar si tiene password legacy.
        $user = User::where($this->username(), $request->input($this->username()))->first();

        // CU06: Si no existe usuario, mantiene el fallo normal de autenticacion.
        if (!$user) {
            return false;
        }

        // CU06 y CU01: Solo permite comparacion directa cuando el password guardado no es hash moderno.
        if ($this->passwordEsHashModerno((string) $user->password)) {
            return false;
        }

        // CU06 y CU01: Compara la password enviada contra la password antigua en texto plano.
        if (!hash_equals((string) $user->password, (string) $request->input('password'))) {
            return false;
        }

        // CU01: Migra la password antigua a Bcrypt para que los siguientes ingresos sean seguros.
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // CU06: Inicia sesion manualmente despues de migrar la password.
        $this->guard()->login($user, $request->boolean('remember'));

        // CU06: Indica que la autenticacion fue correcta.
        return true;
    }

    // CU01: Detecta si la password ya esta guardada como Bcrypt o Argon.
    private function passwordEsHashModerno(string $password): bool
    {
        return str_starts_with($password, '$2y$')
            || str_starts_with($password, '$2a$')
            || str_starts_with($password, '$2b$')
            || str_starts_with($password, '$argon');
    }

    // CU06 y CU05: Se ejecuta despues de autenticar y registra el inicio en bitacora.
    protected function authenticated(Request $request, $user)
    {
        // CU06: Marca inicio para medir rendimiento del flujo de login.
        $inicio = microtime(true);

        // CU05 y CU06: Guarda en bitacora el inicio de sesion.
        BitacoraLogger::log($request, 'Inicio de sesion', $user);

        // CU06: Registra en logs tecnicos el tiempo usado por la bitacora.
        Log::info('[PERF] LoginController@authenticated bitacora', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        // CU06: Marca inicio para medir la redireccion por rol.
        $inicioRedirect = microtime(true);
        // CU06: AuthService decide a que modulo entra el usuario segun su rol.
        $redirectTo = $this->authService->redirectAfterLogin($user);

        // CU06: Registra en logs tecnicos la ruta calculada.
        Log::info('[PERF] LoginController@authenticated redirectAfterLogin', [
            'ms' => round((microtime(true) - $inicioRedirect) * 1000, 2),
            'redirect_to' => $redirectTo,
            'id_user' => $user->id_user ?? null,
        ]);

        // CU06: Registra el tiempo total del inicio de sesion.
        Log::info('[PERF] LoginController@authenticated total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        // CU06: Redirige al usuario al modulo correspondiente.
        return redirect($redirectTo);
    }

    // CU07 y CU05: Cierra sesion y registra la accion en bitacora.
    public function logout(Request $request)
    {
        // CU07: Marca inicio para medir el cierre de sesion.
        $inicio = microtime(true);
        // CU07: Recupera el usuario autenticado antes de destruir la sesion.
        $user = $request->user();

        // CU05 y CU07: Si hay usuario, se registra el cierre en bitacora.
        if ($user) {
            // CU07: Marca inicio para medir la escritura de bitacora.
            $inicioBitacora = microtime(true);
            // CU05 y CU07: Guarda el cierre de sesion.
            BitacoraLogger::log($request, 'Cierre de sesion', $user);

            // CU07: Registra en logs tecnicos el tiempo usado por la bitacora.
            Log::info('[PERF] LoginController@logout bitacora', [
                'ms' => round((microtime(true) - $inicioBitacora) * 1000, 2),
                'id_user' => $user->id_user ?? null,
            ]);
        }

        // CU07: Marca inicio para medir la destruccion de sesion.
        $inicioLogout = microtime(true);
        // CU07: Cierra la sesion en el guard de Laravel.
        $this->guard()->logout();
        // CU07: Invalida los datos de sesion del navegador.
        $request->session()->invalidate();
        // CU07: Regenera el token CSRF despues del cierre.
        $request->session()->regenerateToken();

        // CU07: Registra en logs tecnicos el tiempo de cierre de sesion.
        Log::info('[PERF] LoginController@logout sesion', [
            'ms' => round((microtime(true) - $inicioLogout) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        // CU07: Registra el tiempo total del cierre de sesion.
        Log::info('[PERF] LoginController@logout total', [
            'ms' => round((microtime(true) - $inicio) * 1000, 2),
            'id_user' => $user->id_user ?? null,
        ]);

        // CU07: Devuelve al usuario a la pantalla de login.
        return redirect('/login');
    }
}
