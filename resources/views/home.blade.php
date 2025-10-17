<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>IE. JosÃ© Antonio GalÃ¡n â Inicio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;background:#f6f7fb}
    .card{max-width:560px;margin:8vh auto;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.08);padding:28px}
    .btn{display:inline-block;background:#1a73e8;color:#fff;padding:12px 18px;border-radius:10px;text-decoration:none;font-weight:600}
    .btn:hover{filter:brightness(1.05)}
    .muted{color:#6b7280}
  </style>
</head>
<body>
  <div class="card">
    <h1 style="margin:0 0 8px">Bienvenido</h1>
    <p class="muted" style="margin:0 0 24px">AutentÃ­cate con tu cuenta @josegalan.edu.co para continuar.</p>

    @if (session('error'))
      <div style="background:#fee2e2;color:#991b1b;border-radius:10px;padding:10px 12px;margin-bottom:16px">
        {{ session('error') }}
      </div>
    @endif

    <a class="btn" href="{{ route('google.redirect') }}">Ingresar con Google</a>
  </div>
</body>
</html>
