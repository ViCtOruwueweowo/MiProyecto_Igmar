<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}">

  </head>
  <body class="d-flex justify-content-center align-items-center" style="height: 100vh;">
    <main class="form-signin w-50">
      <div class="container">
        <!-- Verifica si hay errores y los muestra -->
        @if($errors->any())
          <div class="alert alert-danger">
            <strong>{{$errors->first()}}</strong>
          </div>
        @endif

        <!-- Formulario para enviar OTP -->
        <form method="POST" action="{{ route('2fa') }}">
          @csrf
          <h1 class="h3 mb-3 fw-normal text-center">Registro OTP</h1>

          <p class="mb-3 text-center">Por favor, ingresa el <strong>OTP</strong> generado en tu aplicación Authenticator. <br>
          Asegúrate de enviar el actual, ya que se actualiza cada 30 segundos.</p>

          <!-- Campo para ingresar el OTP -->
          <div class="form-floating mb-3">
            <input id="one_time_password" type="number" class="form-control" name="one_time_password" required autofocus>
            <label for="one_time_password">Contraseña de un solo uso</label>
          </div>

          <!-- Botón para enviar el formulario -->
          <button type="submit" class="w-100 btn btn-lg btn-outline-primary">Enviar</button>
        </form>
      </div>
    </main>
  </body>
</html>
