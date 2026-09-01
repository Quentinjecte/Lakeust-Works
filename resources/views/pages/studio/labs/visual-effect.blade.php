<!-- Visual Effect Lab — captures VFX Unity (VFX Graph / Shader Graph),
     showcase simple : pas de mécanique JS dédiée, juste des vidéos avec
     leur contexte. -->

@extends('layouts.site')
@section('title', 'Visual Effect Lab — Lakeust Works')
@section('cat', 'Studio')

@section('content')
<head>
    @vite(['resources/css/lab.css'])

    @php
        $nav = [
            ['route' => 'studio.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'studio.works',   'i18n' => 'nav.games',  'keep' => true],
            ['route' => 'studio.lab',     'i18n' => 'nav.lab',  'keep' => false, 'external' => true],
        ];

        $vfx = [
            ['file' => 'blackhole', 'poster' => 'blackhole-poster.png', 'name' => 'Black Hole', 'tag' => 'VFX Graph · Shader',
                'fr' => "Disque d'accrétion et distorsion procédurale, construits avec VFX Graph et un shader dédié.",
                'en' => 'Accretion disk and procedural distortion, built with VFX Graph and a dedicated shader.'],
            ['file' => 'dissolve', 'poster' => null, 'name' => 'Dissolve', 'tag' => 'Shader Graph',
                'fr' => "Dissolution d'un objet : un bord lumineux balaie le mesh avant sa disparition.",
                'en' => 'Object dissolve: a glowing edge sweeps across the mesh before it disappears.'],
            ['file' => 'radar', 'poster' => 'radar-poster.png', 'name' => 'Radar', 'tag' => 'UI · Détection',
                'fr' => 'Radar de détection en temps réel — les contacts sont reportés sur une carte.',
                'en' => 'Real-time detection radar — contacts plotted on a map display.'],
            ['file' => 'ultra-beam', 'poster' => 'ultra-beam-poster.png', 'name' => 'Ultra-Beam', 'tag' => 'VFX Graph · Particules',
                'fr' => "Faisceau à particules multi-couches piloté par VFX Graph, du démarrage jusqu'au tir continu.",
                'en' => 'Multi-layered particle beam driven by VFX Graph, from start-up to sustained fire.'],
        ];
    @endphp
</head>

<div style="position:relative;min-height:100vh;background:#05050a">
    <main style="position:relative;z-index:1">
        <section style="min-height:44vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:clamp(96px,14vh,150px) clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
            <span data-reveal="rise" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
            <h1 data-reveal="rise" style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Visual Effect</h1>
            <p data-reveal="rise" style="margin:0;max-width:60ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">
                <span class="i18n-fr">Quatre effets Unity construits sur VFX Graph et Shader Graph — trou noir, dissolution, radar de détection et faisceau à particules.</span>
                <span class="i18n-en">Four Unity effects built on VFX Graph and Shader Graph — a black hole, an object dissolve, a detection radar and a particle beam.</span>
            </p>
        </section>

        <section style="padding:0 clamp(56px,9vw,180px) clamp(80px,10vh,140px)">
            <div style="display:grid;gap:24px;grid-template-columns:repeat(auto-fit,minmax(min(100%,420px),1fr))">
                @foreach ($vfx as $i => $v)
                    <article data-reveal="rise" style="display:flex;flex-direction:column;gap:14px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55);border-radius:10px;overflow:hidden">
                        <div style="position:relative;aspect-ratio:16/9;background:#000">
                            <video
                                src="{{ asset('videos/vfx/' . $v['file'] . '.mp4') }}"
                                @if ($v['poster']) poster="{{ asset('images/vfx/' . $v['poster']) }}" @endif
                                controls preload="metadata" playsinline
                                style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:10px;padding:6px 22px 22px">
                            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                                <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)">{{ $v['tag'] }}</span>
                            </div>
                            <h3 style="margin:0;font-size:clamp(19px,2vw,24px);font-weight:200;letter-spacing:-.01em">{{ $v['name'] }}</h3>
                            <p style="margin:0;font-size:13.5px;line-height:1.65;color:rgba(226,221,209,.62)">
                                <span class="i18n-fr">{{ $v['fr'] }}</span><span class="i18n-en">{{ $v['en'] }}</span>
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</div>
@endsection
