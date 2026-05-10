<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Partida</title>
</head>
<body>
    <h1>Partida — Piso {{ $game->floor ?? 1 }}</h1>

    {{-- TODO: mostrar equipo del jugador, enemigos, opciones de combate --}}

    <form method="POST" action="{{ route('game.exit') }}">
        @csrf
        <button type="submit">Salir (guardar partida)</button>
    </form>

    <form method="POST" action="{{ route('game.finish') }}">
        @csrf
        <button type="submit">Terminar partida</button>
    </form>
</body>
</html>
