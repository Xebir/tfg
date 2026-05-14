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
            'type'         => ['required', 'in:skill,switch'],
            'char_index'   => ['required', 'integer', 'min:0'],
            'skill_id'     => ['nullable', 'integer'],
            'target_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $team    = $game->characters()->where('recruited', true)->with('skills')->get();
        $enemies = session('enemies');

        if (!$enemies || $team->isEmpty()) {
            return response()->json(['redirect' => route('game.show')]);
        }

        $charIndex = (int) $validated['char_index'];
        $events    = [];

        // ── CAMBIO DE PERSONAJE (cuesta un turno) ──────────────────────────
        if ($validated['type'] === 'switch') {
            $newChar = $team[$charIndex] ?? $team->first();

            if (!$newChar->alive) {
                return response()->json(['error' => 'char_dead']);
            }

            $events[] = ['type' => 'switch', 'char_index' => $charIndex, 'name' => $newChar->name];

            $aliveEnemies = $enemies->filter(fn($e) => $e->alive);
            $this->doEnemyAttacks($events, $aliveEnemies, $team, $charIndex);

            session(['enemies' => $enemies]);

            if ($team->every(fn($c) => !$c->alive)) {
                $game->characters()->delete();
                $game->delete();
                session()->forget('enemies');
                session()->forget('active_char_index');
                $events[] = ['type' => 'game_over'];
                return response()->json(['events' => $events]);
            }

            $finalChar = $this->resolveActiveChar($events, $charIndex);
            session(['active_char_index' => $finalChar]);

            return response()->json(['events' => $events]);
        }

        // ── USAR HABILIDAD ─────────────────────────────────────────────────
        $targetIndex = (int) ($validated['target_index'] ?? 0);
        $skillId     = (int) ($validated['skill_id']     ?? 0);

        $attacker = $team[$charIndex] ?? $team->first();
        $target   = $enemies[$targetIndex] ?? null;
        $skill    = $attacker->skills->firstWhere('id', $skillId);

        if (!$skill || !$target || !$target->alive || !$attacker->alive) {
            return response()->json(['error' => 'invalid_action']);
        }

        $aliveEnemies = $enemies->filter(fn($e) => $e->alive);
        $fastestEnemy = $aliveEnemies->sortByDesc('speed')->first();
        $playerFirst  = $attacker->speed >= ($fastestEnemy ? $fastestEnemy->speed : 0);

        if ($playerFirst) {
            $this->doPlayerAttack($events, $attacker, $skill, $target, $targetIndex);
            $aliveEnemies = $enemies->filter(fn($e) => $e->alive);
            if ($aliveEnemies->isNotEmpty()) {
                $this->doEnemyAttacks($events, $aliveEnemies, $team, $charIndex);
            }
        } else {
            $this->doEnemyAttacks($events, $aliveEnemies, $team, $charIndex);
            if ($attacker->alive) {
                $this->doPlayerAttack($events, $attacker, $skill, $target, $targetIndex);
            }
        }

        session(['enemies' => $enemies]);

        // Victoria
        if ($enemies->every(fn($e) => !$e->alive)) {
            $game->floor++;
            $game->save();
            session()->forget('enemies');
            $finalChar = $this->resolveActiveChar($events, $charIndex);
            session(['active_char_index' => $finalChar]);
            $events[] = ['type' => 'victory', 'floor' => $game->floor];
            return response()->json(['events' => $events]);
        }

        // Derrota
        if ($team->every(fn($c) => !$c->alive)) {
            $game->characters()->delete();
            $game->delete();
            session()->forget('enemies');
            session()->forget('active_char_index');
            $events[] = ['type' => 'game_over'];
            return response()->json(['events' => $events]);
        }

        $finalChar = $this->resolveActiveChar($events, $charIndex);
        session(['active_char_index' => $finalChar]);

        return response()->json(['events' => $events]);
    }

    // Devuelve el char_index activo tras procesar todos los eventos (puede haber auto-switch)
    private function resolveActiveChar(array $events, int $default): int
    {
        $last = $default;
        foreach ($events as $ev) {
            if ($ev['type'] === 'switch') $last = $ev['char_index'];
        }
        return $last;
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
            'type'  => 'hp_update', 'entity' => 'enemy',
            'index' => $targetIndex,
            'hp'    => $target->hp, 'max_hp' => $target->max_hp, 'damage' => $dmg,
        ];

        if (!$target->alive) {
            $events[] = ['type' => 'faint', 'entity' => 'enemy', 'index' => $targetIndex, 'name' => $target->name];
        }
    }

    private function doEnemyAttacks(array &$events, $aliveEnemies, $team, int $activeCharIndex): void
    {
        foreach ($aliveEnemies as $eIdx => $enemy) {
            $defender = $team[$activeCharIndex] ?? null;
            if (!$defender || !$defender->alive) break;

            $defIdx = $activeCharIndex;
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
                'type'  => 'hp_update', 'entity' => 'player',
                'index' => $defIdx,
                'hp'    => $defender->hp, 'max_hp' => $defender->max_hp, 'damage' => $eDmg,
            ];

            $defender->save();

            if (!$defender->alive) {
                $events[] = ['type' => 'faint', 'entity' => 'player', 'index' => $defIdx, 'name' => $defender->name];
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
