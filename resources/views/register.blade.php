<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear cuenta</title>
</head>
<body>
    <h1>Crear cuenta</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        {{-- TODO: campos username, password, confirmar password --}}
    </form>
</body>
</html>
