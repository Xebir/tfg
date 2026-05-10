@extends('layouts.abyssal')

@section('title', 'Historial — Abyssal Rift')

@section('content')
    <div class="brand">
        <h1 class="brand-title">Abyssal Rift</h1>
        <div class="brand-divider"></div>
        <p class="brand-subtitle">Historial de partidas</p>
    </div>

    <div class="glass-card" style="max-width: 500px; text-align: center;">
        <h2 class="form-title">Historial</h2>
        <p style="color: rgba(148,163,184,.6); font-size: .9rem; line-height: 1.8;">
            Aún no hay partidas registradas.
        </p>

        <a href="{{ route('menu') }}" class="menu-btn" style="margin-top: 1.5rem; display: block;">
            Volver al menú
        </a>
    </div>

    <style>
        .menu-btn {
            padding: .8rem 1.5rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: .75rem;
            color: #c4b5fd;
            font-family: 'Cinzel', serif;
            font-size: .9rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background .25s, border-color .25s, color .25s, transform .2s, box-shadow .25s;
        }
        .menu-btn:hover {
            background: rgba(124,58,237,.15);
            border-color: rgba(124,58,237,.7);
            color: #fff;
            transform: translateX(4px);
            box-shadow: var(--glow);
        }
    </style>
@endsection
