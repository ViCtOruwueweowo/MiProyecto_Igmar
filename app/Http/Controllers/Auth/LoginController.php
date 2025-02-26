<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use \Illuminate\Foundation\Auth\AuthenticatesUsers;
use Anhskohbo\NoCaptcha\Facades\Captcha; // Asegúrate de importar correctamente el facade

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Este controlador maneja la autenticación de los usuarios, la validación
    | de sus credenciales, y la gestión de la sesión de usuario. Utiliza un trait
    | para proporcionar la funcionalidad de inicio de sesión sin requerir código adicional.
    |
    */

    use \Illuminate\Foundation\Auth\AuthenticatesUsers;

    // Redirige a los usuarios autenticados a esta ruta
    protected $redirectTo = '/home';

    /**
     * Crear una nueva instancia del controlador.
     *
     * @return void
     */
    public function __construct()
    {
        // Permite acceso a la ruta de logout solo si el usuario está autenticado
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Obtener un validador para la solicitud de inicio de sesión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'g-recaptcha-response' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA.',
        ]);
    }

    /**
     * Manejar el inicio de sesión de un usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validar los datos de inicio de sesión
        $this->validateLogin($request);

        // Validar reCAPTCHA con Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $recaptchaData = $response->json();

        // Si la verificación de reCAPTCHA falla
        if (!$recaptchaData['success']) {
            return back()->withErrors(['g-recaptcha-response' => 'La verificación de reCAPTCHA falló. Inténtalo nuevamente.']);
        }

        // Intentar autenticar al usuario con sus credenciales
        if (Auth::attempt($request->only('email', 'password'))) {
            // Redirigir a la ruta destino después del inicio de sesión
            return redirect()->intended($this->redirectTo);
        }

        // Si las credenciales no son correctas
        return redirect()->back()
            ->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])
            ->withInput($request->except('password')); // Mantener los valores de los campos excepto la contraseña
    }

    /**
     * Manejar la salida del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario (si usas Laravel Sanctum)
        if (method_exists($request->user(), 'tokens')) {
            $request->user()->tokens()->delete();
        }

        // Cerrar la sesión del usuario
        Auth::logout();

        // Invalidar la sesión
        $request->session()->invalidate();

        // Regenerar el token CSRF para evitar ataques
        $request->session()->regenerateToken();

        // Eliminar todas las cookies de la sesión
        $cookies = $request->cookies;
        foreach ($cookies as $key => $value) {
            Cookie::queue(Cookie::forget($key)); // Borra todas las cookies almacenadas
        }

        // Redirigir a la página de inicio de sesión
        return redirect('/login');
    }

    /**
     * Mostrar el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Retorna la vista del formulario de login
        return view('auth.login');
    }
}
