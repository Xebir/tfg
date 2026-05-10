<?php

namespace Database\Seeders;

use App\Models\Pasive;
use Illuminate\Database\Seeder;

class PasiveSeeder extends Seeder
{
    public function run(): void
    {
        $pasives = [
            ['description' => 'Regenera un 5% de HP al inicio de cada turno.'],
            ['description' => 'Aumenta el ataque físico un 10% cuando el HP es inferior al 50%.'],
            ['description' => 'Reduce el daño recibido un 10% cuando el HP es inferior al 30%.'],
            ['description' => 'Tiene un 15% de probabilidad de esquivar cualquier ataque.'],
            ['description' => 'Los ataques especiales tienen un 10% de probabilidad de paralizar al enemigo.'],
        ];

        foreach ($pasives as $pasive) {
            Pasive::create($pasive);
        }
    }
}
