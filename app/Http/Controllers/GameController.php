<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\GeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function __construct(
        private GeneratorService $generator
    ) {}

    public function menu()
    {
        $user = Auth::user();
        $hasGame = $user->game()->exists();

        return view('game.menu', compact('hasGame'));
    }

    public function start(Request $request)
    {
        $user = Auth::user();

        if ($user->game) {
            $user->game->characters()->delete();
            $user->game->delete();
        }

        $game = Game::create([
            'user_id' => $user->id,
            'floor'   => 1,
        ]);

        $this->generator->generatePlayerTeam($game);

        return redirect()->route('game.show');
    }

    public function continue()
    {
        $user = Auth::user();

        if (!$user->game) {
            return redirect()->route('menu');
        }

        return redirect()->route('game.show');
    }

    public function show()
    {
        $game = Auth::user()->game;

        if (!$game) {
            return redirect()->route('menu');
        }

        return view('game.game', compact('game'));
    }

    public function exit(Request $request)
    {
        Auth::user()->game;

        return redirect()->route('menu');
    }

    public function finish(Request $request)
    {
        $game = Auth::user()->game;

        if ($game) {
            $game->characters()->delete();
            $game->delete();
        }

        return redirect()->route('menu');
    }
}