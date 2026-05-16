<?php

namespace App\Http\Controllers;

use App\Models\Pasive;
use App\Services\GeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    private const CRIT_BASE_CHANCE = 0.10;

    private const CRIT_BASE_MULTIPLIER = 1.5;

    public function __construct(
        private GeneratorService $generator
    ) {}

    public function action(Request $request)
    {
        $game = Auth::user()->game()->first();
        if (! $game) {
            return response()->json(['redirect' => route('menu')], 404);
        }

        $type = $request->input('type');

        $game->load('activeCharacter.skills', 'activeCharacter.pasive');

        $team = $game->characters()
            ->where('recruited', true)
            ->with('skills', 'pasive')
            ->get();

        $activeCharIndex = $request->input('char_index', 0);
        $active = $team->get($activeCharIndex);

        if (! $active || ! $active->alive) {
            $first = $team->where('alive', true)->first();
            if (! $first) {
                return response()->json(['events' => [['type' => 'game_over']]]);
            }
            $activeCharIndex = $team->search(fn ($c) => $c->id === $first->id);
            $active = $first;
            $game->update(['active_character_id' => $active->id]);
        }

        $enemies = session("game_{$game->id}_enemies", []);
        $events = [];

        if ($type === 'skill') {
            $result = $this->handleSkill($request, $active, $enemies, $game, $team, $activeCharIndex, $events);
            if ($result) {
                return $result;
            }
        } elseif ($type === 'switch') {
            $result = $this->handleSwitch($request, $game, $team, $activeCharIndex, $events);
            if ($result) {
                return $result;
            }
        } else {
            return response()->json(['events' => [['type' => 'dialog', 'text' => 'Acción inválida.']]]);
        }

        $this->decrementCooldowns($active);

        $this->enemyTurn($active, $enemies, $game, $team, $activeCharIndex, $events);

        session(["game_{$game->id}_enemies" => $enemies]);

        $active->save();

        $allDead = $team->every(fn ($c) => ! $c->alive);
        if ($allDead) {
            $events[] = ['type' => 'game_over'];
            session()->forget("game_{$game->id}_enemies");
            session()->forget("game_{$game->id}_turn");
            $game->characters()->delete();
            $game->delete();

            return response()->json(['events' => $events]);
        }

        return response()->json(['events' => $events]);
    }

    private function handleSkill(Request $request, $active, array &$enemies, $game, $team, int $activeCharIndex, array &$events): ?JsonResponse
    {
        $skillId = $request->input('skill_id');
        $targetIndex = (int) $request->input('target_index', 0);

        $skill = $active->skills()->where('skill_id', $skillId)->first();
        if (! $skill || $skill->pivot->cooldown > 0) {
            $events[] = ['type' => 'dialog', 'text' => 'Habilidad no disponible.'];

            return response()->json(['events' => $events]);
        }

        $targetIdx = $this->findAliveEnemyIndex($enemies, $targetIndex);
        if ($targetIdx === null) {
            return $this->handleVictory($game, $enemies, $team, $events);
        }

        $target = &$enemies[$targetIdx];
        $result = $this->calculateDamage(
            $active->physical_attack, $active->special_attack, $skill,
            $target['physical_defense'], $target['special_defense'],
            $active->pasive_id
        );
        $damage = $result['damage'];

        $target['hp'] -= $damage;
        $text = "{$active->name} usa {$skill->name}!";
        if ($result['is_crit']) {
            $text .= ' ¡GOLPE CRÍTICO!';
        }
        $events[] = ['type' => 'dialog', 'text' => $text];
        $events[] = [
            'type' => 'hp_update', 'entity' => 'enemy', 'index' => $targetIdx,
            'hp' => max(0, $target['hp']), 'max_hp' => $target['max_hp'],
        ];

        if ($target['hp'] <= 0) {
            $target['hp'] = 0;
            $target['alive'] = false;
            $events[] = ['type' => 'faint', 'entity' => 'enemy', 'index' => $targetIdx, 'name' => $target['name']];
        }
        unset($target);

        $active->skills()->updateExistingPivot($skill->id, ['cooldown' => 1]);
        $active->load('skills');

        if ($this->allEnemiesDead($enemies)) {
            return $this->handleVictory($game, $enemies, $team, $events);
        }

        return null;
    }

    private function handleSwitch(Request $request, $game, $team, int &$activeCharIndex, array &$events): ?JsonResponse
    {
        $charIndex = (int) $request->input('char_index', 0);
        $newChar = $team->get($charIndex);

        if (! $newChar || ! $newChar->alive) {
            $events[] = ['type' => 'dialog', 'text' => 'Personaje inválido.'];

            return response()->json(['events' => $events]);
        }

        $activeCharIndex = $charIndex;
        $game->update(['active_character_id' => $newChar->id]);
        $events[] = ['type' => 'switch', 'char_index' => $charIndex, 'name' => $newChar->name];

        return null;
    }

    private function enemyTurn($active, array &$enemies, $game, $team, int $activeCharIndex, array &$events): void
    {
        $turnCount = session("game_{$game->id}_turn", 0) + 1;
        session(["game_{$game->id}_turn" => $turnCount]);

        $isFinalBoss = $game->floor === 50;

        foreach ($enemies as &$enemy) {
            if (! $enemy['alive']) {
                continue;
            }

            if ($isFinalBoss && $turnCount > 1 && $turnCount % rand(2, 3) === 0) {
                $fallen = $game->characters()
                    ->where('recruited', true)->where('alive', false)
                    ->get();

                if ($fallen->isNotEmpty()) {
                    $summoned = $fallen->random();
                    $enemies[] = [
                        'id' => -$summoned->id,
                        'name' => $summoned->name,
                        'hp' => $summoned->max_hp,
                        'max_hp' => $summoned->max_hp,
                        'physical_attack' => $summoned->physical_attack,
                        'special_attack' => $summoned->special_attack,
                        'physical_defense' => $summoned->physical_defense,
                        'special_defense' => $summoned->special_defense,
                        'speed' => $summoned->speed,
                        'level' => $summoned->level,
                        'alive' => true,
                        'skills' => [],
                    ];
                    $events[] = ['type' => 'dialog', 'text' => "{$enemy['name']} invocó a {$summoned->name} del abismo!"];

                    continue;
                }
            }

            $skillData = $this->pickEnemySkill($enemy);
            if (! $skillData) {
                continue;
            }

            $result = $this->calculateDamage(
                $enemy['physical_attack'], $enemy['special_attack'],
                (object) $skillData,
                $active->physical_defense, $active->special_defense,
                $enemy['pasive_id'] ?? null
            );
            $damage = $result['damage'];

            $active->hp -= $damage;
            $text = "{$enemy['name']} usa {$skillData['name']}!";
            if ($result['is_crit']) {
                $text .= ' ¡GOLPE CRÍTICO!';
            }
            $events[] = ['type' => 'dialog', 'text' => $text];
            $events[] = [
                'type' => 'hp_update', 'entity' => 'player', 'index' => $activeCharIndex,
                'hp' => max(0, $active->hp), 'max_hp' => $active->max_hp,
            ];

            if ($active->hp <= 0) {
                $active->hp = 0;
                $active->alive = false;
                $active->save();
                $events[] = ['type' => 'faint', 'entity' => 'player', 'index' => $activeCharIndex, 'name' => $active->name];
                break;
            }
        }
        unset($enemy);
    }

    private function pickEnemySkill(array $enemy): ?array
    {
        $skills = $enemy['skills'] ?? [];
        if (empty($skills)) {
            return null;
        }

        return $skills[array_rand($skills)];
    }

    private function calculateDamage(int $physAtk, int $specAtk, $skill, int $physDef, int $specDef, ?int $attackerPasiveId = null): array
    {
        $isPhysical = is_object($skill) ? ($skill->damage_type ?? true) : ($skill['damage_type'] ?? true);
        $damage = is_object($skill) ? ($skill->damage ?? 0) : ($skill['damage'] ?? 0);

        if ($isPhysical) {
            $raw = $damage + ($physAtk * 0.5) - ($physDef * 0.3);
        } else {
            $raw = $damage + ($specAtk * 0.5) - ($specDef * 0.3);
        }

        $damage = max(1, (int) round($raw));

        $mods = $this->getCritModifiers($attackerPasiveId);
        $isCrit = (mt_rand() / mt_getrandmax()) < $mods['chance'];

        if ($isCrit) {
            $damage = (int) round($damage * $mods['multiplier']);
        }

        return ['damage' => $damage, 'is_crit' => $isCrit];
    }

    private function getCritModifiers(?int $pasiveId): array
    {
        $chance = self::CRIT_BASE_CHANCE;
        $multiplier = self::CRIT_BASE_MULTIPLIER;

        if ($pasiveId === null) {
            return ['chance' => $chance, 'multiplier' => $multiplier];
        }

        if (! isset($pasives[$pasiveId])) {
            $pasives = Pasive::all();
            $pasive = $pasives->find($pasiveId);
            $name = $pasive ? $pasive->name : null;

            if ($name === 'Precisión') {
                $chance += 0.05;
            } elseif ($name === 'Crítico') {
                $multiplier += 0.15;
            }
        }

        return ['chance' => $chance, 'multiplier' => $multiplier];
    }

    private function decrementCooldowns($character): void
    {
        foreach ($character->skills as $skill) {
            if ($skill->pivot->cooldown > 0) {
                $character->skills()->updateExistingPivot($skill->id, [
                    'cooldown' => $skill->pivot->cooldown - 1,
                ]);
            }
        }
    }

    private function findAliveEnemyIndex(array $enemies, int $preferred): ?int
    {
        if (isset($enemies[$preferred]) && $enemies[$preferred]['alive']) {
            return $preferred;
        }
        foreach ($enemies as $i => $e) {
            if ($e['alive']) {
                return $i;
            }
        }

        return null;
    }

    private function allEnemiesDead(array $enemies): bool
    {
        foreach ($enemies as $e) {
            if ($e['alive']) {
                return false;
            }
        }

        return true;
    }

    private function handleVictory($game, array &$enemies, $team, array &$events): JsonResponse
    {
        $this->healPlayerTeam($game);
        $game->increment('floor');
        $floor = $game->fresh()->floor;

        session(["game_{$game->id}_enemies" => []]);
        session(["game_{$game->id}_turn" => 0]);

        $events[] = ['type' => 'victory', 'floor' => $floor];

        return response()->json(['events' => $events]);
    }

    private function healPlayerTeam($game): void
    {
        $chars = $game->characters()->where('recruited', true)->with('skills')->get();
        foreach ($chars as $c) {
            $c->hp = (int) round($c->max_hp * 0.5);
            $c->alive = true;
            $c->save();

            foreach ($c->skills as $skill) {
                $c->skills()->updateExistingPivot($skill->id, ['cooldown' => 0]);
            }
        }

        $first = $game->characters()->where('recruited', true)->where('alive', true)->first();
        if ($first) {
            $game->update(['active_character_id' => $first->id]);
        }
    }
}
