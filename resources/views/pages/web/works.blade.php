@extends('layouts.site')
@section('title', 'Travaux — Lakeust Works')
@section('content')
<head>

    @vite(['resources/css/app.css', 'resources/css/web.css'])

    @php
        /* Placeholder : à remplacer par une source de données réelle. Le mot
           "Projet"/"archivé" est traduit côté JS (data-i18n) ; le numéro reste
           hors traduction. */
        $selection = [
            ['num' => '01', 'annee' => '2026'],
            ['num' => '02', 'annee' => '2025'],
            ['num' => '03', 'annee' => '2025'],
            ['num' => '04', 'annee' => '2024'],
            ['num' => '05', 'annee' => '2023'],
        ];
        $archive = [
            ['num' => '06', 'annee' => '2022'],
            ['num' => '07', 'annee' => '2022'],
            ['num' => '08', 'annee' => '2021'],
            ['num' => '09', 'annee' => '2020'],
        ];

        $nav = [
            ['route' => 'web.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'web.works',   'i18n' => 'nav.works',  'keep' => true],
            ['route' => 'web.lab',     'i18n' => 'nav.lab', 'keep' => false, 'external' => true],
        ];
    @endphp

</head>

    <section class="section" style="padding:clamp(120px,18vh,200px) 0 var(--s-6);">
        <div class="wrap">
            <div data-reveal="mask" style="w-lg">
                <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-4);" data-i18n="works.index">Index — 2020 / 2026</span></span>
                <span class="mask-line"><h1 class="t-display " data-i18n="works.title">Travaux</h1></span>
            </div>
            <p class="t-lead" data-reveal="blur" data-reveal-delay="200" style="margin-top:var(--s-5);" data-i18n="works.lead">
                Texte de remplacement. La bande ci-dessous se déplace horizontalement au fil du scroll vertical.
            </p>
        </div>
    </section>

    {{-- dérive horizontale : le scroll vertical translate la piste --}}
    <section class="drift" data-drift>
        <div class="drift-sticky">
            <div class="drift-head">
                <span class="label" data-i18n="works.selection">Sélection</span>
                <span class="drift-count label" data-drift-count>01 / {{ str_pad(count($selection), 2, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="drift-track">
                @foreach ($selection as $i => $projet)
                    <a class="drift-item" href="{{ route('web.project') }}">
                        <div class="media media-4-3 media-hover" @if ($i === 0) data-reveal="frame" @endif><div class="media-fill"></div></div>
                        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-top:var(--s-3);">
                            <span class="t-h3"><span data-i18n="works.project">Projet</span> {{ $projet['num'] }}</span><span class="label">{{ $projet['annee'] }}</span>
                        </div>
                        <span class="t-body" style="font-size:13px;" data-i18n="works.placeholder.desc">Placeholder — une ligne de description.</span>
                    </a>
                @endforeach
            </div>

            <div class="drift-bar"><span></span></div>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            <span class="label" data-reveal="rise" data-i18n="works.archive">Archive</span>
            <hr class="rule" data-reveal="line" style="margin:var(--s-3) 0 var(--s-5);">
            <div class="grid-2" data-reveal="converge">
                @foreach ($archive as $projet)
                    <a class="card" href="{{ route('web.project') }}">
                        <span class="card-kicker">{{ $projet['annee'] }}</span>
                        <span class="card-title"><span data-i18n="works.project.archived">Projet archivé</span> {{ $projet['num'] }}</span>
                        <span class="card-body" data-i18n="works.placeholder.card">Placeholder.</span>
                        <span class="card-more"><span data-i18n="works.open">Ouvrir</span> <span aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
