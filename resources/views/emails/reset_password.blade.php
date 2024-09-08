<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h1>¡Hola!</h1>
    <p>Has solicitado restablecer tu contraseña. </p>
    <p> Haz clic en el siguiente enlace para cambiarla:</p>


    <a href="http://localhost:3000/recuperacion/{{ $token }}" style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none;">
        Restablecer Contraseña
    </a>

    <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>

    <p>Gracias,</p>
    <p>El equipo de {{ config('app.name') }}</p>
</body>
</html>
