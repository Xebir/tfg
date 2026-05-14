<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Piso {{ $game->floor }} — Abyssal Rift</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Press+Start+2P&family=Raleway:wght@400;600&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple:     #7c3aed;
            --cyan:       #06b6d4;
            --pink:       #db2777;
            --red:        #ef4444;
            --gold:       #f59e0b;
            --ui-bg:      #1a0a2e;
            --ui-border:  #4c1d95;
            --ui-text:    #e2e8f0;
        }

        html, body {
            height: 100%; width: 100%;
            overflow: hidden;
            font-family: 'Press Start 2P', monospace;
            background: #050010;
            color: var(--ui-text);
            image-rendering: pixelated;
        }

        /* ══════════════════════════════════════
           LAYOUT PRINCIPAL
        ══════════════════════════════════════ */
        .game-wrapper {
            height: 100vh;
            display: grid;
            grid-template-rows: 62% 38%;
        }

        /* ══════════════════════════════════════
           ESCENA DE BATALLA
        ══════════════════════════════════════ */
        .battle-scene {
            position: relative;
            overflow: hidden;
        }

        /* Cielo */
        .scene-sky {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg,
                #0a0015 0%,
                #130026 40%,
                #1a0035 60%,
                #0d001e 100%);
        }

        /* Estrellas */
        #star-canvas {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
        }

        /* Niebla / neblina */
        .scene-fog {
            position: absolute;
            bottom: 35%;
            left: 0; right: 0;
            height: 80px;
            background: linear-gradient(180deg, transparent, rgba(124,58,237,.08), transparent);
            z-index: 2;
            pointer-events: none;
        }

        /* Suelo */
        .scene-ground {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 38%;
            background: linear-gradient(180deg,
                #0d001e 0%,
                #1a0533 30%,
                #0f0025 100%);
            border-top: 2px solid rgba(124,58,237,.3);
            z-index: 2;
        }
        .scene-ground::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg,
                transparent, rgba(124,58,237,.6), rgba(6,182,212,.4),
                rgba(219,39,119,.3), transparent);
        }

        /* Plataformas */
        .platform-enemy {
            position: absolute;
            right: 12%;
            top: 48%;
            width: 160px;
            height: 28px;
            background: radial-gradient(ellipse, rgba(124,58,237,.25) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 3;
            filter: blur(4px);
        }
        .platform-player {
            position: absolute;
            left: 10%;
            bottom: 28%;
            width: 200px;
            height: 36px;
            background: radial-gradient(ellipse, rgba(6,182,212,.2) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 3;
            filter: blur(5px);
        }

        /* ══════════════════════════════════════
           SPRITES
        ══════════════════════════════════════ */

        .enemy-sprite-area {
            position: absolute;
            right: 8%;
            top: 8%;
            z-index: 4;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .enemies-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }
        .enemy-figure {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: filter .2s;
            animation: enemy-idle 2s ease-in-out infinite;
        }
        .enemy-figure:hover { filter: brightness(1.4); }
        .enemy-figure.active-target {
            animation: enemy-target 1.5s ease-in-out infinite;
        }
        .enemy-figure.dead { opacity: .2; filter: grayscale(1); pointer-events: none; }

        @keyframes enemy-idle {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }
        @keyframes enemy-target {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-9px) scale(1.06); }
        }

        .player-sprite-area {
            position: absolute;
            left: 6%;
            bottom: 22%;
            z-index: 4;
        }
        .player-figure {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .p-sprite {
            position: relative;
            animation: player-idle 3s ease-in-out infinite;
        }
        @keyframes player-idle {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-5px); }
        }
        .p-aura {
            position: absolute;
            inset: -14px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--pc, #7c3aed) 0%, transparent 70%);
            opacity: .2;
            filter: blur(8px);
        }

        /* ══════════════════════════════════════
           CAJAS DE INFO HP (estilo Pokémon)
        ══════════════════════════════════════ */

        /* Info enemigo — arriba izquierda */
        .info-enemy {
            position: absolute;
            left: 3%;
            top: 8%;
            z-index: 5;
            background: rgba(10,3,25,.85);
            border: 2px solid rgba(124,58,237,.5);
            border-radius: 8px;
            padding: 8px 14px;
            min-width: 200px;
            box-shadow: 0 0 20px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.05);
        }

        /* Info jugador — abajo derecha */
        .info-player {
            position: absolute;
            right: 3%;
            bottom: 30%;
            z-index: 5;
            background: rgba(10,3,25,.9);
            border: 2px solid rgba(6,182,212,.4);
            border-radius: 8px;
            padding: 8px 14px;
            min-width: 220px;
            box-shadow: 0 0 20px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.05);
        }

        .info-name-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 5px;
        }
        .info-name {
            font-size: .55rem;
            color: #e2e8f0;
            letter-spacing: .05em;
        }
        .info-level {
            font-size: .45rem;
            color: rgba(167,139,250,.7);
        }

        .hp-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .hp-label-pk {
            font-size: .4rem;
            color: var(--purple);
            letter-spacing: .1em;
            min-width: 16px;
        }
        .hp-track {
            flex: 1;
            height: 6px;
            background: rgba(255,255,255,.1);
            border-radius: 3px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.05);
        }
        .hp-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, #16a34a, #4ade80);
            transition: width .5s ease;
        }
        .hp-fill.mid { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .hp-fill.low { background: linear-gradient(90deg, #dc2626, #f87171); }
        .hp-nums {
            font-size: .38rem;
            color: rgba(148,163,184,.6);
            min-width: 50px;
            text-align: right;
        }

        /* Indicadores de enemigos múltiples */
        .enemy-indicators {
            position: absolute;
            right: 3%;
            top: 8%;
            z-index: 5;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .enemy-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(239,68,68,.3);
            border: 1px solid rgba(239,68,68,.5);
            cursor: pointer;
            transition: all .2s;
        }
        .enemy-dot.active { background: #ef4444; box-shadow: 0 0 8px #ef4444; }
        .enemy-dot.dead   { background: rgba(100,100,100,.2); border-color: rgba(100,100,100,.3); }

        /* Piso */
        .floor-badge {
            position: absolute;
            top: 8px; left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            font-size: .45rem;
            color: rgba(167,139,250,.6);
            letter-spacing: .2em;
            background: rgba(10,3,25,.6);
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid rgba(124,58,237,.2);
        }

        /* ══════════════════════════════════════
           UI INFERIOR (estilo Pokémon)
        ══════════════════════════════════════ */
        .battle-ui {
            background: var(--ui-bg);
            border-top: 3px solid var(--ui-border);
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
        }
        .battle-ui::before {
            content: '';
            position: absolute;
            top: -1px; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,58,237,.6), rgba(6,182,212,.4), transparent);
        }

        /* Caja de diálogo */
        .dialog-box {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 2px solid var(--ui-border);
            position: relative;
            overflow: hidden;
        }
        .dialog-box::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(124,58,237,.04) 0%, transparent 60%);
            pointer-events: none;
        }
        .dialog-text {
            font-size: .55rem;
            line-height: 1.8;
            color: var(--ui-text);
            letter-spacing: .03em;
        }
        .dialog-text .char-name-hl { color: #c4b5fd; }
        .dialog-cursor {
            display: inline-block;
            width: 6px; height: 6px;
            background: #c4b5fd;
            border-radius: 1px;
            margin-left: 4px;
            animation: blink .8s step-end infinite;
        }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

        /* Botones de acción */
        .action-panel { padding: 12px; display: flex; flex-direction: column; gap: 8px; }
        .action-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; flex: 1; }

        .action-btn {
            background: rgba(124,58,237,.12);
            border: 2px solid rgba(124,58,237,.35);
            border-radius: 6px;
            color: #c4b5fd;
            font-family: 'Press Start 2P', monospace;
            font-size: .55rem;
            letter-spacing: .05em;
            cursor: pointer;
            transition: all .15s;
            padding: 10px 6px;
            text-align: center;
        }
        .action-btn:hover, .action-btn:focus {
            background: rgba(124,58,237,.3);
            border-color: rgba(124,58,237,.8);
            color: #fff;
            outline: none;
            box-shadow: inset 0 0 10px rgba(124,58,237,.2);
        }
        .action-btn.selected {
            background: rgba(124,58,237,.4);
            border-color: #7c3aed;
            color: #fff;
        }
        .action-btn.danger {
            color: rgba(252,165,165,.8);
            border-color: rgba(239,68,68,.3);
            background: rgba(239,68,68,.06);
        }
        .action-btn.danger:hover {
            background: rgba(239,68,68,.18);
            border-color: rgba(239,68,68,.7);
            color: #fca5a5;
        }

        /* Panel de habilidades (reemplaza botones) */
        .skills-panel { display: none; flex-direction: column; gap: 8px; padding: 12px; }
        .skills-panel.visible { display: flex; }
        .action-panel.hidden  { display: none; }

        .skill-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; flex: 1; }
        .skill-btn {
            background: rgba(29,78,216,.1);
            border: 2px solid rgba(29,78,216,.3);
            border-radius: 6px;
            color: #93c5fd;
            font-family: 'Press Start 2P', monospace;
            font-size: .42rem;
            cursor: pointer;
            padding: 8px 6px;
            text-align: left;
            transition: all .15s;
            line-height: 1.6;
        }
        .skill-btn:hover {
            background: rgba(29,78,216,.25);
            border-color: rgba(29,78,216,.7);
            color: #fff;
        }
        .skill-btn .sk-name { display: block; color: #e2e8f0; margin-bottom: 3px; }
        .skill-btn .sk-dmg  { color: rgba(148,163,184,.6); font-size: .38rem; }
        .skill-btn .sk-type-phys { color: rgba(251,191,36,.7); }
        .skill-btn .sk-type-spec { color: rgba(6,182,212,.7); }

        .back-btn {
            font-family: 'Press Start 2P', monospace;
            font-size: .42rem;
            color: rgba(167,139,250,.6);
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            padding: 2px 0;
            transition: color .15s;
        }
        .back-btn:hover { color: #c4b5fd; }

        /* Panel de equipo */
        .team-panel { display: none; flex-direction: column; gap: 6px; padding: 12px; }
        .team-panel.visible { display: flex; }

        .team-member {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 8px;
            background: rgba(124,58,237,.06);
            border: 1px solid rgba(124,58,237,.2);
            border-radius: 5px;
            cursor: pointer;
            transition: all .15s;
            font-size: .4rem;
        }
        .team-member:hover { background: rgba(124,58,237,.15); border-color: rgba(124,58,237,.5); }
        .team-member.active-char { border-color: var(--purple); color: #c4b5fd; }
        .team-member.dead { opacity: .35; filter: grayscale(1); pointer-events: none; }
        .tm-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .tm-name { flex: 1; color: #e2e8f0; }
        .tm-hp   { color: rgba(148,163,184,.6); font-size: .35rem; }

        /* Formulario oculto */
        #battle-form { display: none; }

        /* ══════════════════════════════════════
           MODAL GAME OVER
        ══════════════════════════════════════ */
        .game-over-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(0, 0, 0, 0);
            backdrop-filter: blur(0px);
            align-items: center;
            justify-content: center;
            transition: background .6s ease, backdrop-filter .6s ease;
        }
        .game-over-overlay.visible {
            display: flex;
            background: rgba(0, 0, 0, .88);
            backdrop-filter: blur(6px);
            animation: go-fade-in .6s ease forwards;
        }
        @keyframes go-fade-in {
            from { background: rgba(0,0,0,0); backdrop-filter: blur(0px); }
            to   { background: rgba(0,0,0,.88); backdrop-filter: blur(6px); }
        }

        .game-over-card {
            text-align: center;
            padding: 3rem 3.5rem;
            background: rgba(10, 3, 25, .95);
            border: 2px solid rgba(239, 68, 68, .4);
            border-radius: 12px;
            box-shadow: 0 0 60px rgba(239, 68, 68, .25), 0 0 120px rgba(239, 68, 68, .1);
            max-width: 480px;
            animation: go-card-in .7s cubic-bezier(.22,1,.36,1) .2s both;
        }
        @keyframes go-card-in {
            from { transform: scale(.8) translateY(20px); opacity: 0; }
            to   { transform: scale(1) translateY(0);    opacity: 1; }
        }

        .go-title {
            font-size: 2rem;
            color: #ef4444;
            letter-spacing: .2em;
            text-shadow: 0 0 20px rgba(239,68,68,.7), 0 0 40px rgba(239,68,68,.4);
            animation: go-flicker 3s ease-in-out infinite;
            line-height: 1.2;
        }
        @keyframes go-flicker {
            0%, 100% { opacity: 1; }
            92%       { opacity: 1; }
            93%       { opacity: .4; }
            94%       { opacity: 1; }
            96%       { opacity: .6; }
            97%       { opacity: 1; }
        }

        .go-divider {
            margin: 1.5rem auto;
            height: 1px;
            width: 80%;
            background: linear-gradient(90deg, transparent, rgba(239,68,68,.5), transparent);
        }

        .go-phrase {
            font-size: .5rem;
            color: rgba(203, 213, 225, .75);
            line-height: 2.2;
            letter-spacing: .06em;
            margin-bottom: 2rem;
        }

        .go-btn {
            display: inline-block;
            padding: .8rem 2rem;
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(239, 68, 68, .4);
            border-radius: 6px;
            color: #fca5a5;
            font-family: 'Press Start 2P', monospace;
            font-size: .5rem;
            letter-spacing: .1em;
            text-decoration: none;
            cursor: pointer;
            transition: background .2s, border-color .2s, box-shadow .2s;
        }
        .go-btn:hover {
            background: rgba(239, 68, 68, .25);
            border-color: rgba(239, 68, 68, .7);
            box-shadow: 0 0 16px rgba(239, 68, 68, .3);
            color: #fff;
        }

        /* Deshabilitar controles durante animación */
        .battle-busy .action-btn,
        .battle-busy .skill-btn,
        .battle-busy .back-btn {
            pointer-events: none;
            opacity: .45;
        }
    </style>
</head>
<body>

    <div class="game-wrapper">

        {{-- ════ ESCENA DE BATALLA ════ --}}
        <div class="battle-scene">
            <div class="scene-sky"></div>
            <canvas id="star-canvas"></canvas>
            <div class="scene-fog"></div>
            <div class="scene-ground"></div>
            <div class="platform-enemy"></div>
            <div class="platform-player"></div>

            {{-- Badge piso --}}
            <div class="floor-badge">Piso {{ $game->floor }}</div>

            {{-- ── ENEMIGOS (arriba derecha) ── --}}
            @php
                $enemyColors = [
                    ['--ec:#ef4444', 'ec' => '#ef4444'],
                    ['--ec:#f97316', 'ec' => '#f97316'],
                    ['--ec:#a855f7', 'ec' => '#a855f7'],
                ];
                $activeEnemy = $enemies->first();
            @endphp

            <div class="enemy-sprite-area">
                <div class="enemies-row">
                    @foreach($enemies as $j => $enemy)
                        @php $ec = $enemyColors[$j % 3]; @endphp
                        <div class="enemy-figure {{ $j === 0 ? 'active-target' : '' }} {{ !$enemy->alive ? 'dead' : '' }}"
                             id="enemy-figure-{{ $j }}"
                             style="{{ $ec[0] }}"
                             onclick="selectTarget({{ $j }})">
                            @include('game.sprites.enemy', [
                                'imagen' => $enemy->imagen ?? 'enemy_1',
                                'nombre' => $enemy->name,
                                'size'   => $j === 0 ? 80 : 60,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Info enemigo activo (arriba izquierda) --}}
            @foreach($enemies as $j => $enemy)
                @php
                    $eHpPct   = $enemy->max_hp > 0 ? ($enemy->hp / $enemy->max_hp) * 100 : 0;
                    $eHpClass = $eHpPct > 50 ? '' : ($eHpPct > 25 ? 'mid' : 'low');
                @endphp
                <div class="info-enemy" id="info-enemy-{{ $j }}" style="{{ $j !== 0 ? 'display:none' : '' }}">
                    <div class="info-name-row">
                        <span class="info-name">{{ $enemy->name }}</span>
                        <span class="info-level">Nv{{ $enemy->level }}</span>
                    </div>
                    <div class="hp-row">
                        <span class="hp-label-pk">HP</span>
                        <div class="hp-track">
                            <div class="hp-fill {{ $eHpClass }}" id="enemy-hp-bar-{{ $j }}" style="width:{{ $eHpPct }}%"></div>
                        </div>
                        <span class="hp-nums">{{ $enemy->hp }}/{{ $enemy->max_hp }}</span>
                    </div>
                </div>
            @endforeach

            {{-- ── JUGADOR (abajo izquierda) ── --}}
            @php
                $charColors = [
                    ['style' => '--pc:#7c3aed;--pc-dim:#4c1d95', 'hex' => '#7c3aed'],
                    ['style' => '--pc:#06b6d4;--pc-dim:#0e7490', 'hex' => '#06b6d4'],
                    ['style' => '--pc:#db2777;--pc-dim:#9d174d', 'hex' => '#db2777'],
                ];
            @endphp

            <div class="player-sprite-area" id="player-sprite">
                <div class="player-figure">
                    <div class="p-sprite" id="p-svg-wrap" style="{{ $charColors[$activeCharIndex]['style'] }}">
                        <div class="p-aura"></div>
                        @include('game.sprites.player', [
                            'imagen' => $team[$activeCharIndex]->imagen ?? 'hero_1',
                            'nombre' => $team[$activeCharIndex]->name,
                            'size'   => 80,
                        ])
                    </div>
                </div>
            </div>

            {{-- Info jugador activo (abajo derecha) --}}
            @foreach($team as $i => $char)
                @php
                    $hpPct   = $char->max_hp > 0 ? ($char->hp / $char->max_hp) * 100 : 0;
                    $hpClass = $hpPct > 50 ? '' : ($hpPct > 25 ? 'mid' : 'low');
                @endphp
                <div class="info-player" id="info-player-{{ $i }}" style="{{ $i !== $activeCharIndex ? 'display:none' : '' }}">
                    <div class="info-name-row">
                        <span class="info-name">{{ $char->name }}</span>
                        <span class="info-level">Nv{{ $char->level }}</span>
                    </div>
                    <div class="hp-row">
                        <span class="hp-label-pk">HP</span>
                        <div class="hp-track">
                            <div class="hp-fill {{ $hpClass }}" style="width:{{ $hpPct }}%"></div>
                        </div>
                        <span class="hp-nums">{{ $char->hp }}/{{ $char->max_hp }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ════ UI INFERIOR ════ --}}
        <div class="battle-ui">

            {{-- Caja de diálogo --}}
            <div class="dialog-box">
                <div class="dialog-text" id="dialog-text">
                    Que hara <span class="char-name-hl">{{ $team[$activeCharIndex]->name }}</span>?
                    <span class="dialog-cursor"></span>
                </div>
            </div>

            {{-- Panel acciones principal --}}
            <div class="action-panel" id="action-panel">
                <div class="action-grid">
                    <button class="action-btn" onclick="showSkills()">LUCHAR</button>
                    <button class="action-btn" onclick="showTeam()">EQUIPO</button>
                    <button class="action-btn danger" onclick="exitGame()">GUARDAR</button>
                    <button class="action-btn danger" onclick="finishGame()">HUIR</button>
                </div>
            </div>

            {{-- Panel habilidades --}}
            <div class="skills-panel" id="skills-panel">
                @foreach($team as $i => $char)
                    <div id="skill-list-{{ $i }}" style="display:{{ $i === $activeCharIndex ? 'contents' : 'none' }}">
                        <div class="skill-grid">
                            @foreach($char->skills->take(4) as $skill)
                                <button class="skill-btn" onclick="useSkill({{ $skill->id }}, '{{ addslashes($skill->name) }}')">
                                    <span class="sk-name">{{ $skill->name }}</span>
                                    <span class="sk-dmg {{ $skill->damage_type ? 'sk-type-spec' : 'sk-type-phys' }}">
                                        {{ $skill->damage }} — {{ $skill->damage_type ? 'Magico' : 'Fisico' }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <button class="back-btn" onclick="showActions()">Volver</button>
            </div>

            {{-- Panel equipo --}}
            <div class="team-panel" id="team-panel">
                @foreach($team as $i => $char)
                    @php
                        $hpPct = $char->max_hp > 0 ? ($char->hp / $char->max_hp) * 100 : 0;
                    @endphp
                    <div class="team-member {{ $i === $activeCharIndex ? 'active-char' : '' }} {{ !$char->alive ? 'dead' : '' }}"
                         onclick="switchChar({{ $i }})">
                        <div class="tm-dot" style="background:{{ $charColors[$i]['hex'] }};box-shadow:0 0 5px {{ $charColors[$i]['hex'] }}"></div>
                        <span class="tm-name">{{ $char->name }} Nv{{ $char->level }}</span>
                        <span class="tm-hp">{{ $char->hp }}/{{ $char->max_hp }} HP</span>
                    </div>
                @endforeach
                <button class="back-btn" onclick="showActions()">Volver</button>
            </div>
        </div>
    </div>

    {{-- Modal GAME OVER --}}
    <div class="game-over-overlay" id="game-over-overlay">
        <div class="game-over-card">
            <div class="go-title">GAME OVER</div>
            <div class="go-divider"></div>
            <p class="go-phrase" id="go-phrase"></p>
            <a href="{{ route('menu') }}" class="go-btn">Volver al menú</a>
        </div>
    </div>

    {{-- Formularios ocultos (solo exit y finish, battle ahora es AJAX) --}}
    <form id="exit-form"   method="POST" action="{{ route('game.exit') }}">@csrf</form>
    <form id="finish-form" method="POST" action="{{ route('game.finish') }}">@csrf</form>

    <script>
        // ── ESTRELLAS ──
        const canvas = document.getElementById('star-canvas');
        const ctx    = canvas.getContext('2d');
        let stars    = [];
        function resize() {
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight * .62;
            stars = [];
            const n = Math.floor((canvas.width * canvas.height) / 4000);
            for (let i = 0; i < n; i++) {
                stars.push({ x: Math.random()*canvas.width, y: Math.random()*canvas.height,
                    r: Math.random()*1.3+.2, a: Math.random(), s: Math.random()*.004+.001,
                    c: ['#fff','#c4b5fd','#93c5fd'][Math.floor(Math.random()*3)] });
            }
        }
        function draw() {
            ctx.clearRect(0,0,canvas.width,canvas.height);
            stars.forEach(s => {
                s.a += s.s; if (s.a>1||s.a<0) s.s*=-1;
                ctx.beginPath(); ctx.arc(s.x,s.y,s.r,0,Math.PI*2);
                ctx.fillStyle=s.c; ctx.globalAlpha=Math.max(0,Math.min(1,s.a)); ctx.fill();
            });
            ctx.globalAlpha=1; requestAnimationFrame(draw);
        }
        window.addEventListener('resize', resize); resize(); draw();

        // ── ESTADO ──
        let activeCharIndex   = {{ $activeCharIndex }};
        let activeTargetIndex = 0;
        let battleBusy        = false;
        let forcedSwitch      = false;

        const charNames    = @json($team->pluck('name'));
        const charColors   = @json(array_column($charColors, 'style'));
        const charHexColors = @json(array_column($charColors, 'hex'));
        const charDimColors = ['#4c1d95', '#0e7490', '#9d174d'];

        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        const GAME_OVER_PHRASES = [
            'El abismo te ha reclamado.\nSolo los fuertes\nsobreviven al Rift.',
            'La oscuridad te ha\nconsumido. Tu llama\nse ha extinguido.',
            'Caiste en las\nprofundidades eternas.\nEl Rift no perdona.',
            'Tu equipo ha\nsucumbido a la sombra.\nVuelve mas fuerte.',
            'El vacío ha ganado.\nPero los heroes\nsiempre regresan.',
        ];

        // ── UTILIDADES ──
        const sleep = ms => new Promise(r => setTimeout(r, ms));

        function setDialog(html) {
            document.getElementById('dialog-text').innerHTML =
                html + '<span class="dialog-cursor"></span>';
        }

        function setBusy(busy) {
            battleBusy = busy;
            document.querySelector('.game-wrapper').classList.toggle('battle-busy', busy);
        }

        // ── ACTUALIZAR BARRA HP ──
        function updateHPBar(event) {
            const pct = event.max_hp > 0 ? (event.hp / event.max_hp) * 100 : 0;
            const cls = pct > 50 ? '' : pct > 25 ? 'mid' : 'low';

            if (event.entity === 'enemy') {
                const bar  = document.getElementById(`enemy-hp-bar-${event.index}`);
                const nums = document.querySelector(`#info-enemy-${event.index} .hp-nums`);
                if (bar)  { bar.style.width = pct + '%'; bar.className = `hp-fill ${cls}`; }
                if (nums) nums.textContent = `${event.hp}/${event.max_hp}`;
            } else {
                const box  = document.getElementById(`info-player-${event.index}`);
                if (box) {
                    const bar  = box.querySelector('.hp-fill');
                    const nums = box.querySelector('.hp-nums');
                    if (bar)  { bar.style.width = pct + '%'; bar.className = `hp-fill ${cls}`; }
                    if (nums) nums.textContent = `${event.hp}/${event.max_hp}`;
                }
                const tmHp = document.querySelectorAll('.tm-hp')[event.index];
                if (tmHp) tmHp.textContent = `${event.hp}/${event.max_hp} HP`;
            }
        }

        // ── MANEJAR MUERTE ──
        function handleFaint(event) {
            if (event.entity === 'enemy') {
                const fig = document.getElementById(`enemy-figure-${event.index}`);
                if (fig) fig.classList.add('dead');
                // Seleccionar siguiente enemigo vivo automáticamente
                const next = [...document.querySelectorAll('.enemy-figure:not(.dead)')];
                if (next.length > 0) {
                    const idx = parseInt(next[0].id.replace('enemy-figure-', ''));
                    selectTarget(idx);
                }
            } else {
                const tm = document.querySelectorAll('.team-member')[event.index];
                if (tm) tm.classList.add('dead');
            }
        }

        // ── CAMBIO AUTOMÁTICO DE PERSONAJE ──
        function handleSwitch(event) {
            activeCharIndex = event.char_index;
            document.querySelectorAll('.info-player').forEach((b, i) => {
                b.style.display = i === event.char_index ? '' : 'none';
            });
            document.querySelectorAll('.team-member').forEach((m, i) => {
                m.classList.toggle('active-char', i === event.char_index);
            });
            document.querySelectorAll('[id^="skill-list-"]').forEach((el, i) => {
                el.style.display = i === event.char_index ? 'contents' : 'none';
            });
        }

        // ── REPRODUCTOR DE EVENTOS ──
        async function playEvents(events) {
            for (const ev of events) {
                switch (ev.type) {
                    case 'dialog':
                        setDialog(ev.text);
                        await sleep(900);
                        break;

                    case 'hp_update':
                        updateHPBar(ev);
                        await sleep(450);
                        break;

                    case 'faint':
                        setDialog(`${ev.name} ha caido!`);
                        handleFaint(ev);
                        await sleep(1000);
                        break;

                    case 'switch':
                        handleSwitch(ev);
                        setDialog(`${ev.name} entra en combate!`);
                        await sleep(900);
                        break;

                    case 'victory':
                        setDialog(`Victoria! Avanzas al piso ${ev.floor}!`);
                        await sleep(1800);
                        window.location.reload();
                        return;

                    case 'game_over':
                        await sleep(600);
                        showGameOverModal();
                        return;
                }
            }

            // Si llegamos aquí no hay game_over ni victory.
            // Comprobar si el personaje activo ha muerto → cambio forzado gratis.
            const activeEl  = document.querySelectorAll('.team-member')[activeCharIndex];
            const activeDead = activeEl && activeEl.classList.contains('dead');

            setBusy(false);

            if (activeDead) {
                forcedSwitch = true;
                showTeam();
                setDialog('Elige tu siguiente personaje!');
            } else {
                showActions();
            }
        }

        // ── MODAL GAME OVER ──
        function showGameOverModal() {
            const phrase = GAME_OVER_PHRASES[Math.floor(Math.random() * GAME_OVER_PHRASES.length)];
            document.getElementById('go-phrase').innerHTML = phrase.replace(/\n/g, '<br>');
            document.getElementById('game-over-overlay').classList.add('visible');
        }

        // ── USAR HABILIDAD (AJAX) ──
        async function useSkill(skillId, skillName) {
            if (battleBusy) return;
            setBusy(true);
            showActions();
            setDialog(`${charNames[activeCharIndex]} usa ${skillName}!`);
            await sleep(300);

            try {
                const res = await fetch('{{ route("battle.action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'Accept':        'application/json',
                        'X-CSRF-TOKEN':  CSRF,
                    },
                    body: JSON.stringify({
                        type:         'skill',
                        skill_id:     skillId,
                        target_index: activeTargetIndex,
                        char_index:   activeCharIndex,
                    }),
                });

                if (res.redirected || !res.ok) { window.location.reload(); return; }

                const data = await res.json();

                if (data.redirect) { window.location.href = data.redirect; return; }
                if (data.events)   { await playEvents(data.events); return; }

            } catch (e) {
                window.location.reload();
            }

            setBusy(false);
            showActions();
        }

        // ── NAVEGACIÓN PANELES ──
        function showActions() {
            document.getElementById('action-panel').classList.remove('hidden');
            document.getElementById('skills-panel').classList.remove('visible');
            document.getElementById('team-panel').classList.remove('visible');
            setDialog(`Que hara <span class="char-name-hl">${charNames[activeCharIndex]}</span>?`);
        }
        function showSkills() {
            if (battleBusy) return;
            document.getElementById('action-panel').classList.add('hidden');
            document.getElementById('skills-panel').classList.add('visible');
            setDialog('Elige una habilidad.');
        }
        function showTeam() {
            if (battleBusy) return;
            document.getElementById('action-panel').classList.add('hidden');
            document.getElementById('team-panel').classList.add('visible');
            setDialog('Elige un personaje.');
        }

        // ── SELECCIONAR OBJETIVO ──
        function selectTarget(index) {
            document.querySelectorAll('.enemy-figure').forEach((f, i) => {
                f.classList.toggle('active-target', i === index);
            });
            document.querySelectorAll('.info-enemy').forEach((b, i) => {
                b.style.display = i === index ? '' : 'none';
            });
            activeTargetIndex = index;
        }

        // ── CAMBIAR PERSONAJE ──
        async function switchChar(index) {
            if (battleBusy) return;

            // Cambio forzado por muerte — gratis, solo actualiza la UI
            if (forcedSwitch) {
                const member = document.querySelectorAll('.team-member')[index];
                if (!member || member.classList.contains('dead')) return;
                forcedSwitch = false;
                handleSwitch({ char_index: index, name: charNames[index] });
                showActions();
                return;
            }

            if (index === activeCharIndex) { showActions(); return; }

            setBusy(true);
            showActions();
            setDialog(`Cambiando a ${charNames[index]}...`);
            await sleep(300);

            try {
                const res = await fetch('{{ route("battle.action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ type: 'switch', char_index: index }),
                });

                if (res.redirected || !res.ok) { window.location.reload(); return; }

                const data = await res.json();
                if (data.redirect) { window.location.href = data.redirect; return; }
                if (data.events)   { await playEvents(data.events); return; }

            } catch (e) {
                window.location.reload();
            }

            setBusy(false);
            showActions();
        }

        // ── SALIR / HUIR ──
        function exitGame()   { if (!battleBusy) document.getElementById('exit-form').submit(); }
        function finishGame() { if (!battleBusy) document.getElementById('finish-form').submit(); }
    </script>
</body>
</html>
