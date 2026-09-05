{{-- Cinématique orbitale — Three.js (rendu) + Theatre.js (timeline).
     Reprise de Reference.mp4 : approche décélérante de la Terre avec la lune en
     transit, puis entrée atmosphérique, traversée des nuages, révélation de la
     ville, et remise de la main à Three.js pour les points d'intérêt.

     Une seule scène, une seule caméra, une seule boucle de rendu — la bascule
     espace → sol se fait sous le voile de nuages. --}}
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

    @vite(['resources/css/web.css', 'resources/js/cinematic/home-cinematic/home-cinematic-entry.js'])
</head>
<body class="page-wc">
@php($pages = [
    ['label' => 'Projets', 'url' => '/web-projet'],
    ['label' => 'Laboratoire', 'url' => '/web-lab'],
    ['label' => 'À propos', 'url' => '/web-a-propos'],
    ['label' => 'Contact', 'url' => '/web-a-propos#contact'],
])
<div data-wc-root style="position:relative; width:100%; height:100vh; min-height:560px; background:#05050a; overflow:hidden; font-family:Inter, system-ui, sans-serif; color:#d8d2c4; user-select:none; -webkit-font-smoothing:antialiased;">

    <orbital-stage
        drive="external"
        quality="{{ $quality ?? 'auto' }}"
        tex-base="{{ asset('textures') }}/"></orbital-stage>

    <div style="position:absolute; inset:0; pointer-events:none; z-index:2;">

        {{-- chrome de coin --}}
        <div data-wc="hud" style="opacity:0">
            <div style="position:absolute;top:0;left:0;right:0;height:130px;background:linear-gradient(180deg,rgba(5,5,10,0.5),rgba(5,5,10,0))"></div>
            <div style="position:absolute;bottom:0;left:0;right:0;height:190px;background:linear-gradient(0deg,rgba(5,5,10,0.58),rgba(5,5,10,0))"></div>
            <div style="position:absolute;top:0;left:0;right:0;display:flex;justify-content:space-between;align-items:flex-start;padding:28px 32px;font-size:10px;letter-spacing:0.34em;text-transform:uppercase;color:rgba(216,210,196,0.52)">
                <div style="display:flex;align-items:center;gap:10px"><span style="display:block;width:18px;height:1px;background:linear-gradient(90deg,transparent,rgba(216,210,196,0.6))"></span><span>{{ $brand ?? 'Lakeust Works' }}</span></div>
                <div style="display:flex;align-items:center;gap:12px"><span data-wc="phaseName" style="color:rgba(145,132,217,0.92)">orbite</span><span style="display:block;width:1px;height:10px;background:rgba(216,210,196,0.24)"></span><span>Descente</span></div>
            </div>
        </div>

        {{-- télémétrie : lue quatre fois par seconde sur stage.shot() --}}
        <div data-wc="readout" style="opacity:0;position:absolute;left:32px;bottom:34px;display:flex;flex-direction:column;gap:9px;font-size:10px;letter-spacing:0.16em;text-transform:uppercase;color:rgba(216,210,196,0.46);font-variant-numeric:tabular-nums">
            <div style="display:flex;align-items:center;gap:10px"><span style="display:block;width:22px;height:1px;background:linear-gradient(90deg,rgba(145,132,217,0.7),transparent)"></span><span>Trajectoire</span></div>
            <div style="display:grid;grid-template-columns:auto auto;gap:5px 16px;color:rgba(233,233,237,0.78)">
                <span style="color:rgba(216,210,196,0.38)">Altitude</span><span data-wc-num="alt">—</span>
                <span style="color:rgba(216,210,196,0.38)">Vitesse</span><span data-wc-num="speed">—</span>
                <span style="color:rgba(216,210,196,0.38)">Focale</span><span data-wc-num="fov">—</span>
            </div>
        </div>

        <div data-wc="signal" style="opacity:0;position:absolute;left:50%;top:50%;transform:translate3d(0,10px,0);margin-left:-160px;margin-top:-20px;width:320px;display:flex;flex-direction:column;align-items:center;gap:14px">
            <div style="position:relative;width:1px;height:40px;overflow:hidden;background:rgba(216,210,196,0.10)"><span style="position:absolute;inset:0;height:26%;background:linear-gradient(180deg,transparent,#9184d9);animation:wc-scan 2.8s linear infinite"></span></div>
            <div style="font-size:10px;letter-spacing:0.42em;text-transform:uppercase;color:rgba(233,233,237,0.72);animation:wc-pulse 3.2s ease-in-out infinite">Transit orbital</div>
        </div>

        <div data-wc="space" style="opacity:0;position:absolute;left:50%;top:50%;transform:translate3d(0,14px,0);margin-left:-220px;margin-top:-28px;width:440px;display:flex;flex-direction:column;align-items:center;gap:16px;text-align:center">
            <div style="font-size:11px;letter-spacing:0.46em;text-transform:uppercase;color:rgba(233,233,237,0.86)">Terre</div>
            <span style="display:block;width:180px;height:1px;background:linear-gradient(90deg,transparent,rgba(216,210,196,0.44),transparent)"></span>
            <div style="font-size:10px;letter-spacing:0.24em;text-transform:uppercase;color:rgba(216,210,196,0.40)">Orbite haute — approche</div>
        </div>

        <div data-wc="label" style="opacity:0;position:absolute;left:50%;top:50%;transform:translate3d(0,16px,0);margin-left:-190px;margin-top:-52px;width:380px;display:flex;flex-direction:column;align-items:center;gap:10px">
            <div style="padding:12px 20px 13px;border:1px solid rgba(145,132,217,0.42);border-radius:8px;background:rgba(9,9,16,0.58);backdrop-filter:blur(9px);display:flex;align-items:center;gap:16px;white-space:nowrap">
                <span style="font-size:13px;letter-spacing:0.22em;text-transform:uppercase;color:#e9e9ed">Lune en transit</span>
                <span style="width:1px;height:12px;background:rgba(216,210,196,0.24)"></span>
                <span style="font-size:10px;letter-spacing:0.24em;text-transform:uppercase;color:#9184d9">Conjonction</span>
            </div>
            <span style="display:block;width:1px;height:30px;background:linear-gradient(180deg,rgba(145,132,217,0.5),transparent)"></span>
        </div>

        <div data-wc="descent" style="opacity:0;position:absolute;left:50%;top:50%;transform:translate3d(0,14px,0);margin-left:-220px;margin-top:-24px;width:440px;display:flex;flex-direction:column;align-items:center;gap:14px;text-align:center">
            <div style="font-size:11px;letter-spacing:0.46em;text-transform:uppercase;color:rgba(233,233,237,0.86)">Entrée atmosphérique</div>
            <span style="display:block;width:150px;height:1px;background:linear-gradient(90deg,transparent,rgba(145,132,217,0.7),transparent)"></span>
        </div>

        <div data-wc="blind" style="opacity:0;position:absolute;left:50%;top:50%;transform:translate3d(0,12px,0);margin-left:-200px;margin-top:-22px;width:400px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center">
            <div style="font-size:10px;letter-spacing:0.42em;text-transform:uppercase;color:rgba(233,233,237,0.78)">Traversée nuageuse</div>
            <div style="font-size:10px;letter-spacing:0.24em;text-transform:uppercase;color:rgba(216,210,196,0.44);animation:wc-pulse 2.4s ease-in-out infinite">Descente maintenue</div>
        </div>

        <div data-wc="city" style="opacity:0;position:absolute;left:50%;top:50%;transform:translate3d(0,16px,0);margin-left:-190px;margin-top:60px;width:380px;display:flex;flex-direction:column;align-items:center;gap:10px">
            <span style="display:block;width:1px;height:30px;background:linear-gradient(0deg,rgba(145,132,217,0.5),transparent)"></span>
            <div style="padding:12px 20px 13px;border:1px solid rgba(145,132,217,0.42);border-radius:8px;background:rgba(9,9,16,0.58);backdrop-filter:blur(9px);display:flex;align-items:center;gap:16px;white-space:nowrap">
                <span style="font-size:13px;letter-spacing:0.22em;text-transform:uppercase;color:#e9e9ed">{{ $district ?? 'Complexe central' }}</span>
                <span style="width:1px;height:12px;background:rgba(216,210,196,0.24)"></span>
                <span style="font-size:10px;letter-spacing:0.24em;text-transform:uppercase;color:#9184d9">Accès sud</span>
            </div>
        </div>

        <div data-wc="title" style="opacity:0;position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:24px;transform:translate3d(0,26px,0);background:radial-gradient(58% 42% at 50% 47%,rgba(6,7,12,0.62) 0%,rgba(6,7,12,0.32) 52%,rgba(6,7,12,0) 76%)">
            <div style="display:flex;font-size:clamp(30px,7.2vw,84px);font-weight:200;letter-spacing:0.30em;color:#f3ede2;text-indent:0.30em;line-height:1;text-shadow:0 2px 30px rgba(6,7,12,0.6)"><span>L</span><span>A</span><span>K</span><span>E</span><span>U</span><span>S</span><span>T</span></div>
            <div style="display:flex;align-items:center;gap:20px"><span style="display:block;width:clamp(34px,8vw,92px);height:1px;background:linear-gradient(90deg,transparent,rgba(196,188,174,0.9))"></span><span style="font-size:clamp(11px,1.5vw,17px);font-weight:300;letter-spacing:0.62em;text-indent:0.62em;color:#c4bcae;text-shadow:0 1px 18px rgba(6,7,12,0.6)">WORKS</span><span style="display:block;width:clamp(34px,8vw,92px);height:1px;background:linear-gradient(270deg,transparent,rgba(196,188,174,0.9))"></span></div>
        </div>

        {{-- Points d'intérêt : le stage les projette une fois la main rendue à Three.js --}}
        <div data-wc="pages" style="opacity:0;position:absolute;inset:0;pointer-events:none;transition:opacity .8s ease">
            @foreach ($pages as $page)
                <a class="ob-spot" data-orb-spot href="{{ $page['url'] }}"><span class="ring"></span><span class="rule"></span><span class="name">{{ $page['label'] }}</span></a>
            @endforeach
        </div>

        <div data-wc="enter" style="opacity:0;position:absolute;left:50%;bottom:84px;margin-left:-170px;width:340px;display:flex;flex-direction:column;align-items:center;gap:18px;transform:translate3d(0,12px,0);pointer-events:auto">
            <div style="display:flex;gap:12px">
                <a href="{{ $homeUrl ?? '/' }}" class="wc-btn">Entrer</a>
                <button type="button" class="wc-btn wc-btn-ghost" data-wc="replay">Rejouer</button>
            </div>
            <div style="font-size:10px;letter-spacing:0.30em;text-transform:uppercase;color:rgba(216,210,196,0.34)">Fin de la séquence</div>
        </div>

        <button type="button" data-wc="skip" style="opacity:0;position:absolute;right:32px;bottom:34px;pointer-events:none;border:1px solid rgba(216,210,196,0.20);border-radius:8px;background:transparent;color:rgba(216,210,196,0.66);font:inherit;font-size:10px;letter-spacing:0.26em;text-transform:uppercase;padding:10px 18px;cursor:pointer">Passer</button>
    </div>
</div>
</body>
</html>
