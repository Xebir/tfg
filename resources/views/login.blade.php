@extends('layouts.abyssal')

@section('title', 'Iniciar sesión — Abyssal Rift')

@section('content')
    <div class="brand">
        <a href="{{ route('home') }}" class="brand-link">
            <h1 class="brand-title">Abyssal Rift</h1>
        </a>
        <div class="brand-divider"></div>
    </div>

    <div class="glass-card">
        <h2 class="form-title">Acceder al Abismo</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="tu@correo.com"
                       required autofocus>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••"
                       required>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-epic">
                <span>Entrar</span>
            </button>
        </form>

        <p class="form-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Únete al Rift</a>
        </p>
    </div>
@endsection
