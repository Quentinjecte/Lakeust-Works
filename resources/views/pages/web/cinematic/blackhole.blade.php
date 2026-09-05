<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Black Hole — Lakeust Works</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">
    @include('partials.analytics')

    @vite(['resources/css/web.css', 'resources/js/three/blackhole.js'])
</head>
<body class="page-bh">
@php
    /* Navigation objects around the horizon — one per portfolio page.
       Add an entry here (and its route) to add a navigation point. */
    $pages = [
        ['name' => 'A propos',          'url' => '/web-a-propos'],
        ['name' => 'Projet',            'url' => '/web-projet'],
        ['name' => 'Laboratoire',       'url' => '/three-lab'],
        ['name' => 'Forest Cinematic',       'url' => '/forest-cinematic'],
        ['name' => 'BlackHole Cinematic',       'url' => '/blackhole-cinematic'],
        ['name' => 'Orbital',       'url' => '/home-cinematic'],
    ];
@endphp
        <!--['name' => 'Three.js Test',     'url' => '/three-test'],-->

<div style="position:relative; width:100%; height:100vh; min-height:560px; background:#05050a; overflow:hidden;">

    <black-hole-stage
        pages="{{ json_encode($pages) }}"
        home-url=""
        duration="15"
        quality="auto"
        disk-palette="violet"></black-hole-stage>

    <div style="position:absolute; inset:0; pointer-events:none;">

        <div style="position:absolute; top:0; left:0; right:0; height:150px; background:linear-gradient(180deg, rgba(5,5,10,0.72), rgba(5,5,10,0));"></div>
        <div style="position:absolute; bottom:0; left:0; right:0; height:240px; background:linear-gradient(0deg, rgba(5,5,10,0.94) 0%, rgba(5,5,10,0.82) 34%, rgba(5,5,10,0.34) 68%, rgba(5,5,10,0));"></div>
        <div style="position:absolute; top:0; bottom:0; right:0; width:190px; background:linear-gradient(270deg, rgba(5,5,10,0.80), rgba(5,5,10,0));"></div>

        <div style="position:absolute; top:0; left:0; right:0; display:flex; justify-content:space-between; align-items:flex-start; padding:28px 32px; font-size:10px; letter-spacing:0.34em; text-transform:uppercase; color:rgba(216,210,196,0.42);">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="display:block; width:18px; height:1px; background:linear-gradient(90deg, transparent, rgba(216,210,196,0.6));"></span>
                <span>Lakeust Works</span>
            </div>
            <div style="text-align:right;">Sagittarius&nbsp;A&#42; &#8212; observation deck</div>
        </div>

        <div data-bh="label" style="position:absolute; left:0; top:0; opacity:0; transform:translate(-50%,-50%); transition:opacity .28s ease; will-change:transform,opacity;">
            <div style="display:flex; flex-direction:column; align-items:center; gap:8px; transform:translateY(-46px);">
                <div style="padding:9px 16px 10px; border:1px solid rgba(145,132,217,0.42); border-radius:8px; background:rgba(9,9,16,0.62); backdrop-filter:blur(9px); display:flex; align-items:center; gap:14px; white-space:nowrap;">
                    <span data-bh="labelName" style="font-size:12px; letter-spacing:0.20em; text-transform:uppercase; color:#e6e0d3;"></span>
                    <span style="width:1px; height:11px; background:rgba(216,210,196,0.24);"></span>
                    <span style="font-size:10px; letter-spacing:0.24em; text-transform:uppercase; color:#9184d9;">Enter</span>
                </div>
                <span style="display:block; width:1px; height:26px; background:linear-gradient(180deg, rgba(145,132,217,0.5), transparent);"></span>
            </div>
        </div>

        <div data-bh="rail" style="position:absolute; right:32px; top:50%; transform:translateY(-50%); display:flex; flex-direction:column; align-items:center; gap:14px; transition:opacity .6s ease;">
            <span style="font-size:9px; letter-spacing:0.32em; text-transform:uppercase; color:rgba(216,210,196,0.3); writing-mode:vertical-rl;">Approach</span>
            <div style="position:relative; width:1px; height:132px; background:linear-gradient(180deg, transparent, rgba(216,210,196,0.22) 18%, rgba(216,210,196,0.22) 82%, transparent);">
                <span data-bh="tick" style="position:absolute; left:-3px; top:0; width:7px; height:7px; border-radius:50%; background:#d8d2c4; box-shadow:0 0 10px rgba(216,210,196,0.9); transform:translateY(-3px);"></span>
                <span style="position:absolute; left:-5px; top:78%; width:11px; height:1px; background:rgba(145,132,217,0.75);"></span>
            </div>
            <span data-bh="pct" style="font-size:10px; letter-spacing:0.14em; color:rgba(216,210,196,0.78); font-variant-numeric:tabular-nums;">0%</span>
        </div>

        <div data-bh="hint" style="position:absolute; bottom:38px; left:50%; transform:translateX(-50%); display:flex; flex-direction:column; align-items:center; gap:12px; transition:opacity .8s ease;">
            <div style="font-size:10px; letter-spacing:0.34em; text-transform:uppercase; color:rgba(233,233,237,0.92); text-shadow:0 1px 14px rgba(5,5,10,0.9); animation:bh-breathe 3.4s ease-in-out infinite;">Scroll to approach</div>
            <div style="position:relative; width:1px; height:34px; overflow:hidden; background:rgba(216,210,196,0.10);">
                <span style="position:absolute; inset:0; background:linear-gradient(180deg, transparent, #d8d2c4); animation:bh-drift 2.4s ease-in-out infinite;"></span>
            </div>
        </div>

        <div data-bh="warn" style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); opacity:0; text-align:center; transition:opacity .5s ease;">
            <div style="font-size:11px; letter-spacing:0.42em; text-transform:uppercase; color:rgba(145,132,217,0.92);">Point of no return</div>
            <div style="margin-top:12px; font-size:10px; letter-spacing:0.18em; color:rgba(216,210,196,0.4);">Gravity has taken the wheel</div>
        </div>

        <div data-bh="reveal" style="position:absolute; inset:0; display:none; flex-direction:column; align-items:center; justify-content:center; gap:26px; pointer-events:auto;">
            <div style="display:flex; flex-direction:column; align-items:center; gap:22px;">
                <div style="display:flex; font-size:clamp(30px, 7.2vw, 84px); font-weight:200; letter-spacing:0.30em; color:#e8e1d5; text-indent:0.30em; line-height:1;">
                    <span data-letter="1" style="display:inline-block;">L</span><span data-letter="1" style="display:inline-block;">A</span><span data-letter="1" style="display:inline-block;">K</span><span data-letter="1" style="display:inline-block;">E</span><span data-letter="1" style="display:inline-block;">U</span><span data-letter="1" style="display:inline-block;">S</span><span data-letter="1" style="display:inline-block;">T</span>
                </div>
                <div data-bh="sub" style="display:flex; align-items:center; gap:20px;">
                    <span style="display:block; width:clamp(34px, 8vw, 92px); height:1px; background:linear-gradient(90deg, transparent, rgba(138,131,120,0.9));"></span>
                    <span style="font-size:clamp(11px, 1.5vw, 17px); font-weight:300; letter-spacing:0.62em; text-indent:0.62em; color:#8a8378;">WORKS</span>
                    <span style="display:block; width:clamp(34px, 8vw, 92px); height:1px; background:linear-gradient(270deg, transparent, rgba(138,131,120,0.9));"></span>
                </div>
            </div>
            <div data-bh="outro" style="display:flex; flex-direction:column; align-items:center; gap:18px; margin-top:16px;">
                <div style="font-size:10px; letter-spacing:0.32em; text-transform:uppercase; color:rgba(216,210,196,0.38);">End of the journey</div>
                <button type="button" data-bh="again" style="font-size:10px; letter-spacing:0.26em; text-transform:uppercase; padding:9px 18px; cursor:pointer; background:transparent; color:#d8d2c4; border:1px solid rgba(145,132,217,0.55); border-radius:8px;">Fall again</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
