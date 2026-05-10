<?php

namespace Database\Seeders;

use App\Models\Pasive;
use Illuminate\Database\Seeder;

class PasiveSeeder extends Seeder
{
    public function run(): void
    {
        $pasives = [
            ['name' => 'Regeneración', 'description' => 'Regenera un 5% de HP al inicio de cada turno.'],
            ['name' => 'Furia', 'description' => 'Aumenta el ataque físico un 10% cuando el HP es inferior al 50%.'],
            ['name' => 'Fortaleza', 'description' => 'Reduce el daño recibido un 10% cuando el HP es inferior al 30%.'],
            ['name' => 'Agilidad', 'description' => 'Tiene un 15% de probabilidad de esquivar cualquier ataque.'],
            ['name' => 'Estática', 'description' => 'Los ataques especiales tienen un 10% de probabilidad de paralizar al enemigo.'],
            ['name' => 'Vampirismo', 'description' => 'Restaura un 5% del daño infligido como HP.'],
            ['name' => 'Espina', 'description' => 'Devuelve un 10% del daño recibido al atacante.'],
            ['name' => 'Precisión', 'description' => 'Aumenta la probabilidad de golpe crítico en un 5%.'],
            ['name' => 'Muro', 'description' => 'Aumenta la defensa física un 15%.'],
            ['name' => 'Escudo Mágico', 'description' => 'Aumenta la defensa especial un 15%.'],
            ['name' => 'Velocidad', 'description' => 'Aumenta la velocidad base un 10%.'],
            ['name' => 'Berserker', 'description' => 'Aumenta el ataque un 20% cuando el HP es inferior al 25%.'],
            ['name' => 'Protección', 'description' => 'Reduce el daño recibido un 5% siempre.'],
            ['name' => 'Crítico', 'description' => 'Aumenta el daño de golpes críticos en un 15%.'],
            ['name' => 'Perseverancia', 'description' => 'Evita el KO una vez por combate (50% HP mínimo).'],
        ];

        foreach ($pasives as $pasive) {
            Pasive::create($pasive);
        }
    }
}