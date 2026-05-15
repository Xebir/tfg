<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Game;
use App\Models\Pasive;
use App\Models\Skill;
use Illuminate\Support\Collection;

class GeneratorService
{
    private const BASE_STATS = [
        'min' => 50,
        'max' => 100,
    ];

    private const ENEMY_BASE_STATS = [
        'min' => 30,
        'max' => 60,
    ];

    private const NAMES = [
        'Blaze', 'Frost', 'Volt', 'Terra', 'Aqua', 'Shadow', 'Flare', 'Storm',
        'Ember', 'Glacier', 'Thunder', 'Stone', 'Marine', 'Phantom', 'Inferno',
        'Zephyr', 'Crystal', 'Onyx', 'Ruby', 'Sapphire', 'Emerald', 'Topaz',
    ];

    private const BOSS_NAMES = [
        10 => 'Guardian Carmesí',
        20 => 'Señor del Abismo',
        30 => 'Titán de Sombras',
        40 => 'Dragón Astral',
        50 => 'El Vacío Primordial',
    ];

    public function generatePlayerTeam(Game $game): Collection
    {
        $pasives = Pasive::all();
        $skills = Skill::all();

        $characters = [];
        for ($i = 0; $i < 5; $i++) {
            $isRecruited = $i < 3;

            $stats = $this->generateStats(self::BASE_STATS['min'], self::BASE_STATS['max']);
            $randomSkills = $skills->random(min(4, $skills->count()));
            $randomPasive = $pasives->random();

            $character = Character::create([
                'game_id'        => $game->id,
                'name'           => $this->generateName(),
                'pasive_id'      => $randomPasive->id,
                'hp'             => $stats['hp'],
                'max_hp'         => $stats['hp'],
                'physical_attack'  => $stats['physical_attack'],
                'special_attack'   => $stats['special_attack'],
                'physical_defense' => $stats['physical_defense'],
                'special_defense'   => $stats['special_defense'],
                'speed'          => $stats['speed'],
                'level'          => 1,
                'exp'            => 0,
                'recruited'      => $isRecruited,
                'alive'          => true,
            ]);

            $character->skills()->attach($randomSkills->pluck('id'));
            $characters[] = $character;
        }

        return collect($characters);
    }

    public function generateEnemies(int $floor): Collection
    {
        $pasives = Pasive::all();
        $skills = Skill::all();

        $scalingFactor = 1 + (($floor - 1) * 0.15);
        $minStats = (int) floor(self::ENEMY_BASE_STATS['min'] * $scalingFactor);
        $maxStats = (int) floor(self::ENEMY_BASE_STATS['max'] * $scalingFactor);

        $enemyCount = ($floor <= 3) ? 2 : 3;
        $enemies = [];

        for ($i = 0; $i < $enemyCount; $i++) {
            $enemies[] = $this->makeEnemy($minStats, $maxStats, $pasives, $skills, $floor);
        }

        return collect($enemies);
    }

    public function generateMiniboss(int $floor): Collection
    {
        $pasives = Pasive::all();
        $skills = Skill::all();

        $scalingFactor = 1 + (($floor - 1) * 0.15);
        $minStats = (int) floor(self::ENEMY_BASE_STATS['min'] * $scalingFactor * 2.5);
        $maxStats = (int) floor(self::ENEMY_BASE_STATS['max'] * $scalingFactor * 2.5);

        $bossName = self::BOSS_NAMES[$floor] ?? "Miniboss Piso $floor";
        $boss = $this->makeEnemy($minStats, $maxStats, $pasives, $skills, $floor, $bossName, 5);
        $boss->setRelation('skills', $skills->random(min(4, $skills->count())));

        return collect([$boss]);
    }

    public function generateFinalBoss(int $floor): Collection
    {
        $pasives = Pasive::all();
        $skills = Skill::all();

        $scalingFactor = 1 + (($floor - 1) * 0.15);
        $minStats = (int) floor(self::ENEMY_BASE_STATS['min'] * $scalingFactor * 4);
        $maxStats = (int) floor(self::ENEMY_BASE_STATS['max'] * $scalingFactor * 4);

        $boss = $this->makeEnemy(
            $minStats, $maxStats, $pasives, $skills, $floor,
            self::BOSS_NAMES[50], 5
        );
        $boss->pasive_id = $pasives->where('name', 'Regeneración')->first()?->id ?? $pasives->random()->id;
        $boss->setRelation('skills', $skills->random(min(4, $skills->count())));
        $boss->can_summon = true;

        return collect([$boss]);
    }

    private function makeEnemy(
        int $min, int $max, Collection $pasives, Collection $skills,
        int $floor, ?string $name = null, int $skillCount = 4
    ): Character {
        $stats = $this->generateStats($min, $max);

        $enemy = new Character([
            'game_id'           => 0,
            'name'              => $name ?? $this->generateName(),
            'pasive_id'         => $pasives->random()->id,
            'hp'                => $stats['hp'],
            'max_hp'            => $stats['hp'],
            'physical_attack'   => $stats['physical_attack'],
            'special_attack'   => $stats['special_attack'],
            'physical_defense' => $stats['physical_defense'],
            'special_defense'  => $stats['special_defense'],
            'speed'             => $stats['speed'],
            'level'             => $floor,
            'exp'               => 0,
            'recruited'         => false,
            'alive'             => true,
        ]);

        $randomSkills = $skills->random(min($skillCount, $skills->count()));
        $enemy->setRelation('skills', $randomSkills);

        return $enemy;
    }

    private function generateStats(int $min, int $max): array
    {
        return [
            'hp'               => rand($min, $max),
            'physical_attack'  => rand($min, $max),
            'special_attack'   => rand($min, $max),
            'physical_defense' => rand($min, $max),
            'special_defense'  => rand($min, $max),
            'speed'            => rand($min, $max),
        ];
    }

    private function generateName(): string
    {
        return self::NAMES[array_rand(self::NAMES)];
    }
}
