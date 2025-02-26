<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class RegisterController extends Controller
{

    use RegistersUsers {
        register as registration;
    }

    /**
     * Ruta a la que se redirige al usuario después del registro.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Crear una nueva instancia del controlador.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Obtener un validador para una solicitud de registro entrante.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:5', 'max:30', 'regex:/^[a-zA-ZÀ-ÿ\s\'-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i'],
            'password' => ['required', 'string', 'min:10', 'confirmed', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&]/', 'regex:/^(?!.*\s).*$/'],
            'g-recaptcha-response' => ['required'],
        ],
    /*
    * Aqui es donde se mostraran los mensajes de error personalizados de registro. 
    */ 
        [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede superar los 30 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras, espacios, apóstrofes y guiones.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.regex' => 'El correo debe tener un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 10 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial.',
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA.',
        ]);
    }

    /**
     * Crear una nueva instancia de usuario después de un registro válido.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'google2fa_secret' => $data['google2fa_secret'],
        ]);
    }

    /**
     * Manejar el registro de un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validar los datos del registro
        $this->validator($request->all())->validate();

        // Validar reCAPTCHA con Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $recaptchaData = $response->json();

        // Verificar si la validación de reCAPTCHA fue exitosa
        if (!$recaptchaData['success']) {
            return back()->withErrors(['g-recaptcha-response' => 'La verificación de reCAPTCHA falló. Inténtalo nuevamente.']);
        }

        // Generar la clave 2FA
        $google2fa = app(Google2FA::class);

        $registration_data = $request->all();
        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();

        // Guardar los datos de registro en la sesión para su posterior uso
        $request->session()->flash('registration_data', $registration_data);

        // Crear el URI del código QR
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $registration_data['email'],
            $registration_data['google2fa_secret']
        );

        // Generar el código QR en formato SVG usando BaconQrCode
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $QR_Image = $writer->writeString($qrCodeUrl);

        // Retornar la vista con el código QR y la clave secreta
        return view('google2fa.register', [
            'QR_Image' => $QR_Image,
            'secret' => $registration_data['google2fa_secret']
        ]);
    }

    /**
     * Completar el proceso de registro después de verificar el 2FA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completeRegistration(Request $request)
    {
        // Obtener los datos de registro de la sesión
        $registrationData = session('registration_data');

        // Verificar si los datos de registro están disponibles
        if (is_null($registrationData)) {
            return redirect()->route('register')->withErrors(['message' => 'Los datos de registro han expirado o no están disponibles.']);
        }

        // Incorporar los datos de registro a la solicitud
        $request->merge($registrationData);

        // Completar el registro utilizando el método de registro original
        return $this->registration($request);
    }
}
