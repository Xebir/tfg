<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Físicas (damage_type = true)
            ['description' => 'Golpe rápido', 'damage' => 40, 'damage_type' => true],
            ['description' => 'Embestida poderosa', 'damage' => 80, 'damage_type' => true],
            ['description' => 'Golpe crítico', 'damage' => 100, 'damage_type' => true],
            ['description' => 'Arañazo veloz', 'damage' => 35, 'damage_type' => true],
            ['description' => 'Patada giratoria', 'damage' => 60, 'damage_type' => true],
            // Especiales (damage_type = false)
            ['description' => 'Rayo de energía', 'damage' => 50, 'damage_type' => false],
            ['description' => 'Explosión mágica', 'damage' => 90, 'damage_type' => false],
            ['description' => 'Tormenta de fuego', 'damage' => 110, 'damage_type' => false],
            ['description' => 'Pulso mental', 'damage' => 45, 'damage_type' => false],
            ['description' => 'Ola de frío', 'damage' => 65, 'damage_type' => false],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
