@extends('layouts.site')
@section('title', 'Laboratoire — Lakeust Studio')
@section('content')
<head>
    @vite(['resources/css/lab.css', 'resources/js/labs/catalogue/lab-catalogue.js'])

    @php
        /* Laboratoire Studio : mécaniques Unity capturées en vidéo — chaque
           page est un showcase, pas une démo interactive à recréer en JS. */
        $sections = [
            [
                'url' => '/visual-effect',
                'tag' => "Ω · 01",
                'name' => "Visual Effect",
                'built' => 1,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.vfx',
                'desc' => "Quatre effets Unity construits sur VFX Graph et Shader Graph — trou noir, dissolution, radar de détection et faisceau à particules."
            ],
            [
                'url' => '/csharp-code',
                'tag' => "Ω · 02",
                'name' => "C# Code",
                'built' => 2,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.csharp',
                'desc' => "Trois systèmes plus proches de l'algorithme que du visuel — détection et ciblage, placement en grille, performance ECS/DOTS."
            ],
        ];

    $nav = [
        ['route' => 'studio.about',   'i18n' => 'nav.about', 'keep' => true],
        ['route' => 'studio.works',   'i18n' => 'nav.games',  'keep' => true],
        ['route' => 'studio.lab',     'i18n' => 'nav.lab', 'keep' => false, 'external' => true],
    ];
@endphp
</head>

    <div data-lab-root style="position:relative;min-height:100vh;background:#05050a">
        <main style="position:relative;z-index:1">
            {{-- ================================================ Catalogue --- --}}
            <section class="lab-sec" data-lab="catalogue" data-screen-label="Lab IX Catalogue">
                <div style="min-height:52vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
                    <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                    <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98" data-i18n="lab.title">Laboratoire</h1>
                    <p data-reveal="up" style="margin:0;max-width:56ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="lab.subtitle">Six mécaniques.</p>
                </div>

                <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));padding:0 clamp(56px,9vw,180px) clamp(60px,8vw,120px)">
                    @foreach ($sections as $c)
                        <article style="display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55)">
                            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                                <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ $c['tag'] }}</span>
                                <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)" data-i18n="lab.status">{{ $c['status'] }}</span>
                            </div>
                            <h3 style="margin:0;font-size:clamp(20px,2.2vw,28px);font-weight:200;letter-spacing:-.02em;line-height:1.1">{{ $c['name'] }}</h3>
                            <p style="margin:0;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="{{ $c['i18n'] }}">{{ $c['desc'] }}</p>
                            @if ($c['url'] !== null)
                                {{-- data-barba-prevent : ces pages (barba-lab, scroll-lab, three-lab,
                                     animation-lab) logent leur @vite dans @section('content') — jamais
                                     exécuté si Barba se contente d'injecter le HTML fetché en SPA. --}}
                                <a href="{{ $c['url'] }}" class="lab-cta" data-barba-prevent style="appearance:none;margin-top:6px;align-self:flex-start;background:none;border:1px solid rgba(145,132,217,.5);color:#b3a9e6;cursor:pointer;font-family:inherit;font-size:10px;letter-spacing:.22em;text-transform:uppercase;padding:9px 16px;border-radius:8px;transition:background .3s,color .3s"><span data-i18n="lab.open">Ouvrir</span> {{ str_pad($c['built'], STR_PAD_LEFT) }}</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
@endsection
