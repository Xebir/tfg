<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    public function action(Request $request)
    {
        $game = Auth::user()->game()->first();
        if (!$game) return response()->json(['redirect' => route('menu')]);

        $validated = $request->validate([
            'skill_id'     => ['required', 'integer'],
            'target_index' => ['required', 'integer', 'min:0'],
            'char_index'   => ['required', 'integer', 'min:0'],
        ]);

        $team    = $game->characters()->where('recruited', true)->with('skills')->get();
        $enemies = session('enemies');

        if (!$enemies || $team->isEmpty()) {
            return response()->json(['redirect' => route('game.show')]);
        }

        $charIndex   = (int) $validated['char_index'];
        $targetIndex = (int) $validated['target_index'];
        $skillId     = (int) $validated['skill_id'];

        $attacker = $team[$charIndex] ?? $team->first();
        $target   = $enemies[$targetIndex] ?? null;
        $skill    = $attacker->skills->firstWhere('id', $skillId);

        if (!$skill || !$target || !$target->alive || !$attacker->alive) {
            return response()->json(['error' => 'invalid_action']);
        }

        $events       = [];
        $aliveEnemies = $enemies->filter(fn($e) => $e->alive);
        $fastestEnemy = $aliveEnemies->sortByDesc('speed')->first();
        $playerFirst  = $attacker->speed >= ($fastestEnemy ? $fastestEnemy->speed : 0);

        if ($playerFirst) {
            $this->doPlayerAttack($events, $attacker, $skill, $target, $targetIndex);
            $aliveEnemies = $enemies->filter(fn($e) => $e->alive);
            if ($aliveEnemies->isNotEmpty()) {
                $this->doEnemyAttacks($events, $aliveEnemies, $team);
            }
        } else {
            $this->doEnemyAttacks($events, $aliveEnemies, $team);
            if ($attacker->alive) {
                $this->doPlayerAttack($events, $attacker, $skill, $target, $targetIndex);
            }
        }

        // Persist updated enemies
        session(['enemies' => $enemies]);

        // All enemies defeated → advance floor
        if ($enemies->every(fn($e) => !$e->alive)) {
            $game->floor++;
            $game->save();
            session()->forget('enemies');
            $events[] = ['type' => 'victory', 'floor' => $game->floor];
            return response()->json(['events' => $events]);
        }

        // All player chars defeated → game over
        if ($team->every(fn($c) => !$c->alive)) {
            $game->characters()->delete();
            $game->delete();
            session()->forget('enemies');
            $events[] = ['type' => 'game_over'];
            return response()->json(['events' => $events]);
        }

        return response()->json(['events' => $events]);
    }

    private function doPlayerAttack(array &$events, $attacker, $skill, $target, int $targetIndex): void
    {
        $dmg = $this->calcDamage(
            $skill->damage, (bool) $skill->damage_type,
            $attacker->special_attack, $attacker->physical_attack,
            $target->special_defense,  $target->physical_defense
        );

        $events[] = ['type' => 'dialog', 'text' => "{$attacker->name} usa {$skill->name}!"];

        $target->hp = max(0, $target->hp - $dmg);
        if ($target->hp <= 0) { $target->alive = false; $target->hp = 0; }

        $events[] = [
            'type'   => 'hp_update', 'entity' => 'enemy',
            'index'  => $targetIndex,
            'hp'     => $target->hp, 'max_hp' => $target->max_hp, 'damage' => $dmg,
        ];

        if (!$target->alive) {
            $events[] = ['type' => 'faint', 'entity' => 'enemy', 'index' => $targetIndex, 'name' => $target->name];
        }
    }

    private function doEnemyAttacks(array &$events, $aliveEnemies, $team): void
    {
        foreach ($aliveEnemies as $eIdx => $enemy) {
            $aliveChars = $team->filter(fn($c) => $c->alive);
            if ($aliveChars->isEmpty()) break;

            $defender   = $aliveChars->random();
            $defIdx     = $team->search(fn($c) => $c->id === $defender->id);
            $enemySkill = ($enemy->skills ?? collect())->isNotEmpty()
                ? $enemy->skills->random()
                : null;

            $eDmg = $enemySkill
                ? $this->calcDamage(
                    $enemySkill->damage, (bool) $enemySkill->damage_type,
                    $enemy->special_attack, $enemy->physical_attack,
                    $defender->special_defense, $defender->physical_defense
                )
                : max(1, $enemy->physical_attack - $defender->physical_defense);

            $events[] = ['type' => 'dialog', 'text' => "{$enemy->name} ataca a {$defender->name}!"];

            $defender->hp = max(0, $defender->hp - $eDmg);
            if ($defender->hp <= 0) { $defender->alive = false; $defender->hp = 0; }

            $events[] = [
                'type'   => 'hp_update', 'entity' => 'player',
                'index'  => $defIdx,
                'hp'     => $defender->hp, 'max_hp' => $defender->max_hp, 'damage' => $eDmg,
            ];

            $defender->save();

            if (!$defender->alive) {
                $events[] = ['type' => 'faint', 'entity' => 'player', 'index' => $defIdx, 'name' => $defender->name];

                $next = $team->filter(fn($c) => $c->alive)->first();
                if ($next) {
                    $nextIdx  = $team->search(fn($c) => $c->id === $next->id);
                    $events[] = ['type' => 'switch', 'char_index' => $nextIdx, 'name' => $next->name];
                }
            }
        }
    }

    private function calcDamage(int $base, bool $special, int $atkSp, int $atkPh, int $defSp, int $defPh): int
    {
        $atk = $special ? $atkSp : $atkPh;
        $def = $special ? $defSp : $defPh;
        return max(1, $base + $atk - $def);
    }
}
