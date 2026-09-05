{{-- Welcome cinématique — Three.js (rendu) + Theatre.js (timeline).
     Le trou noir est celui de la page welcome : même élément, même shaders, même
     boucle de rendu, piloté ici en drive="external" (voir resources/js/three/blackhole.js). --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Lakeust Works' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">
    @include('partials.analytics')

    @vite(['resources/css/web.css', 'resources/js/cinematic/blackhole-cinematic/blackhole-cinematic-entry.js'])
</head>
<body class="page-wc">
<div data-wc-root style="position:relative; width:100%; height:100vh; min-height:560px; background:#05050a; overflow:hidden; font-family:Inter, system-ui, sans-serif; color:#d8d2c4; user-select:none; -webkit-font-smoothing:antialiased;">

    <black-hole-stage
        drive="external"
        pages="[]"
        quality="{{ $quality ?? 'auto' }}"
        disk-palette="{{ $diskPalette ?? 'bone' }}"></black-hole-stage>

    <div style="position:absolute; inset:0; pointer-events:none; z-index:2;">

        <div data-wc="hud" style="opacity:0; transition:opacity .2s linear;">
            <div style="position:absolute; top:0; left:0; right:0; height:130px; background:linear-gradient(180deg, rgba(5,5,10,0.66), rgba(5,5,10,0));"></div>
            <div style="position:absolute; bottom:0; left:0; right:0; height:200px; background:linear-gradient(0deg, rgba(5,5,10,0.88), rgba(5,5,10,0));"></div>
            <div style="position:absolute; top:0; left:0; right:0; display:flex; justify-content:space-between; align-items:flex-start; padding:28px 32px; font-size:10px; letter-spacing:0.34em; text-transform:uppercase; color:rgba(216,210,196,0.52);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="display:block; width:18px; height:1px; background:linear-gradient(90deg, transparent, rgba(216,210,196,0.6));"></span>
                    <span>Lakeust Works</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span data-wc="phaseName" style="color:rgba(145,132,217,0.92);">initialisation</span>
                    <span style="display:block; width:1px; height:10px; background:rgba(216,210,196,0.24);"></span>
                    <span>Séquence 02</span>
                </div>
            </div>
        </div>

        <div data-wc="readout" style="opacity:0; position:absolute; left:32px; bottom:34px; display:flex; flex-direction:column; gap:9px; font-size:10px; letter-spacing:0.16em; text-transform:uppercase; color:rgba(216,210,196,0.46); font-variant-numeric:tabular-nums;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="display:block; width:22px; height:1px; background:linear-gradient(90deg, rgba(145,132,217,0.7), transparent);"></span>
                <span>Télémétrie</span>
            </div>
            <div style="display:grid; grid-template-columns:auto auto; gap:5px 16px; color:rgba(233,233,237,0.78);">
                <span style="color:rgba(216,210,196,0.38);">Rayon</span><span data-wc-num="dist">—</span>
                <span style="color:rgba(216,210,196,0.38);">Lentille</span><span data-wc-num="lens">—</span>
                <span style="color:rgba(216,210,196,0.38);">Champ</span><span data-wc-num="fov">—</span>
            </div>
        </div>

        <div data-wc="signal" style="opacity:0; position:absolute; left:50%; top:50%; transform:translate3d(0,10px,0); margin-left:-160px; margin-top:-20px; width:320px; display:flex; flex-direction:column; align-items:center; gap:14px;">
            <div style="position:relative; width:1px; height:40px; overflow:hidden; background:rgba(216,210,196,0.10);">
                <span style="position:absolute; inset:0; height:26%; background:linear-gradient(180deg, transparent, #9184d9); animation:wc-scan 2.8s linear infinite;"></span>
            </div>
            <div style="font-size:10px; letter-spacing:0.42em; text-transform:uppercase; color:rgba(233,233,237,0.72); animation:wc-pulse 3.2s ease-in-out infinite;">Signal capté</div>
        </div>

        <div data-wc="space" style="opacity:0; position:absolute; left:50%; top:50%; transform:translate3d(0,14px,0); margin-left:-220px; margin-top:-28px; width:440px; display:flex; flex-direction:column; align-items:center; gap:16px; text-align:center;">
            <div style="font-size:11px; letter-spacing:0.46em; text-transform:uppercase; color:rgba(233,233,237,0.86);">Champ profond</div>
            <span style="display:block; width:180px; height:1px; background:linear-gradient(90deg, transparent, rgba(216,210,196,0.44), transparent);"></span>
            <div style="font-size:10px; letter-spacing:0.24em; text-transform:uppercase; color:rgba(216,210,196,0.40);">26 000 années-lumière du centre galactique</div>
        </div>

        <div data-wc="label" style="opacity:0; position:absolute; left:50%; top:50%; transform:translate3d(0,16px,0); margin-left:-190px; margin-top:-52px; width:380px; display:flex; flex-direction:column; align-items:center; gap:10px;">
            <div style="padding:12px 20px 13px; border:1px solid rgba(145,132,217,0.42); border-radius:8px; background:rgba(9,9,16,0.58); backdrop-filter:blur(9px); display:flex; align-items:center; gap:16px; white-space:nowrap;">
                <span style="font-size:13px; letter-spacing:0.22em; text-transform:uppercase; color:#e9e9ed;">Sagittarius A&#42;</span>
                <span style="width:1px; height:12px; background:rgba(216,210,196,0.24);"></span>
                <span style="font-size:10px; letter-spacing:0.24em; text-transform:uppercase; color:#9184d9;">4,3 millions M&#9737;</span>
            </div>
            <span style="display:block; width:1px; height:30px; background:linear-gradient(180deg, rgba(145,132,217,0.5), transparent);"></span>
        </div>

        <div data-wc="title" style="opacity:0; position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:24px; transform:translate3d(0,26px,0);">
            <div style="display:flex; font-size:clamp(30px, 7.2vw, 84px); font-weight:200; letter-spacing:0.30em; color:#e8e1d5; text-indent:0.30em; line-height:1;">
                <span>L</span><span>A</span><span>K</span><span>E</span><span>U</span><span>S</span><span>T</span>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <span style="display:block; width:clamp(34px, 8vw, 92px); height:1px; background:linear-gradient(90deg, transparent, rgba(138,131,120,0.9));"></span>
                <span style="font-size:clamp(11px, 1.5vw, 17px); font-weight:300; letter-spacing:0.62em; text-indent:0.62em; color:#8a8378;">WORKS</span>
                <span style="display:block; width:clamp(34px, 8vw, 92px); height:1px; background:linear-gradient(270deg, transparent, rgba(138,131,120,0.9));"></span>
            </div>
        </div>

        <div data-wc="enter" style="opacity:0; position:absolute; left:50%; bottom:84px; margin-left:-170px; width:340px; display:flex; flex-direction:column; align-items:center; gap:18px; transform:translate3d(0,12px,0); pointer-events:auto;">
            <div style="display:flex; gap:12px;">
                <a href="{{ $homeUrl ?? '/' }}" class="wc-btn">Entrer</a>
                <button type="button" data-wc="replay" class="wc-btn wc-btn-ghost">Rejouer</button>
            </div>
            <div style="font-size:10px; letter-spacing:0.30em; text-transform:uppercase; color:rgba(216,210,196,0.34);">Fin de la séquence</div>
        </div>

        <div data-wc="flash" style="opacity:0; position:absolute; inset:0; background:radial-gradient(circle at 50% 50%, rgba(233,233,237,0.98) 0%, rgba(200,192,236,0.62) 14%, rgba(145,132,217,0.22) 30%, rgba(145,132,217,0) 52%); mix-blend-mode:screen;"></div>
        <div data-wc="curtain" style="opacity:0; position:absolute; inset:0; background:#05050a;"></div>

        <button type="button" data-wc="skip" class="wc-btn wc-btn-ghost" style="position:absolute; right:28px; bottom:30px; font-size:9px; letter-spacing:0.28em; padding:8px 14px; pointer-events:auto; opacity:0.5;">Passer</button>
    </div>
</div>
</body>
</html>
