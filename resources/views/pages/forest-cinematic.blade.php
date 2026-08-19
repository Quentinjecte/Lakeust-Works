{{-- Forêt cinématique — Three.js (rendu, forest-stage.js) + Theatre.js (timeline,
     forest-cinematic.js). Même architecture à trois couches que /welcome-cinematic,
     appliquée au décor dirt_road_forest.glb : la caméra suit l'axe de la route de terre,
     en plein midi — départ sous les arbres → la route s'ouvre → les lacets → la caméra
     s'élève au-dessus du dernier virage et le titre monte.

     Trois calques passifs finissent l'image côté DOM, sous les cartons : un vignetage,
     un grain, et un voile radial derrière le titre — sans lui les lettres se perdent
     dans le ciel. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Lakeust Works' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/js/forest-cinematic-entry.js'])

    <style>
        html, body { margin: 0; padding: 0; background: #05050a; overflow: hidden; }
        a { color: #d8d2c4; text-decoration: none; }
        a:hover { color: #ffffff; }
        @keyframes wc-pulse { 0%, 100% { opacity: .35 } 50% { opacity: .9 } }
        @keyframes wc-scan { 0% { transform: translateY(-100%) } 100% { transform: translateY(400%) } }
        .wc-btn { border: 1px solid rgba(145,132,217,0.5); border-radius: 8px; background: transparent;
                  color: #e9e9ed; font-family: inherit; font-size: 10px; letter-spacing: .26em;
                  text-transform: uppercase; padding: 11px 22px; cursor: pointer; transition: background .2s, border-color .2s; }
        .wc-btn:hover { background: rgba(145,132,217,0.14); border-color: rgba(145,132,217,0.8); }
        .wc-btn:active { background: rgba(145,132,217,0.24); }
        .wc-btn:focus-visible { outline: 2px solid #9184d9; outline-offset: 2px; }
        .wc-btn-ghost { border-color: rgba(216,210,196,0.22); color: rgba(216,210,196,0.72); }
        .wc-vignette { position: absolute; inset: 0; pointer-events: none;
                       background: radial-gradient(126% 104% at 50% 46%, rgba(8,10,14,0) 44%, rgba(8,10,14,0.16) 80%, rgba(8,10,14,0.36) 100%); }
        .wc-grain { position: absolute; inset: 0; pointer-events: none; opacity: .05; mix-blend-mode: overlay;
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)'/%3E%3C/svg%3E"); }
    </style>
</head>
<body>
<div data-wc-root style="position:relative; width:100%; height:100vh; min-height:560px; background:#05050a; overflow:hidden; font-family:Inter, system-ui, sans-serif; color:#d8d2c4; user-select:none; -webkit-font-smoothing:antialiased;">

    <forest-stage src="/GLB/dirt_road_forest.glb"></forest-stage>

    <div class="wc-vignette" style="z-index:1;"></div>
    <div class="wc-grain" style="z-index:1;"></div>

    <div style="position:absolute; inset:0; pointer-events:none; z-index:2;">

        <div data-wc="hud" style="opacity:0; transition:opacity .2s linear;">
            <div style="position:absolute; top:0; left:0; right:0; height:130px; background:linear-gradient(180deg, rgba(8,10,14,0.46), rgba(8,10,14,0));"></div>
            <div style="position:absolute; bottom:0; left:0; right:0; height:200px; background:linear-gradient(0deg, rgba(8,10,14,0.62), rgba(8,10,14,0));"></div>
            <div style="position:absolute; top:0; left:0; right:0; display:flex; justify-content:space-between; align-items:flex-start; padding:28px 32px; font-size:10px; letter-spacing:0.34em; text-transform:uppercase; color:rgba(216,210,196,0.52);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="display:block; width:18px; height:1px; background:linear-gradient(90deg, transparent, rgba(216,210,196,0.6));"></span>
                    <span>Lakeust Works</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span data-wc="phaseName" style="color:rgba(145,132,217,0.92);">départ</span>
                    <span style="display:block; width:1px; height:10px; background:rgba(216,210,196,0.24);"></span>
                    <span>Forêt</span>
                </div>
            </div>
        </div>

        <div data-wc="readout" style="opacity:0; position:absolute; left:32px; bottom:34px; display:flex; flex-direction:column; gap:9px; font-size:10px; letter-spacing:0.16em; text-transform:uppercase; color:rgba(216,210,196,0.46); font-variant-numeric:tabular-nums;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="display:block; width:22px; height:1px; background:linear-gradient(90deg, rgba(145,132,217,0.7), transparent);"></span>
                <span>Navigation</span>
            </div>
            <div style="display:grid; grid-template-columns:auto auto; gap:5px 16px; color:rgba(233,233,237,0.78);">
                <span style="color:rgba(216,210,196,0.38);">Altitude</span><span data-wc-num="alt">—</span>
                <span style="color:rgba(216,210,196,0.38);">Vitesse</span><span data-wc-num="speed">—</span>
                <span style="color:rgba(216,210,196,0.38);">Cap</span><span data-wc-num="heading">—</span>
            </div>
        </div>

        <div data-wc="signal" style="opacity:0; position:absolute; left:50%; top:50%; transform:translate3d(0,10px,0); margin-left:-160px; margin-top:-20px; width:320px; display:flex; flex-direction:column; align-items:center; gap:14px;">
            <div style="position:relative; width:1px; height:40px; overflow:hidden; background:rgba(216,210,196,0.10);">
                <span style="position:absolute; inset:0; height:26%; background:linear-gradient(180deg, transparent, #9184d9); animation:wc-scan 2.8s linear infinite;"></span>
            </div>
            <div style="font-size:10px; letter-spacing:0.42em; text-transform:uppercase; color:rgba(233,233,237,0.72); animation:wc-pulse 3.2s ease-in-out infinite;">Chemin repéré</div>
        </div>

        <div data-wc="space" style="opacity:0; position:absolute; left:50%; top:50%; transform:translate3d(0,14px,0); margin-left:-220px; margin-top:-28px; width:440px; display:flex; flex-direction:column; align-items:center; gap:16px; text-align:center;">
            <div style="font-size:11px; letter-spacing:0.46em; text-transform:uppercase; color:rgba(233,233,237,0.86);">Sous-bois</div>
            <span style="display:block; width:180px; height:1px; background:linear-gradient(90deg, transparent, rgba(216,210,196,0.44), transparent);"></span>
            <div style="font-size:10px; letter-spacing:0.24em; text-transform:uppercase; color:rgba(216,210,196,0.40);">Forêt tempérée — plein midi</div>
        </div>

        <div data-wc="label" style="opacity:0; position:absolute; left:50%; top:50%; transform:translate3d(0,16px,0); margin-left:-190px; margin-top:-52px; width:380px; display:flex; flex-direction:column; align-items:center; gap:10px;">
            <div style="padding:12px 20px 13px; border:1px solid rgba(145,132,217,0.42); border-radius:8px; background:rgba(9,9,16,0.58); backdrop-filter:blur(9px); display:flex; align-items:center; gap:16px; white-space:nowrap;">
                <span style="font-size:13px; letter-spacing:0.22em; text-transform:uppercase; color:#e9e9ed;">Route forestière</span>
                <span style="width:1px; height:12px; background:rgba(216,210,196,0.24);"></span>
                <span style="font-size:10px; letter-spacing:0.24em; text-transform:uppercase; color:#9184d9;">Secteur 12</span>
            </div>
            <span style="display:block; width:1px; height:30px; background:linear-gradient(180deg, rgba(145,132,217,0.5), transparent);"></span>
        </div>

        <div data-wc="title" style="opacity:0; position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:24px; transform:translate3d(0,26px,0); background:radial-gradient(58% 42% at 50% 47%, rgba(6,7,12,0.68) 0%, rgba(6,7,12,0.36) 52%, rgba(6,7,12,0) 76%);">
            <div style="display:flex; font-size:clamp(30px, 7.2vw, 84px); font-weight:200; letter-spacing:0.30em; color:#f3ede2; text-indent:0.30em; line-height:1; text-shadow:0 2px 30px rgba(6,7,12,0.6);">
                <span>L</span><span>A</span><span>K</span><span>E</span><span>U</span><span>S</span><span>T</span>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <span style="display:block; width:clamp(34px, 8vw, 92px); height:1px; background:linear-gradient(90deg, transparent, rgba(196,188,174,0.9));"></span>
                <span style="font-size:clamp(11px, 1.5vw, 17px); font-weight:300; letter-spacing:0.62em; text-indent:0.62em; color:#c4bcae; text-shadow:0 1px 18px rgba(6,7,12,0.6);">WORKS</span>
                <span style="display:block; width:clamp(34px, 8vw, 92px); height:1px; background:linear-gradient(270deg, transparent, rgba(196,188,174,0.9));"></span>
            </div>
        </div>

        <div data-wc="enter" style="opacity:0; position:absolute; left:50%; bottom:84px; margin-left:-170px; width:340px; display:flex; flex-direction:column; align-items:center; gap:18px; transform:translate3d(0,12px,0); pointer-events:auto;">
            <div style="display:flex; gap:12px;">
                <a href="{{ $homeUrl ?? '/' }}" class="wc-btn">Entrer</a>
                <button type="button" data-wc="replay" class="wc-btn wc-btn-ghost">Rejouer</button>
            </div>
            <div style="font-size:10px; letter-spacing:0.30em; text-transform:uppercase; color:rgba(216,210,196,0.34);">Fin de la séquence</div>
        </div>

        <div data-wc="flash" style="opacity:0; position:absolute; inset:0; background:radial-gradient(circle at 50% 50%, rgba(255,244,222,0.95) 0%, rgba(255,214,150,0.55) 16%, rgba(255,180,90,0.20) 32%, rgba(255,180,90,0) 55%); mix-blend-mode:screen;"></div>
        <div data-wc="curtain" style="opacity:0; position:absolute; inset:0; background:#05050a;"></div>

        <button type="button" data-wc="skip" class="wc-btn wc-btn-ghost" style="position:absolute; right:28px; bottom:30px; font-size:9px; letter-spacing:0.28em; padding:8px 14px; pointer-events:auto; opacity:0.5;">Passer</button>
    </div>
</div>
</body>
</html>
