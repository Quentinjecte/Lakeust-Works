{{-- Lakeust Arts · À propos — page d'accueil de la branche, avec la même ossature
     que les À propos du Studio et du Web (hero, bandeau, chiffres, services,
     démonstrations, méthode, technologies, contact), dans le thème Arts :
     bronze/or/cuivre, turntable, projecteur, annotations de viewport, et surtout
     de la vraie 3D — le hero présente UN seul modèle (ModelCatalog::HERO, le boss
     squelette de cristal), en fond derrière le titre et non interactif ; il se « construit » à
     l'arrivée (Maillage -> plein).

     Styles : css/pages/arts-theme.css. Apparition du hero :
     js/pages/arts-models.js (data-ar-build). Les chiffres et les cartes de
     modèles viennent de App\Support\ModelCatalog : remplacer les fiches
     d'exemple par les vrais modèles met cette page à jour toute seule. --}}
@extends('pages.arts.subsite')
@section('title', 'Lakeust Arts — Modèles 3D Blender & Blockbench')
@section('description', "Lakeust Arts : modèles 3D (props, personnages, décors) créés avec Blender et Blockbench, prêts pour Unity et le web. L'atelier, ses outils, sa méthode — et des modèles à faire tourner en direct.")

@section('sub-content')
    @use('App\Support\ModelCatalog')
    @php
        $models   = ModelCatalog::all();
        $tools    = collect($models)->pluck('tools')->flatten()->unique()->values();
        $hero     = ModelCatalog::hero();   // null : le modèle du hero a quitté le catalogue -> repli décoratif
        $featured = ModelCatalog::featured(3);
        $steps    = ModelCatalog::steps();

        $words = ['Blender', 'Blockbench', 'glTF', 'Low poly', 'Voxel', 'PBR', 'UV', 'Rig', 'Texturing', 'Optimisation'];

        /* les quatre terrains de la section « Ce que l'on crée » : une forme animée
           (partials/arts-glyph) et une couleur du thème par discipline */
        $discs = [
            ['kind' => 'facet',   'color' => 'var(--ar-gold)',   'tools' => ['blender'],
                'title' => ['fr' => 'Props & hard surface',     'en' => 'Props & hard surface'],
                'text'  => ['fr' => "Objets nets et lisibles : facettes propres, budget de triangles tenu, matériaux prêts pour le moteur.",
                            'en' => "Clean, readable objects: crisp facets, a triangle budget kept, materials ready for the engine."]],
            ['kind' => 'voxel',   'color' => 'var(--ar-copper)', 'tools' => ['blockbench'],
                'title' => ['fr' => 'Personnages & voxel',      'en' => 'Characters & voxel'],
                'text'  => ['fr' => "Personnages en cubes ou en low poly, texturés en pixel art, riggés et animés dans Blockbench.",
                            'en' => "Cube-built or low-poly characters, pixel-art textured, rigged and animated in Blockbench."]],
            ['kind' => 'terrain', 'color' => 'var(--ar-teal)',   'tools' => ['blender', 'blockbench'],
                'title' => ['fr' => 'Décors & environnements',  'en' => 'Environments'],
                'text'  => ['fr' => "Éléments de décor et scènes entières, pensés pour être instanciés en nombre sans coût perceptible.",
                            'en' => "Environment pieces and whole scenes, designed to be instanced by the hundreds at no perceptible cost."]],
            ['kind' => 'rig',     'color' => 'var(--ar-coral)',  'tools' => ['blender', 'blockbench'],
                'title' => ['fr' => 'Rig & animation',          'en' => 'Rig & animation'],
                'text'  => ['fr' => "Squelettes simples et animations courtes, exportés avec le modèle dans un seul fichier glTF.",
                            'en' => "Simple skeletons and short animations, exported with the model in a single glTF file."]],
        ];
    @endphp

    {{-- ===================================================================== hero --}}
    <section id="top" class="ar-about-hero">
        <div class="ar-about-bg" aria-hidden="true">
            <span class="ar-glow g1"></span>
            <span class="ar-glow g2"></span>
            <span class="ar-glow g3"></span>
            <span class="ar-spot"></span>
            <!--@for ($i = 0; $i < 14; $i++)
                @php
                    $mx  = ($i * 37 + 11) % 100;                    // position horizontale (%)
                    $msz = 2 + ($i % 3);                            // taille (px)
                    $mt  = 11 + ($i * 7) % 9;                       // durée de la montée (s)
                    $md  = -(($i * 13) % 17);                       // décalage négatif : déjà en vol au chargement
                    $mdx = ($i % 2 ? 1 : -1) * (14 + ($i * 5) % 30); // dérive latérale (px)
                @endphp
                <span class="ar-mote" style="--x:{{ $mx }}%;--s:{{ $msz }}px;--t:{{ $mt }}s;--d:{{ $md }}s;--dx:{{ $mdx }}px"></span>
            @endfor-->
        </div>

        @if ($hero)
        {{-- le modèle du hero, DERRIÈRE le titre et purement décoratif : pointer-events: none (arts-theme.css)
             -> ni glisser, ni molette, ni toucher ; il tourne seul. Il arrive en Maillage puis se « construit »
             en plein, une fois (data-ar-build, arts-models.js). --}}
        <div class="ar-showcase" aria-hidden="true">
            <lw-model-viewer autorotate wireframe no-zoom data-ar-build
                             demo="{{ $hero['demo'] }}" accent="{{ $hero['accent'] }}" load="{{ $hero['load'] }}"
                             @if ($hero['src']) src="{{ asset($hero['src']) }}" @endif></lw-model-viewer>
        </div>
        @endif

        <div class="ar-about-inner">
            <div class="ar-about-head">
                <div class="ar-eyebrow"><span class="i18n-fr">Lakeust Arts · Atelier 3D</span><span class="i18n-en">Lakeust Arts · 3D workshop</span></div>
                <h1 class="ar-about-title">Lakeust Arts</h1>
            </div>
            <div class="ar-about-foot">
                <p class="ar-lead">
                    <span class="i18n-fr">Modèles 3D créés avec Blender et Blockbench : props, personnages et décors, prêts pour Unity et le web.</span>
                    <span class="i18n-en">3D models built with Blender and Blockbench: props, characters and environments, ready for Unity and the web.</span>
                </p>
                <div class="ar-cta">
                    <a class="ar-btn ar-btn--solid" href="{{ route('arts.models') }}" data-barba-prevent><span class="i18n-fr">Voir les modèles</span><span class="i18n-en">See the models</span></a>
                    <a class="ar-btn" href="#contact"><span class="i18n-fr">Parler d'un projet</span><span class="i18n-en">Talk about a project</span></a>
                </div>
            </div>
        </div>
        <span class="ar-cue" aria-hidden="true"></span>
    </section>

    {{-- =================================================================== bandeau --}}
    <div class="ar-band">
        <div class="row">
            @for ($r = 0; $r < 2; $r++)
                <div class="set" @if ($r === 1) aria-hidden="true" @endif>
                    @foreach ($words as $w)
                        <span>{{ $w }}</span><span class="dot">·</span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    {{-- ==================================================================== chiffres --}}
    <section id="atelier" class="ar-sec" style="padding-bottom:calc(var(--space-8)*2)">
        <div class="ar-stats" data-reveal="rise">
            <div>
                <div class="ar-stat" data-count="2024" data-plain="1" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em">2024</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Depuis</span><span class="i18n-en">Since</span></div>
            </div>
            <div>
                <div class="ar-stat" data-count="{{ count($models) }}">{{ count($models) }}</div>
                <div class="ar-stat-l"><span class="i18n-fr">Modèles à l'atelier</span><span class="i18n-en">Models in the workshop</span></div>
            </div>
            <div>
                <div class="ar-stat" data-count="{{ count($tools) }}">{{ count($tools) }}</div>
                <div class="ar-stat-l"><span class="i18n-fr">Outils de création</span><span class="i18n-en">Creation tools</span></div>
            </div>
        </div>
    </section>

    {{-- ================================================================= 03 · créer --}}
    <section id="services" class="ar-services">
        <div class="ar-sec">
            <div class="ar-kicker" data-reveal="rise">03 — <span class="i18n-fr">Ce que l'on crée</span><span class="i18n-en">What we make</span></div>
            <h2 class="ar-h2" data-reveal="rise"><span class="i18n-fr">Du prop au décor, la même exigence.</span><span class="i18n-en">From prop to environment, one standard.</span></h2>
            <div class="ar-discs">
                @foreach ($discs as $d)
                    <article class="ar-disc" data-reveal="rise" style="--c:{{ $d['color'] }}">
                        @include('partials.arts-glyph', ['kind' => $d['kind'], 'color' => $d['color']])
                        <h3><span class="i18n-fr">{{ $d['title']['fr'] }}</span><span class="i18n-en">{{ $d['title']['en'] }}</span></h3>
                        <p><span class="i18n-fr">{{ $d['text']['fr'] }}</span><span class="i18n-en">{{ $d['text']['en'] }}</span></p>
                        <span class="ar-badges">{!! ModelCatalog::badges($d['tools']) !!}</span>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================================ 04 · modèles --}}
    <section id="demos" class="ar-sec">
        <div class="ar-kicker" data-reveal="rise">04 — <span class="i18n-fr">Modèles</span><span class="i18n-en">Models</span></div>
        <h2 class="ar-h2" data-reveal="rise"><span class="i18n-fr">Des modèles qui tournent, pas des captures.</span><span class="i18n-en">Models that spin, not screenshots.</span></h2>
        <div class="ar-feat">
            @foreach ($featured as $m)
                <a class="ar-card" data-reveal="rise" data-barba-prevent href="{{ $m['url'] }}" style="--c:{{ $m['accent'] }}">
                    <lw-model-viewer demo="{{ $m['demo'] }}" accent="{{ $m['accent'] }}" load="{{ $m['load'] }}" hover-rotate
                                     @if ($m['src']) src="{{ asset($m['src']) }}" @endif></lw-model-viewer>
                    <div class="ar-card-top">
                        <span class="ar-badges">{!! ModelCatalog::badges($m['tools']) !!}</span>
                        <span class="ar-pill">{{ $m['tris'] }} tris</span>
                    </div>
                    <div class="ar-card-body">
                        <h3 class="ar-card-name">{{ $m['name'] }}</h3>
                        <div class="ar-card-meta"><span class="i18n-fr">{{ $m['category']['fr'] }}</span><span class="i18n-en">{{ $m['category']['en'] }}</span> · {{ $m['year'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="ar-more" data-reveal="rise">
            <a class="ar-btn" href="{{ route('arts.models') }}" data-barba-prevent>
                <span class="i18n-fr">Tous les modèles</span><span class="i18n-en">All the models</span> · {{ str_pad(count($models), 2, '0', STR_PAD_LEFT) }} →
            </a>
        </div>
    </section>

    {{-- ============================================================== 05 · méthode --}}
    {{-- dérive horizontale : le scroll vertical translate la piste (setupDrift, core/page-systems.js) ;
         sous 900px elle devient une piste défilante à accroche (core/responsive.css) --}}
    <section id="methode" class="drift ar-drift" data-drift>
        <div class="drift-sticky">
            <div class="drift-head">
                <div class="ar-kicker">05 — <span class="i18n-fr">Du blockout à l'export</span><span class="i18n-en">From blockout to export</span></div>
            </div>
            <div class="drift-track">
                @foreach ($steps as $i => $s)
                    <div class="drift-item">
                        <div class="ar-pstep">
                            <div class="n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <div>
                                <h3><span class="i18n-fr">{{ $s['title']['fr'] }}</span><span class="i18n-en">{{ $s['title']['en'] }}</span></h3>
                                <p><span class="i18n-fr">{{ $s['text']['fr'] }}</span><span class="i18n-en">{{ $s['text']['en'] }}</span></p>
                                <div class="ar-pips" aria-hidden="true">
                                    @for ($k = 0; $k < count($steps); $k++)<i @class(['on' => $k <= $i])></i>@endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================================== 06 · technologies --}}
    <section id="stack" class="ar-services">
        <div class="ar-sec">
            <div class="ar-kicker" data-reveal="rise" style="margin-bottom:calc(var(--space-8)*2)">06 — <span class="i18n-fr">Technologies</span><span class="i18n-en">Technologies</span></div>
            <div class="ar-tech">
                <div>
                    <h3 data-reveal="rise"><span class="i18n-fr">Création</span><span class="i18n-en">Creation</span></h3>
                    <div class="ar-techtags" data-reveal="rise">
                        @foreach ($tools as $t)
                            <span class="ar-techtag" style="--t:{{ ModelCatalog::TOOLS[$t]['color'] ?? '#e8c26a' }}">{{ ModelCatalog::TOOLS[$t]['label'] ?? $t }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3 data-reveal="rise"><span class="i18n-fr">Formats & intégration</span><span class="i18n-en">Formats & integration</span></h3>
                    <div class="ar-techtags" data-reveal="rise">
                        @foreach (['glTF 2.0', '.glb', 'Unity', 'Three.js'] as $t)
                            <span class="ar-techtag">{{ $t }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================================= 07 · contact --}}
    <section id="contact" class="ar-contact-sec">
        <div class="ar-sec">
            <div class="ar-kicker" data-reveal="rise">07 — Contact</div>
            <div class="ar-contact">
                <div>
                    <h2 class="ar-h2" data-reveal="rise" style="margin-bottom:var(--space-8);max-width:16ch"><span class="i18n-fr">Un modèle à créer ?</span><span class="i18n-en">A model to create?</span></h2>
                    <p class="ar-prose" data-reveal="rise" style="margin-bottom:calc(var(--space-8)*1.5)">
                        <span class="i18n-fr">Prop, personnage ou décor à produire : décrivez le besoin, on répond avec un périmètre et un prix.</span>
                        <span class="i18n-en">A prop, a character or an environment to build: describe the need and we answer with a scope and a price.</span>
                    </p>
                    <a class="ar-mail" data-reveal="rise" href="mailto:lakeustworks@gmail.com">lakeustworks@gmail.com</a>
                </div>
                <div data-reveal="rise" class="ar-contact-cta">
                    <a class="ar-btn ar-btn--solid" href="mailto:lakeustworks@gmail.com"><span class="i18n-fr">Écrire</span><span class="i18n-en">Write</span></a>
                    <a class="ar-btn" href="{{ route('arts.models') }}" data-barba-prevent><span class="i18n-fr">Voir les modèles</span><span class="i18n-en">See the models</span></a>
                </div>
            </div>
        </div>
    </section>
@endsection
