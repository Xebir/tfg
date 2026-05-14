@php
    $imagen = $imagen ?? 'hero_1';
    $size   = $size   ?? 80;
    $src    = asset('images/sprites/players/' . $imagen . '.png');
@endphp
<img
    src="{{ $src }}"
    alt="{{ $nombre ?? 'Heroe' }}"
    width="{{ $size }}"
    style="image-rendering:pixelated; display:block;"
    onerror="this.style.opacity='0'"
>
