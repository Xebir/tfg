@php
    $imagen = $imagen ?? 'enemy_1';
    $size   = $size   ?? 60;
    $src    = asset('images/sprites/enemies/' . $imagen . '.png');
@endphp
<img
    src="{{ $src }}"
    alt="{{ $nombre ?? 'Enemigo' }}"
    width="{{ $size }}"
    style="image-rendering:pixelated; display:block;"
    onerror="this.style.opacity='0'"
>
