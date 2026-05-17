@extends('layouts.abyssal')

@section('title', 'Historial — Abyssal Rift')

@section('content')
    <div class="brand">
        <h1 class="brand-title">Abyssal Rift</h1>
        <div class="brand-divider"></div>
        <p class="brand-subtitle">Historial de partidas</p>
    </div>

    <div class="glass-card" style="max-width: 700px;">
        <h2 class="form-title">Tus partidas</h2>

        @if($games->isEmpty())
            <p style="color: rgba(148,163,184,.6); font-size: .85rem; line-height: 1.8; text-align: center;">
                Aún no hay partidas registradas.
            </p>
        @else
            <div class="hist-list">
                @foreach($games as $g)
                    @php
                        $statusColors = [
                            'won'       => ['label' => 'Victoria', 'color' => '#4ade80'],
                            'lost'      => ['label' => 'Derrota',  'color' => '#ef4444'],
                            'abandoned' => ['label' => 'Abandonada', 'color' => '#f59e0b'],
                        ];
                        $st = $statusColors[$g->status] ?? ['label' => ucfirst($g->status), 'color' => '#a78bfa'];
                    @endphp
                    <div class="hist-row">
                        <div class="hist-dot" style="background:{{ $st['color'] }};box-shadow:0 0 8px {{ $st['color'] }};"></div>
                        <div class="hist-info">
                            <span class="hist-status {{ $g->status }}">{{ $st['label'] }}</span>
                            <span class="hist-meta">Piso {{ $g->floor }} · {{ $g->characters_count }} personajes</span>
                        </div>
                        <span class="hist-date">{{ $g->created_at->format('d/m/y H:i') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <a href="{{ route('menu') }}" class="hist-back">Volver al menú</a>
    </div>

    <style>
        .hist-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 1.5rem;
        }
        .hist-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(124,58,237,.12);
            border-radius: 8px;
            transition: background .2s;
        }
        .hist-row:hover {
            background: rgba(124,58,237,.08);
        }
        .hist-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .hist-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .hist-status {
            font-family: 'Cinzel', serif;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
        }
        .hist-status.won       { color: #4ade80; }
        .hist-status.lost      { color: #ef4444; }
        .hist-status.abandoned { color: #f59e0b; }
        .hist-meta {
            font-size: .75rem;
            color: rgba(148,163,184,.6);
        }
        .hist-date {
            font-size: .7rem;
            color: rgba(148,163,184,.4);
            white-space: nowrap;
        }
        .hist-back {
            display: block;
            text-align: center;
            padding: .8rem;
            background: rgba(124,58,237,.08);
            border: 1px solid rgba(124,58,237,.25);
            border-radius: 8px;
            color: #c4b5fd;
            font-family: 'Cinzel', serif;
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
            transition: background .25s, border-color .25s, color .25s;
        }
        .hist-back:hover {
            background: rgba(124,58,237,.15);
            border-color: rgba(124,58,237,.6);
            color: #fff;
        }
    </style>
@endsection
