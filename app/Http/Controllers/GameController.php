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
            session()->forget("game_{$existingGame->id}_enemies");
            session()->forget("game_{$existingGame->id}_turn");
            $existingGame->characters()->delete();
            $existingGame->update(['status' => 'abandoned']);
        }

        $game = Game::create([
            'user_id' => $user->id,
            'floor'   => 1,
            'status'  => 'active',
        ]);

        $this->generator->generatePlayerTeam($game);

        $firstCharacter = $game->characters()
            ->where('recruited', true)->first();
        if ($firstCharacter) {
            $game->update(['active_character_id' => $firstCharacter->id]);
        }

        $enemies = $this->generateEnemiesForFloor(1);
        session(["game_{$game->id}_enemies" => $enemies]);
        session(["game_{$game->id}_turn" => 0]);

        return redirect()->route('game.show');
    }

    public function continue()
    {
        $game = Auth::user()->game()->first();

        if (!$game) {
            return redirect()->route('menu');
        }

        $enemies = session("game_{$game->id}_enemies", []);
        if (empty($enemies)) {
            $enemies = $this->generateEnemiesForFloor($game->floor);
            session(["game_{$game->id}_enemies" => $enemies]);
            session(["game_{$game->id}_turn" => 0]);
        }

        return redirect()->route('game.show');
    }

    public function show()
    {
        $game = Auth::user()->game;

        if (!$game) {
            return redirect()->route('menu');
        }

        $game->load('activeCharacter.skills', 'activeCharacter.pasive');

        $team = $game->characters()
            ->where('recruited', true)
            ->with('skills', 'pasive')
            ->get();

        $activeCharIndex = 0;
        if ($game->active_character_id) {
            foreach ($team as $i => $char) {
                if ($char->id === $game->active_character_id) {
                    $activeCharIndex = $i;
                    break;
                }
            }
        }

        $rawEnemies = session("game_{$game->id}_enemies", []);
        if (empty($rawEnemies)) {
            $rawEnemies = $this->generateEnemiesForFloor($game->floor);
            session(["game_{$game->id}_enemies" => $rawEnemies]);
            session(["game_{$game->id}_turn" => 0]);
        }
        $enemies = collect();
        foreach ($rawEnemies as $e) {
            $enemies->push((object) $e);
        }

        $charColors = [
            ['style' => '--pc:#7c3aed;--pc-dim:#4c1d95', 'hex' => '#7c3aed'],
            ['style' => '--pc:#06b6d4;--pc-dim:#0e7490', 'hex' => '#06b6d4'],
            ['style' => '--pc:#db2777;--pc-dim:#9d174d', 'hex' => '#db2777'],
        ];

        $enemyColors = [
            ['--ec:#ef4444', 'ec' => '#ef4444'],
            ['--ec:#f97316', 'ec' => '#f97316'],
            ['--ec:#a855f7', 'ec' => '#a855f7'],
        ];

        return view('game.game', compact(
            'game', 'enemies', 'team', 'activeCharIndex',
            'charColors', 'enemyColors'
        ));
    }

    public function exit(Request $request)
    {
        return redirect()->route('menu');
    }

    public function historial()
    {
        $games = Auth::user()->games()
            ->where('status', '!=', 'active')
            ->withCount('characters')
            ->get();

        return view('game.historial', compact('games'));
    }

    public function finish(Request $request)
    {
        $game = Auth::user()->game()->first();

        if ($game) {
            session()->forget("game_{$game->id}_enemies");
            session()->forget("game_{$game->id}_turn");
            $game->characters()->delete();
            $game->update(['status' => 'abandoned']);
        }

        return redirect()->route('menu');
    }

    public function generateEnemiesForFloor(int $floor): array
    {
        if (in_array($floor, [10, 20, 30, 40], true)) {
            $enemies = $this->generator->generateMiniboss($floor);
        } elseif ($floor === 50) {
            $enemies = $this->generator->generateFinalBoss($floor);
        } else {
            $enemies = $this->generator->generateEnemies($floor);
        }

        return $enemies->map(function ($e) {
            return [
                'id'               => $e->id,
                'name'             => $e->name,
                'hp'               => $e->hp,
                'max_hp'           => $e->max_hp,
                'physical_attack'  => $e->physical_attack,
                'special_attack'  => $e->special_attack,
                'physical_defense' => $e->physical_defense,
                'special_defense' => $e->special_defense,
                'speed'            => $e->speed,
                'level'            => $e->level,
                'alive'            => $e->alive,
                'skills'           => $e->relationLoaded('skills')
                    ? $e->skills->toArray()
                    : [],
            ];
        })->values()->toArray();
    }
}
