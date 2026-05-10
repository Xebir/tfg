<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Abyssal Rift')</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Raleway:wght@300;400;600&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple:       #7c3aed;
            --blue:         #1d4ed8;
            --glow:         0 0 20px rgba(124,58,237,.6), 0 0 50px rgba(124,58,237,.2);
            --glass-bg:     rgba(10, 5, 30, 0.65);
            --glass-border: rgba(124,58,237,.3);
        }

        html, body {
            height: 100%;
            font-family: 'Raleway', sans-serif;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        body {
            background: radial-gradient(ellipse at 20% 50%, #1a0533 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, #0a1628 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, #0d0020 0%, transparent 60%),
                        #050010;
        }

        /* ── ESTRELLAS ── */
        #star-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* ── NEBULOSAS (estáticas, sin movimiento) ── */
        .nebula {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .nebula-1 {
            width: 550px; height: 550px;
            background: radial-gradient(circle, rgba(124,58,237,.2) 0%, transparent 70%);
            top: -100px; left: -80px;
        }
        .nebula-2 {
            width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(6,182,212,.15) 0%, transparent 70%);
            top: 25%; right: -60px;
        }
        .nebula-3 {
            width: 500px; height: 350px;
            background: radial-gradient(circle, rgba(76,29,149,.2) 0%, transparent 70%);
            bottom: -80px; left: 30%;
        }

        /* ── CONTENIDO ── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* ── TÍTULO ── */
        .brand-link {
            text-decoration: none;
            display: inline-block;
            transition: filter .25s;
        }
        .brand-link:hover .brand-title {
            filter: drop-shadow(0 0 35px rgba(124,58,237,1)) brightness(1.15);
        }
        .brand {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: enter .7s cubic-bezier(.22,1,.36,1) both;
        }
        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 900;
            letter-spacing: .15em;
            text-transform: uppercase;
            background: linear-gradient(135deg, #c4b5fd 0%, #7c3aed 50%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 25px rgba(124,58,237,.7));
        }
        .brand-subtitle {
            font-family: 'Cinzel', serif;
            font-size: clamp(.7rem, 1.5vw, .85rem);
            letter-spacing: .5em;
            text-transform: uppercase;
            color: rgba(167,139,250,.6);
            margin-top: .5rem;
        }
        .brand-divider {
            width: 180px;
            height: 1px;
            margin: 1rem auto;
            background: linear-gradient(90deg, transparent, rgba(124,58,237,.7), rgba(6,182,212,.5), transparent);
            position: relative;
        }
        .brand-divider::before, .brand-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 4px; height: 4px;
            border-radius: 50%;
            background: rgba(124,58,237,.8);
        }
        .brand-divider::before { left: -10px; }
        .brand-divider::after  { right: -10px; }

        /* ── CARD ── */
        .glass-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px rgba(0,0,0,.5),
                        inset 0 1px 0 rgba(255,255,255,.04);
            animation: enter .7s cubic-bezier(.22,1,.36,1) .15s both;
            position: relative;
            overflow: hidden;
        }
        .glass-card::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,58,237,.5), transparent);
            animation: shimmer 5s ease-in-out infinite;
        }

        @keyframes enter {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0%   { left: -100%; }
            100% { left: 200%; }
        }

        /* ── FORMULARIO ── */
        .form-title {
            font-family: 'Cinzel', serif;
            font-size: 1.3rem;
            font-weight: 700;
            text-align: center;
            color: #c4b5fd;
            margin-bottom: 1.75rem;
            letter-spacing: .08em;
        }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(167,139,250,.75);
            margin-bottom: .45rem;
        }
        .form-group input {
            width: 100%;
            padding: .7rem 1rem;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(124,58,237,.25);
            border-radius: .6rem;
            color: #e2e8f0;
            font-family: 'Raleway', sans-serif;
            font-size: .9rem;
            outline: none;
            transition: border-color .25s, box-shadow .25s, background .25s;
        }
        .form-group input:focus {
            border-color: rgba(124,58,237,.7);
            background: rgba(124,58,237,.07);
            box-shadow: 0 0 0 3px rgba(124,58,237,.12);
        }
        .form-group input::placeholder { color: rgba(148,163,184,.35); }

        .form-error {
            font-size: .75rem;
            color: #f87171;
            margin-top: .3rem;
            padding-left: .2rem;
        }

        /* ── BOTONES ── */
        .btn-epic {
            width: 100%;
            padding: .8rem 1.5rem;
            margin-top: 1.25rem;
            background: linear-gradient(135deg, #7c3aed, #1d4ed8);
            border: none;
            border-radius: .6rem;
            color: #fff;
            font-family: 'Cinzel', serif;
            font-size: .9rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform .2s, box-shadow .25s;
            box-shadow: 0 4px 18px rgba(124,58,237,.35);
        }
        .btn-epic:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(124,58,237,.55), var(--glow);
        }
        .btn-epic:active { transform: translateY(0); }

        .btn-primary {
            padding: .85rem 2.25rem;
            background: linear-gradient(135deg, #7c3aed, #1d4ed8);
            border: none;
            border-radius: .6rem;
            color: #fff;
            font-family: 'Cinzel', serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
            transition: transform .2s, box-shadow .25s;
            box-shadow: 0 4px 18px rgba(124,58,237,.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(124,58,237,.55), var(--glow);
            color: #fff;
        }
        .btn-secondary {
            padding: .85rem 2.25rem;
            background: transparent;
            border: 1px solid rgba(124,58,237,.45);
            border-radius: .6rem;
            color: #c4b5fd;
            font-family: 'Cinzel', serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
            transition: background .25s, border-color .25s, transform .2s, box-shadow .25s;
        }
        .btn-secondary:hover {
            background: rgba(124,58,237,.12);
            border-color: rgba(124,58,237,.8);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: var(--glow);
        }

        /* ── ENLACE ── */
        .form-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .82rem;
            color: rgba(148,163,184,.65);
        }
        .form-link a {
            color: #a78bfa;
            text-decoration: none;
            font-weight: 600;
            transition: color .2s;
        }
        .form-link a:hover { color: #c4b5fd; }

        /* ── HOME ── */
        .home-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: enter .7s cubic-bezier(.22,1,.36,1) .3s both;
        }
        .home-tagline {
            font-family: 'Cinzel', serif;
            font-size: clamp(.75rem, 1.5vw, .9rem);
            color: rgba(167,139,250,.55);
            letter-spacing: .3em;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: .75rem;
        }
        .home-desc {
            font-size: clamp(.82rem, 1.5vw, .95rem);
            color: rgba(148,163,184,.6);
            text-align: center;
            max-width: 460px;
            line-height: 1.8;
            animation: enter .7s cubic-bezier(.22,1,.36,1) .2s both;
        }

        /* ── ESQUINAS ── */
        .rune-corner {
            position: fixed;
            width: 100px; height: 100px;
            opacity: .12;
            pointer-events: none;
        }
        .rune-corner svg { width: 100%; height: 100%; }
        .rune-tl { top: 16px; left: 16px; }
        .rune-tr { top: 16px; right: 16px; transform: scaleX(-1); }
        .rune-bl { bottom: 16px; left: 16px; transform: scaleY(-1); }
        .rune-br { bottom: 16px; right: 16px; transform: scale(-1); }
    </style>
</head>
<body>
    <canvas id="star-canvas"></canvas>
    <div class="nebula nebula-1"></div>
    <div class="nebula nebula-2"></div>
    <div class="nebula nebula-3"></div>

    <div class="rune-corner rune-tl">
        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 95 L5 5 L95 5" stroke="#7c3aed" stroke-width="2"/>
            <path d="M5 50 L30 50 M50 5 L50 30" stroke="#06b6d4" stroke-width="1.5"/>
            <circle cx="5" cy="5" r="3" fill="#7c3aed"/>
            <rect x="20" y="20" width="10" height="10" stroke="#7c3aed" stroke-width="1" fill="none" transform="rotate(45 25 25)"/>
        </svg>
    </div>
    <div class="rune-corner rune-tr">
        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 95 L5 5 L95 5" stroke="#7c3aed" stroke-width="2"/>
            <path d="M5 50 L30 50 M50 5 L50 30" stroke="#06b6d4" stroke-width="1.5"/>
            <circle cx="5" cy="5" r="3" fill="#7c3aed"/>
            <rect x="20" y="20" width="10" height="10" stroke="#7c3aed" stroke-width="1" fill="none" transform="rotate(45 25 25)"/>
        </svg>
    </div>
    <div class="rune-corner rune-bl">
        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 95 L5 5 L95 5" stroke="#7c3aed" stroke-width="2"/>
            <path d="M5 50 L30 50 M50 5 L50 30" stroke="#06b6d4" stroke-width="1.5"/>
            <circle cx="5" cy="5" r="3" fill="#7c3aed"/>
            <rect x="20" y="20" width="10" height="10" stroke="#7c3aed" stroke-width="1" fill="none" transform="rotate(45 25 25)"/>
        </svg>
    </div>
    <div class="rune-corner rune-br">
        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 95 L5 5 L95 5" stroke="#7c3aed" stroke-width="2"/>
            <path d="M5 50 L30 50 M50 5 L50 30" stroke="#06b6d4" stroke-width="1.5"/>
            <circle cx="5" cy="5" r="3" fill="#7c3aed"/>
            <rect x="20" y="20" width="10" height="10" stroke="#7c3aed" stroke-width="1" fill="none" transform="rotate(45 25 25)"/>
        </svg>
    </div>

    <div class="page-wrapper">
        @yield('content')
    </div>

    <script>
        const canvas = document.getElementById('star-canvas');
        const ctx    = canvas.getContext('2d');
        let stars    = [];

        function resize() {
            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;
            initStars();
        }

        function initStars() {
            stars = [];
            const count = Math.floor((canvas.width * canvas.height) / 4500);
            for (let i = 0; i < count; i++) {
                stars.push({
                    x:     Math.random() * canvas.width,
                    y:     Math.random() * canvas.height,
                    r:     Math.random() * 1.4 + 0.2,
                    alpha: Math.random(),
                    speed: Math.random() * 0.004 + 0.001,
                    color: ['#fff','#c4b5fd','#93c5fd'][Math.floor(Math.random() * 3)],
                });
            }
        }

        function drawStars() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            stars.forEach(s => {
                s.alpha += s.speed;
                if (s.alpha > 1 || s.alpha < 0) s.speed *= -1;
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                ctx.fillStyle = s.color;
                ctx.globalAlpha = Math.max(0, Math.min(1, s.alpha));
                ctx.fill();
            });
            ctx.globalAlpha = 1;
            requestAnimationFrame(drawStars);
        }

        window.addEventListener('resize', resize);
        resize();
        drawStars();
    </script>
</body>
</html>
