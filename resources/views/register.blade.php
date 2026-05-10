@extends('layouts.abyssal')

@section('title', 'Crear cuenta — Abyssal Rift')

@section('content')
    <div class="brand brand--compact">
        <a href="{{ route('home') }}" class="brand-link">
            <h1 class="brand-title">Abyssal Rift</h1>
        </a>
        <div class="brand-divider"></div>
    </div>

    <div class="glass-card glass-card--compact">
        <h2 class="form-title">Forjar tu Leyenda</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group form-group--sm">
                <label for="name">Nombre de héroe</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="Tu nombre en el abismo"
                       required autofocus>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group form-group--sm">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="tu@correo.com"
                       required>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group form-group--sm">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="Mínimo 8 caracteres"
                       required>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group form-group--sm">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Repite tu contraseña"
                       required>
            </div>

            <button type="submit" class="btn-epic btn-epic--sm">
                Descender al Abismo
            </button>
        </form>

        <p class="form-link form-link--sm">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Volver al portal</a>
        </p>
    </div>

    <style>
        /* Compactar solo en register */
        .brand--compact { margin-bottom: 1rem; }
        .brand--compact .brand-title { font-size: clamp(1.8rem, 4vw, 3rem); }
        .brand--compact .brand-divider { margin: .5rem auto; }

        .glass-card--compact { padding: 1.5rem 2rem; }
        .glass-card--compact .form-title { font-size: 1.1rem; margin-bottom: 1rem; }

        .form-group--sm { margin-bottom: .75rem; }
        .form-group--sm label { font-size: .7rem; margin-bottom: .3rem; }
        .form-group--sm input { padding: .55rem .85rem; font-size: .85rem; }

        .btn-epic--sm { padding: .65rem 1.25rem; margin-top: .9rem; font-size: .82rem; }

        .form-link--sm { margin-top: 1rem; font-size: .78rem; }
    </style>
@endsection
