<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}">

  </head>
  <body class="d-flex justify-content-center align-items-center" style="height: 100vh;">
    <main class="form-signin w-50">
      <div class="container">
        <form method="POST" action="{{ route('register') }}">
          @csrf
          <h1 class="h3 mb-3 m-15 fw-normal text-center">Registrar Cuenta</h1>

          <!-- Campo de nombre -->
          <div class="form-floating mb-3">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            <label for="name">Nombre</label>
            @error('name')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <!-- Campo de correo electrónico -->
          <div class="form-floating mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            <label for="email">Correo Electrónico</label>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <!-- Campo de contraseña -->
          <div class="form-floating mb-3 position-relative">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            <label for="password">Contraseña</label>
            <span toggle="#password" class="fas fa-eye-slash field-icon toggle-password" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></span>
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <!-- Campo de confirmar contraseña -->
          <div class="form-floating mb-3 position-relative">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            <label for="password-confirm">Confirmar Contraseña</label>
            <span toggle="#password-confirm" class="fas fa-eye-slash field-icon toggle-password" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></span>
          </div>


          <!--Campo de recapcha-->
          <div class="form-floating mb-3 position-relative">
              <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            @error('g-recaptcha-response')
              <span class="text-danger"><strong>{{ $message }}</strong></span>
            @enderror
          </div>

        <input type="hidden" name="recaptcha_token" id="recaptcha_token">  

          <!-- Botón de registro -->
          <button type="submit" class="w-100 btn btn-lg btn-outline-primary">
            Registrar
          </button>
        </form>

        <!-- Enlace para iniciar sesión -->
        <div class="mt-3 text-center">
          <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Iniciar sesión</a>
        </div>
      </div>
    </main>

    <script>
      // Mostrar u ocultar la contraseña
      $(".toggle-password").click(function() {
        let passwordField = $($(this).attr("toggle"));
        let type = passwordField.attr("type") === "password" ? "text" : "password";
        passwordField.attr("type", type);
        $(this).toggleClass("fa-eye-slash fa-eye");
      });
    </script>

  </body>
</html>
