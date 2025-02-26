<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurar Google Authenticator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}">

  </head>
  <body class="d-flex justify-content-center align-items-center" style="height: 100vh;">
    <main class="form-signin w-50">
      <div class="container">
        <!-- Título de la tarjeta -->
        <h4 class="text-center mb-4">Configura Google Authenticator</h4>
        
        <div class="card card-default">
          <div class="card-body" style="text-align: center;">
            <!-- Instrucciones para configurar la autenticación de dos factores -->
            <p>Configura tu autenticación de dos factores escaneando el código de barras a continuación. 
            Alternativamente, puedes usar el código <strong>{{ $secret }}</strong></p>
            
            <!-- Muestra la imagen del código QR -->
            <div class="mb-3">
              {!! $QR_Image !!}
            </div>
            
            <!-- Mensaje de advertencia para el usuario -->
            <p class="mb-4">Debes configurar tu aplicación Google Authenticator antes de continuar. 
            De lo contrario, no podrás iniciar sesión.</p>
            
            <!-- Botón para completar el registro -->
            <a href="{{ route('complete.registration') }}" class="w-100 btn btn-lg btn-outline-primary">Completar Registro</a>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
