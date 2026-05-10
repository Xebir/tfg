<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú</title>
</head>
<body>
    <h1>Menú principal</h1>

    <form method="POST" action="{{ route('game.start') }}">
        @csrf
        <button type="submit">Nueva partida</button>
    </form>

    {{-- Solo si existe partida guardada --}}
    @if(auth()->user()->game)
        <form method="POST" action="{{ route('game.continue') }}">
            @csrf
            <button type="submit">Continuar partida</button>
        </form>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
