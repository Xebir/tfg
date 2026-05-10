<?php

namespace App\Services;

use App\Models\Game;

class GeneratorService
{
    /**
     * Genera 3 personajes para el jugador al iniciar una partida nueva.
     * Asigna stats aleatorios, un pasivo del catálogo y hasta 4 skills del catálogo.
     */
    public function generatePlayerTeam(Game $game): void
    {
        // TODO: implementar generación procedural del equipo del jugador
    }

    /**
     * Genera los enemigos del piso actual.
     * Los stats escalan según el número de piso.
     */
    public function generateEnemies(int $floor): array
    {
        // TODO: implementar generación procedural de enemigos por piso
        return [];
    }
}