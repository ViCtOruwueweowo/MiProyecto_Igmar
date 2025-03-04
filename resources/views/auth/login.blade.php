<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}">
  </head>
  <body class="d-flex justify-content-center align-items-center" style="height: 100vh;">
    <main class="form-signin w-50">
      <div class="container">
        <!-- Formulario de Login -->
        <form id="loginForm" method="POST" action="{{ route('login') }}">
          @csrf
          <h1 class="h3 mb-3 m-15 fw-normal text-center">Iniciar Sesion</h1>
          
          <!-- Campo de correo electrónico -->
          <div class="form-floating mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            <label for="email">Correo Electronico</label>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <!-- Campo de contraseña -->
          <div class="form-floating mb-3">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            <label for="password">Contraseña</label>
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
a
          <!-- Campo de reCAPTCHA -->
          <div class="form-floating mb-3 position-relative">
            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" required></div>
            @error('g-recaptcha-response')
              <span class="text-danger"><strong>{{ $message }}</strong></span>
            @enderror
          </div>

          <button class="w-100 btn btn-lg btn-outline-primary" type="submit">Iniciar Sesion</button>
        </form>
        <div class="mt-3 text-center">
          <a href="{{ route('register') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Registrate Aqui.</a>
        </div>
      </div>
    </main>
  </body>
</html>
