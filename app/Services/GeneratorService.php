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

    private const PLAYER_IMAGES = ['hero_1', 'hero_2', 'hero_3'];
    private const ENEMY_IMAGES  = ['enemy_1', 'enemy_2', 'enemy_3', 'enemy_4', 'enemy_5'];

    public function generatePlayerTeam(Game $game): Collection
    {
        $pasives = Pasive::all();
        $skills = Skill::all();

        $characters = [];
        for ($i = 0; $i < 5; $i++) {
            $isRecruited = $i < 3;

            $stats = $this->generateStats(self::BASE_STATS['min'], self::BASE_STATS['max'], 1);
            $randomSkills = $skills->random(min(4, $skills->count()));
            $randomPasive = $pasives->random();

            $character = Character::create([
                'game_id'          => $game->id,
                'name'             => $this->generateName(),
                'pasive_id'        => $randomPasive->id,
                'hp'               => $stats['hp'],
                'max_hp'           => $stats['hp'],
                'physical_attack'  => $stats['physical_attack'],
                'special_attack'   => $stats['special_attack'],
                'physical_defense' => $stats['physical_defense'],
                'special_defense'  => $stats['special_defense'],
                'speed'            => $stats['speed'],
                'level'            => 1,
                'exp'              => 0,
                'recruited'        => $isRecruited,
                'alive'            => true,
                'imagen'           => self::PLAYER_IMAGES[$i % count(self::PLAYER_IMAGES)],
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

        $enemyCount = $floor <= 3 ? 2 : 3;
        $enemies = [];

        for ($i = 0; $i < $enemyCount; $i++) {
            $stats = $this->generateStats($minStats, $maxStats, $floor);

            $enemy = new Character([
                'game_id'          => 0,
                'name'             => $this->generateName(),
                'pasive_id'        => $pasives->random()->id,
                'hp'               => $stats['hp'],
                'max_hp'           => $stats['hp'],
                'physical_attack'  => $stats['physical_attack'],
                'special_attack'   => $stats['special_attack'],
                'physical_defense' => $stats['physical_defense'],
                'special_defense'  => $stats['special_defense'],
                'speed'            => $stats['speed'],
                'level'            => $floor,
                'exp'              => 0,
                'recruited'        => false,
                'alive'            => true,
                'imagen'           => self::ENEMY_IMAGES[$i % count(self::ENEMY_IMAGES)],
            ]);

            $randomSkills = $skills->random(min(4, $skills->count()));
            $enemy->setRelation('skills', $randomSkills);

            $enemies[] = $enemy;
        }

        return collect($enemies);
    }

    private function generateStats(int $min, int $max, int $floor): array
    {
        return [
            'hp'               => $this->randomStat($min, $max),
            'physical_attack'  => $this->randomStat($min, $max),
            'special_attack'   => $this->randomStat($min, $max),
            'physical_defense' => $this->randomStat($min, $max),
            'special_defense'  => $this->randomStat($min, $max),
            'speed'            => $this->randomStat($min, $max),
        ];
    }

    private function randomStat(int $min, int $max): int
    {
        return rand($min, $max);
    }

    private function generateName(): string
    {
        return self::NAMES[array_rand(self::NAMES)];
    }
}