<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'Golpe Rápido', 'description' => 'Un golpe rápido y básico.', 'damage' => 40, 'damage_type' => true],
            ['name' => 'Embestida', 'description' => 'Una embestida poderosa.', 'damage' => 80, 'damage_type' => true],
            ['name' => 'Golpe Crítico', 'description' => 'Un golpe con alta probabilidad de crítico.', 'damage' => 100, 'damage_type' => true],
            ['name' => 'Arañazo', 'description' => 'Un ataque rápido con garras.', 'damage' => 35, 'damage_type' => true],
            ['name' => 'Patada Giratoria', 'description' => 'Patada en espiral.', 'damage' => 60, 'damage_type' => true],
            ['name' => 'Puño Sónico', 'description' => 'Puño envuelto en ondas de sonido.', 'damage' => 70, 'damage_type' => true],
            ['name' => 'Rayo', 'description' => 'Rayo de energía eléctrica.', 'damage' => 50, 'damage_type' => false],
            ['name' => 'Explosión Magica', 'description' => 'Explosión de energía mística.', 'damage' => 90, 'damage_type' => false],
            ['name' => 'Tormenta de Fuego', 'description' => 'Fuego que envuelve al enemigo.', 'damage' => 110, 'damage_type' => false],
            ['name' => 'Pulso Mental', 'description' => 'Onda de poder psíquico.', 'damage' => 45, 'damage_type' => false],
            ['name' => 'Ola de Frío', 'description' => 'Frío glacial que quema.', 'damage' => 65, 'damage_type' => false],
            ['name' => 'Terremoto', 'description' => 'Sacude el suelo con fuerza.', 'damage' => 85, 'damage_type' => true],
            ['name' => 'Rayo Solar', 'description' => 'Luz concentrada ardiente.', 'damage' => 95, 'damage_type' => false],
            ['name' => 'Colmillo Venenoso', 'description' => 'Ataque envenenado.', 'damage' => 55, 'damage_type' => true],
            ['name' => 'Viento Helado', 'description' => 'Viento cortante y helado.', 'damage' => 75, 'damage_type' => false],
            ['name' => 'Ráfaga', 'description' => 'Golpe con ráfaga de viento.', 'damage' => 50, 'damage_type' => true],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}