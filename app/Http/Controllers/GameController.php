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
        $existingGame = $user->game()->first();

        if ($existingGame) {
            $existingGame->characters()->delete();
            $existingGame->delete();
        }

        $game = Game::create([
            'user_id' => $user->id,
            'floor'   => 1,
        ]);

        $this->generator->generatePlayerTeam($game);
        session()->forget('active_char_index');

        return redirect()->route('game.show');
    }

    public function continue()
    {
        $game = Auth::user()->game()->first();

        if (!$game) {
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

        $team = $game->characters()
            ->where('recruited', true)
            ->with('skills', 'pasive')
            ->get();

        $enemies = session('enemies');
        if (!$enemies) {
            $enemies = $this->generator->generateEnemies($game->floor);
            session(['enemies' => $enemies]);
        }

        $activeCharIndex = (int) session('active_char_index', 0);
        if (!isset($team[$activeCharIndex]) || !$team[$activeCharIndex]->alive) {
            $activeCharIndex = $team->search(fn($c) => $c->alive) ?: 0;
            session(['active_char_index' => $activeCharIndex]);
        }

        return view('game.game', compact('game', 'team', 'enemies', 'activeCharIndex'));
    }

    public function exit(Request $request)
    {
        session()->forget('enemies');
        session()->forget('active_char_index');
        return redirect()->route('menu');
    }

    public function historial()
    {
        // TODO: implementar historial de partidas
        return view('game.historial');
    }

    public function finish(Request $request)
    {
        $game = Auth::user()->game()->first();

        if ($game) {
            $game->characters()->delete();
            $game->delete();
        }

        session()->forget('enemies');
        session()->forget('active_char_index');
        return redirect()->route('menu');
    }
}