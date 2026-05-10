<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function menu()
    {
        // TODO: cargar datos del usuario y su partida guardada (si existe)
        return view('game.menu');
    }

    public function start()
    {
        // TODO: crear nueva Game, llamar a GeneratorService::generatePlayerTeam()
        // redirigir a /game
    }

    public function continue()
    {
        // TODO: verificar que existe partida guardada y redirigir a /game
    }

    public function show()
    {
        // TODO: cargar estado actual de la partida
        return view('game.game');
    }

    public function exit(Request $request)
    {
        // TODO: guardar estado actual (ya persiste en BD, confirmar floor y HP)
        // redirigir a /menu
    }

    public function finish()
    {
        // TODO: marcar partida como terminada o eliminarla
        // redirigir a /menu
    }
}
