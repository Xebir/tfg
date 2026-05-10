@extends('layouts.abyssal')

@section('title', 'Abyssal Rift')

@section('content')
    <div class="brand">
        <p class="home-tagline">El abismo te llama</p>
        <h1 class="brand-title">Abyssal Rift</h1>
        <div class="brand-divider"></div>
        <p class="brand-subtitle">Forja tu leyenda en las profundidades</p>
    </div>

    <p class="home-desc">
        Reúne a tu equipo de tres héroes y desciende a través de los pisos de la grieta.<br>
        Cada nivel esconde enemigos más oscuros. Pocos regresan.
    </p>

    <div class="home-actions">
        <a href="{{ route('login') }}" class="btn-primary">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn-secondary">Crear cuenta</a>
    </div>
@endsection
