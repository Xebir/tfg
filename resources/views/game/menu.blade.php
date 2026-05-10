@extends('layouts.abyssal')

@section('title', 'Menú — Abyssal Rift')

@section('content')
    <div class="brand">
        <h1 class="brand-title">Abyssal Rift</h1>
        <div class="brand-divider"></div>
        <p class="brand-subtitle">Bienvenido, {{ Auth::user()->name }}</p>
    </div>

    <div class="menu-card">
        <form method="POST" action="{{ route('game.start') }}">
            @csrf
            <button type="submit" class="menu-btn menu-btn--primary">
                Nueva Partida
            </button>
        </form>

        @if($hasGame)
            <form method="POST" action="{{ route('game.continue') }}">
                @csrf
                <button type="submit" class="menu-btn">
                    Continuar
                </button>
            </form>
        @else
            <button class="menu-btn menu-btn--disabled" disabled>
                Continuar
            </button>
        @endif

        <a href="{{ route('historial') }}" class="menu-btn">
            Historial
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="menu-btn menu-btn--danger">
                Cerrar Sesión
            </button>
        </form>
    </div>

    <style>
        .menu-card {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            width: 100%;
            max-width: 340px;
            animation: enter .7s cubic-bezier(.22,1,.36,1) .15s both;
        }
        .menu-card form { width: 100%; }

        .menu-btn {
            display: block;
            width: 100%;
            padding: .9rem 1.5rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: .75rem;
            color: #c4b5fd;
            font-family: 'Cinzel', serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: background .25s, border-color .25s, color .25s, transform .2s, box-shadow .25s;
        }
        .menu-btn:hover {
            background: rgba(124,58,237,.15);
            border-color: rgba(124,58,237,.7);
            color: #fff;
            transform: translateX(4px);
            box-shadow: var(--glow);
        }

        .menu-btn--primary {
            background: linear-gradient(135deg, rgba(124,58,237,.3), rgba(29,78,216,.2));
            border-color: rgba(124,58,237,.6);
            color: #fff;
        }
        .menu-btn--primary:hover {
            background: linear-gradient(135deg, rgba(124,58,237,.5), rgba(29,78,216,.4));
            box-shadow: 0 8px 28px rgba(124,58,237,.4), var(--glow);
        }

        .menu-btn--danger {
            color: rgba(248,113,113,.8);
            border-color: rgba(248,113,113,.2);
        }
        .menu-btn--danger:hover {
            background: rgba(248,113,113,.1);
            border-color: rgba(248,113,113,.5);
            color: #fca5a5;
            box-shadow: 0 4px 20px rgba(248,113,113,.2);
        }

        .menu-btn--disabled {
            opacity: .35;
            cursor: not-allowed;
        }
        .menu-btn--disabled:hover {
            background: var(--glass-bg);
            border-color: var(--glass-border);
            color: #c4b5fd;
            transform: none;
            box-shadow: none;
        }
    </style>
@endsection
