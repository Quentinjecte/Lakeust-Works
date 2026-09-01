@extends('layouts.site')
@section('title', 'Laboratoire — Lakeust Web')
@section('content')
<head>
    @vite(['resources/css/lab.css', 'resources/js/labs/catalogue/lab-catalogue.js'])

    @php
        /* Ordre de navigation (clavier / sommaire) : Catalogue d'abord, puis
            les quinze mécaniques. Doit rester synchronisé avec ORDER dans lab.js. */
        $sections = [
            [
                'url' => '/barba-lab',
                'tag' => "Ω · 01",
                'name' => "Barba js",
                'built' => 1,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.barba',
                'desc' => "Rien ne défile. Le document entier tient dans l'écran et le scroll ne déplace que le point de lecture : la bande visée s'ouvre, les autres se compriment en filets."
            ],
            [
                'url' => '/scroll-lab',
                'tag' => "Ω · 02",
                'name' => "Scroll js",
                'built' => 2,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.scroll',
                'desc' => "Rien ne défile. Le document entier tient dans l'écran et le scroll ne déplace que le point de lecture : la bande visée s'ouvre, les autres se compriment en filets."
            ],
            [
                'url' => '/three-lab',
                'tag' => "Ω · 03",
                'name' => "Three js",
                'built' => 3,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.three',
                'desc' => "Rien ne défile. Le document entier tient dans l'écran et le scroll ne déplace que le point de lecture : la bande visée s'ouvre, les autres se compriment en filets."
            ],
            [
                'url' => '/animation-lab',
                'tag' => "Ω · 04",
                'name' => "Animation JS",
                'built' => 4,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.animation',
                'desc' => "Rien ne défile. Le document entier tient dans l'écran et le scroll ne déplace que le point de lecture : la bande visée s'ouvre, les autres se compriment en filets."
            ],
            [
                'url' => '/carousel-lab',
                'tag' => "Ω · 05",
                'name' => "Carousel Lab",
                'built' => 5,
                'status' => "Prototypé",
                'i18n' => 'lab.cat.carousel',
                'desc' => "Une seule valeur — la position fractionnaire dans la liste — pilote six dispositions spatiales et deux transitions par post-traitement sur les mêmes huit plans."
            ],
        ];


    $nav = [
        ['route' => 'web.about',   'i18n' => 'nav.about', 'keep' => true],
        ['route' => 'web.works',   'i18n' => 'nav.works',  'keep' => true],
        ['route' => 'web.lab',     'i18n' => 'nav.lab', 'keep' => false, 'external' => true],
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
