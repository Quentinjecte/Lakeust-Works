@extends('pages.web.subsite')
@section('title', 'Laboratoire — Lakeust Web')
@section('description', "Laboratoire Lakeust Web : Barba, scroll, Three.js, animation et carousel — des mécaniques d'interaction isolées et rejouables.")
@section('sub-content')
    @vite(['resources/css/lab.css', 'resources/js/labs/catalogue/lab-catalogue.js'])

    @php
        /* Catalogue des labs Web, du plus simple au plus avancé (même ordre que
           la section Démonstrations de web/about). Une forme animée et une
           couleur par lab : partials/web-glyph + css/pages/web-theme.css.
           Textes FR/EN ici même (spans .i18n-fr/.i18n-en) plutôt que dans
           dictionary.json, dont les entrées lab.cat.* de ces labs étaient des
           copies du même paragraphe. */
        $labs = [
            ['route' => 'scroll.lab',    'kind' => 'scroll',    'color' => 'var(--web-1)', 'name' => 'Scroll Lab',
                'level' => ['Débutant', 'Beginner'],
                'stack' => 'IntersectionObserver · Scroll-driven',
                'fr' => "Quinze mécaniques de scroll indépendantes — panneaux, parallaxe, progression, révélations, corridor — chacune isolée pour être comparée aux autres.",
                'en' => "Fifteen independent scroll mechanics — panels, parallax, progress, reveals, corridor — each isolated so they can be compared side by side."],
            ['route' => 'animation-lab', 'kind' => 'animation', 'color' => 'var(--web-3)', 'name' => 'Animation Lab',
                'level' => ['Intermédiaire', 'Intermediate'],
                'stack' => 'GSAP · Catalogue de déclenchement',
                'fr' => "Neuf concepts d'entrée à l'écran rejoués au passage dans le viewport plutôt qu'au scroll continu.",
                'en' => "Nine screen-entry concepts replayed as they enter the viewport rather than on continuous scroll."],
            ['route' => 'barba.lab',     'kind' => 'barba',     'color' => 'var(--web-2)', 'name' => 'Barba Lab',
                'level' => ['Avancé', 'Advanced'],
                'stack' => 'Barba.js · Transitions de page',
                'fr' => "Le système de transitions réellement branché sur le site — corridor, rideau, épingle — documenté en dix scènes autonomes.",
                'en' => "The transition system actually wired into the site — corridor, curtain, pin — documented across ten standalone scenes."],
            ['route' => 'three.lab',     'kind' => 'three',     'color' => 'var(--web-4)', 'name' => 'Three Lab',
                'level' => ['Expert', 'Expert'],
                'stack' => 'Three.js · Scènes WebGL au survol',
                'fr' => "Les scènes WebGL du site — trou noir, forêt, orbite — montées à la demande, en pilotage interne ou externe.",
                'en' => "The site's WebGL scenes — black hole, forest, orbit — mounted on demand, internally or externally driven."],
            ['route' => 'carousel.lab',  'kind' => 'carousel',  'color' => 'var(--web-5)', 'name' => 'Carousel Lab',
                'level' => ['Expert', 'Expert'],
                'stack' => 'Three.js · Post-traitement',
                'fr' => "Une seule valeur — la position fractionnaire dans la liste — pilote six dispositions spatiales et deux transitions par post-traitement.",
                'en' => "A single value — the fractional position in the list — drives six spatial layouts and two post-processing transitions."],
        ];
    @endphp

    {{-- .lab-sec + data-lab="catalogue" : accroche de lab-catalogue.js, qui pose
         .is-active. data-lab-root est conservé pour le même motif. --}}
    <div data-lab-root>
        <section class="lab-sec web-lab" data-lab="catalogue" data-screen-label="Lab Catalogue">

            <div class="web-hero web-hero--lab">
                @include('partials.web-shapes')

                <div class="web-eyebrow">
                    <span class="i18n-fr">{{ str_pad(count($labs), 2, '0', STR_PAD_LEFT) }} mécaniques · rejouables en direct</span>
                    <span class="i18n-en">{{ str_pad(count($labs), 2, '0', STR_PAD_LEFT) }} mechanics · replayable live</span>
                </div>
                <h1 class="web-title" data-i18n="lab.title">Laboratoire</h1>
                <p class="web-lead">
                    <span class="i18n-fr">Chaque lab isole une technique d'interaction pour la comparer, la rejouer et la lire de près.</span>
                    <span class="i18n-en">Each lab isolates one interaction technique so you can compare it, replay it and read it up close.</span>
                </p>
            </div>

            <div class="web-lab-wrap">
                <div class="web-lab-head" data-reveal="rise">
                    <span class="k"><span class="i18n-fr">Catalogue</span><span class="i18n-en">Catalogue</span></span>
                    <span class="s"><span class="i18n-fr">Du plus simple au plus avancé</span><span class="i18n-en">From simplest to most advanced</span></span>
                </div>

                <div class="web-lab-grid">
                    @foreach ($labs as $i => $l)
                        {{-- data-barba-prevent : ces pages logent leur @vite dans leur contenu
                             — jamais exécuté si Barba se contente d'injecter le HTML fetché. --}}
                        <a class="web-card web-lab-card" data-reveal="rise" data-barba-prevent
                           href="{{ route($l['route']) }}" style="--c:{{ $l['color'] }}">
                            @include('partials.web-glyph', ['kind' => $l['kind'], 'color' => $l['color']])
                            <div class="web-lab-meta">
                                <span class="web-lab-n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="tag"><span class="i18n-fr">{{ $l['level'][0] }}</span><span class="i18n-en">{{ $l['level'][1] }}</span></span>
                            </div>
                            <h3 class="web-lab-title">{{ $l['name'] }}</h3>
                            <div class="web-lab-stack">{{ $l['stack'] }}</div>
                            <p class="web-lab-desc"><span class="i18n-fr">{{ $l['fr'] }}</span><span class="i18n-en">{{ $l['en'] }}</span></p>
                            <span class="web-lab-open"><span data-i18n="lab.open">Ouvrir</span> <i aria-hidden="true">→</i></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
