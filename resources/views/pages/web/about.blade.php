@extends('pages.web.subsite')
@section('title', 'Lakeust Web — Sites, WebGL et animation')
@section('description', "Lakeust Web : sites et interfaces sur mesure — intégration CSS, JavaScript, animation GSAP, transitions Barba et expériences temps réel WebGL / Three.js.")
@section('sub-content')
    {{-- nav, CSS du pôle Web et enveloppe .theme-web : fournis par pages/web/subsite.blade.php. Ici seulement les scripts propres à cette page. --}}
    @vite(['resources/js/three/blackhole.js', 'resources/js/ui/orbital-preview.js'])

   {{-- padding-top en max(19vh, 100px) : la nav (.nav, nav.css) est fixed
        avec un fond opaque (~73px de haut) — en dessous de ~526px de haut
        de viewport, 19vh seul (ex: 71px à 375x375) passe sous cette hauteur
        et le titre se retrouve caché derrière la barre. 100px garde toujours
        une marge, sans changer le rendu desktop où 19vh domine déjà. --}}
   {{-- Identité Web (css/pages/web-theme.css) : couleur, formes et mouvement,
        à l'opposé du hero Studio (titre métal doré sur fond noir). Tout le
        décor est en CSS pur — aucun WebGL ici, donc rien de plus à charger
        sur mobile. .theme-web (jetons de couleur) enveloppe la page via
        pages/web/subsite.blade.php. --}}
   <section id="top" class="web-hero">
        <div class="web-hero-bg" aria-hidden="true">
            <span class="web-blob b1"></span>
            <span class="web-blob b2"></span>
            <span class="web-blob b3"></span>
            <!-- <div class="web-floor"></div> -->

            <div class="web-shape sh-ring">
                <svg viewBox="0 0 200 200" fill="none">
                    <defs>
                        <linearGradient id="web-ring-g" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#8b7bff"/><stop offset="1" stop-color="#4ee0ff"/>
                        </linearGradient>
                    </defs>
                    <circle class="r1" cx="100" cy="100" r="92" stroke="url(#web-ring-g)" stroke-width="1.5" stroke-dasharray="6 10"/>
                    <circle class="r2" cx="100" cy="100" r="64" stroke="#ff5fa2" stroke-width="1.5" stroke-opacity=".7"/>
                    <circle cx="100" cy="8" r="5" fill="#4ee0ff"/>
                </svg>
            </div>
            <div class="web-shape tri">
                <svg viewBox="0 0 100 100" fill="none"><path d="M50 8 94 88H6Z" stroke="#ff5fa2" stroke-width="2" stroke-linejoin="round"/></svg>
            </div>
            <div class="web-shape dots">
                <svg viewBox="0 0 100 100" fill="#8b7bff">
                    @for ($y = 0; $y < 5; $y++) @for ($x = 0; $x < 5; $x++)
                        <circle cx="{{ 10 + $x * 20 }}" cy="{{ 10 + $y * 20 }}" r="2.4" opacity="{{ .25 + (($x + $y) % 3) * .3 }}"/>
                    @endfor @endfor
                </svg>
            </div>
            <div class="web-shape plus">
                <svg viewBox="0 0 40 40" fill="none"><path d="M20 4v32M4 20h32" stroke="#ffb86b" stroke-width="2.5" stroke-linecap="round"/></svg>
            </div>
            <div class="web-shape cube"><div class="web-cube"><i></i><i></i><i></i><i></i><i></i><i></i></div></div>

            <span class="web-chip c1">WebGL</span>
            <span class="web-chip c2">GSAP</span>
            <span class="web-chip c3">GLSL</span>
            <span class="web-chip c4">Three.js</span>
            <span class="web-chip c5">Barba</span>
            <span class="web-chip c6">CSS</span>
        </div>

        <div class="web-eyebrow" data-hero="1">
            <span class="i18n-fr">Interfaces · Animation · Temps réel</span><span class="i18n-en">Interfaces · Animation · Real-time</span>
        </div>
        <h1 class="web-title" data-hero="1">LAKEUST WEB</h1>
        <p class="web-lead" data-hero="1">
            <span class="i18n-fr">Des sites qui bougent et qui répondent : intégration précise, animation GSAP, shaders WebGL.</span>
            <span class="i18n-en">Sites that move and respond: precise integration, GSAP animation, WebGL shaders.</span>
        </p>
        <div class="web-cta" data-hero="1">
            <a class="web-btn" href="#demos"><span class="i18n-fr">Voir les démos</span><span class="i18n-en">See the demos</span></a>
            <a class="web-btn ghost" href="#travaux"><span class="i18n-fr">Réalisations</span><span class="i18n-en">Selected work</span></a>
        </div>
        <span class="web-cue" aria-hidden="true"></span>
    </section>

    <div class="web-band" style="overflow:hidden;padding:var(--space-4) 0">
        <div style="display:flex;width:max-content;animation:lw-marquee 34s linear infinite;font-family:var(--font-heading);font-size:12px;letter-spacing:.28em;text-transform:uppercase;color:var(--text-2)">
            @for ($r = 0; $r < 2; $r++)
                <div style="display:flex;gap:var(--space-8);padding-right:var(--space-8)" @if($r === 1) aria-hidden="true" @endif>
                    @foreach (['GLSL','Three.js','GSAP','JavaScript','CSS','Laravel','Vite','Barba', 'Theatre', 'WebGL'] as $t)
                        <span>{{ $t }}</span><span class="web-dot">·</span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    <!-- ==================================================================
                                02 - STUDIO
    =================================================================== -->
    <section id="studio" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8) calc(var(--space-8)*2)">
        <div style="margin-top:calc(var(--space-8)*3);display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1px;background:var(--divider);border-top:1px solid var(--divider);border-bottom:1px solid var(--divider)">
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div class="web-stat" data-count="2022" data-plain="1" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em">2022</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Depuis</span><span class="i18n-en">Since</span></div>
            </div>
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div class="web-stat" data-count="4" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em">4 </div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Ans de pratique</span><span class="i18n-en">Years of practice</span></div>
            </div>
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div class="web-stat" data-count="2" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em">2</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Site Web créés</span><span class="i18n-en">Web site created</span></div>
            </div>
        </div>
    </section>
    
    <!-- ==================================================================
                                03 - SERVICES
    =================================================================== -->
    <section id="services" style="border-top:1px solid var(--divider);background:linear-gradient(180deg,rgba(35,37,50,.34),transparent 60%)">
        <div style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
                03 — <span class="i18n-fr">Nos services</span><span class="i18n-en">What we do</span>
            </div>
            <h2 data-reveal="rise" style="margin:0 0 calc(var(--space-8)*2.5);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:22ch">
                <span class="i18n-fr">La même exigence.</span><span class="i18n-en">One standard.</span>
            </h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:var(--space-8)">
                <div class="web-card" data-reveal="rise" data-magnet="1" style="padding:calc(var(--space-8)*1.6)">
                    <div style="display:flex;align-items:baseline;gap:var(--space-4);margin-bottom:var(--space-8)">
                        <span style="font-family:var(--font-heading);font-size:clamp(26px,3vw,38px);letter-spacing:-.02em;color:var(--text-hi)">Web</span>
                        <span style="width:28px;height:1px;background:var(--accent);display:inline-block"></span>
                        <span style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)">CSS · JS</span>
                    </div>
                    <p style="margin:0 0 calc(var(--space-8)*1.5);font-size:16px;line-height:1.65;color:var(--text-2);text-wrap:pretty">
                        <span class="i18n-fr">Sites et interfaces sur mesure : intégration précise, animation, interactions qui répondent.</span>
                        <span class="i18n-en">Custom sites and interfaces: precise integration, animation, interactions that respond.</span>
                    </p>
                    <ul class="grid sm:grid-cols-2 md:grid-cols-3">
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Intégration CSS</span><span class="i18n-en">CSS integration</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">JavaScript</span><span class="i18n-en">JavaScript</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Animation et transitions</span><span class="i18n-en">Animation and transitions</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Temps réel WebGL</span><span class="i18n-en">Real-time WebGL</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Performence</span><span class="i18n-en">Performence</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Multi-Platforme</span><span class="i18n-en">Multi-Platforme</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================================================================
                                04 - DEMOS
    =================================================================== -->
    <section id="demos" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
        <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-6)">
            04 — <span class="i18n-fr">Démonstrations</span><span class="i18n-en">Demos</span>
        </div>
        <h2 data-reveal="rise" style="margin:0 0 calc(var(--space-8)*2.5);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:26ch">
            <span class="i18n-fr">Le laboratoire, pas une capture d'écran.</span><span class="i18n-en">The lab, not a screenshot.</span>
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:calc(var(--space-8)*1.2)">
            @foreach ([
                ['scroll.lab', 'Scroll Lab', 'Débutant', 'Beginner',
                    'IntersectionObserver · Scroll-driven',
                    "Quinze mécaniques de scroll indépendantes — panneaux, parallaxe, progression, révélations, corridor — chacune isolée pour être comparée aux autres.",
                    "Fifteen independent scroll mechanics — panels, parallax, progress, reveals, corridor — each isolated so they can be compared side by side."],
                ['animation-lab', 'Animation Lab', 'Intermédiaire', 'Intermediate',
                    'GSAP · Catalogue de déclenchement',
                    "Neuf concepts d'entrée à l'écran rejoués au passage dans le viewport plutôt qu'au scroll continu — le même gabarit de fiche que les autres labs.",
                    "Nine screen-entry concepts replayed as they enter the viewport rather than on continuous scroll — same card template as the other labs."],
                ['barba.lab', 'Barba Lab', 'Avancé', 'Advanced',
                    'Barba.js · Transitions de page',
                    "Le système de transitions réellement branché sur le site — corridor, rideau, épingle — documenté ici en dix scènes autonomes.",
                    "The transition system actually wired into the site — corridor, curtain, pin — documented here across ten standalone scenes."],
                ['three.lab', 'Three Lab', 'Expert', 'Expert',
                    'Three.js · Scènes WebGL au survol',
                    "Les scènes WebGL du site — trou noir, forêt, orbite — montées à la demande, comparées à leur propre pilotage interne ou externe.",
                    "The site's WebGL scenes — black hole, forest, orbit — mounted on demand, compared in their own internal versus externally driven modes."],
            ] as $i => $v)
                @php
                    /* une forme + une couleur par lab (web-theme.css) */
                    [$gKind, $gColor] = [
                        'scroll.lab'    => ['scroll',    'var(--web-1)'],
                        'animation-lab' => ['animation', 'var(--web-3)'],
                        'barba.lab'     => ['barba',     'var(--web-2)'],
                        'three.lab'     => ['three',     'var(--web-4)'],
                    ][$v[0]];
                @endphp
                <a class="web-card" data-reveal="rise" data-barba-prevent href="{{ route($v[0]) }}" style="--c:{{ $gColor }};display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;color:inherit;text-decoration:none">
                    @include('partials.web-glyph', ['kind' => $gKind, 'color' => $gColor])
                    <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                        <span style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300)">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="tag tag-accent"><span class="i18n-fr">{{ $v[2] }}</span><span class="i18n-en">{{ $v[3] }}</span></span>
                    </div>
                    <div style="font-family:var(--font-heading);font-size:20px;letter-spacing:-.01em">{{ $v[1] }}</div>
                    <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-4)">{{ $v[4] }}</div>
                    <p style="margin:0;font-size:14px;line-height:1.65;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $v[5] }}</span><span class="i18n-en">{{ $v[6] }}</span></p>
                </a>
            @endforeach
        </div>
    </section>

    <!-- ==================================================================
                                05 -TRAVAUX
    =================================================================== -->
    <section id="travaux" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
        <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
            05 — <span class="i18n-fr">Réalisations</span><span class="i18n-en">Selected work</span>
        </div>
        <h2 data-reveal="rise" style="margin:0 0 calc(var(--space-8)*2.5);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:24ch">
            <span class="i18n-fr">Ce qui tourne, pas ce qui est promis.</span><span class="i18n-en">What runs, not what is promised.</span>
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:var(--space-8)">
            {{-- Portail orbital : le vrai hero de l'accueil, rejoué ici en direct
                 (drive="external", voir resources/js/ui/orbital-preview.js) plutôt
                 qu'une capture figée — la carte pointe vers la page qu'elle montre. --}}
            <a class="web-card" data-reveal="rise" data-magnet="1" data-barba-prevent href="{{ route('three.lab') }}" style="--c:var(--web-1);display:block;color:inherit;overflow:hidden">
                <div data-orbital-preview style="position:relative;aspect-ratio:4/3;background:#05050a;overflow:hidden">
                    <black-hole-stage drive="external" quality="low" disk-palette="violet" style="position:absolute;inset:0;width:100%;height:100%"></black-hole-stage>
                </div>
                <div style="padding:var(--space-8)">
                    <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-3)">Web · WebGL</div>
                    <div style="font-family:var(--font-heading);font-size:20px;letter-spacing:-.01em;margin-bottom:var(--space-3)"><span class="i18n-fr">Portail orbital</span><span class="i18n-en">Orbital portal</span></div>
                    <p style="margin:0;font-size:14px;line-height:1.6;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">Navigation en temps réel : Three.js, shaders de lentille gravitationnelle, GSAP.</span><span class="i18n-en">Real-time navigation: Three.js, gravitational-lensing shaders, GSAP.</span></p>
                </div>
            </a>

            {{-- Transitions de page : pas de capture figée possible pour une
                 mécanique de navigation — la carte pointe vers Barba Lab, où les dix
                 scènes se rejouent réellement plutôt que d'être décrites. --}}
            <a class="web-card" data-reveal="rise" data-magnet="1" data-barba-prevent href="{{ route('barba.lab') }}" style="--c:var(--web-2);display:block;color:inherit;overflow:hidden">
                <div style="position:relative;aspect-ratio:4/3">
                    @include('partials.web-glyph', ['kind' => 'barba', 'color' => 'var(--web-2)', 'fill' => true])
                </div>
                <div style="padding:var(--space-8)">
                    <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-3)">Web · Laravel</div>
                    <div style="font-family:var(--font-heading);font-size:20px;letter-spacing:-.01em;margin-bottom:var(--space-3)"><span class="i18n-fr">Transitions de page</span><span class="i18n-en">Page transitions</span></div>
                    <p style="margin:0;font-size:14px;line-height:1.6;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">Navigation sans rechargement sur une base Laravel et Vite, transitions Barba.</span><span class="i18n-en">No-reload navigation on a Laravel and Vite base, Barba transitions.</span></p>
                </div>
            </a>

            <a class="web-card" data-reveal="rise" data-magnet="1" href="#contact" data-jump="contact" style="--c:var(--web-3);display:block;color:inherit;overflow:hidden">
                <div style="position:relative;aspect-ratio:4/3">
                    @include('partials.web-glyph', ['kind' => 'animation', 'color' => 'var(--web-3)', 'fill' => true])
                </div>
                <div style="padding:var(--space-8)">
                    <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-3)">Web · Sample</div>
                    <div style="font-family:var(--font-heading);font-size:20px;letter-spacing:-.01em;margin-bottom:var(--space-3)"><span class="i18n-fr">Animation Custom</span><span class="i18n-en">Animation Custom</span></div>
                    <p style="margin:0;font-size:14px;line-height:1.6;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">Animation Js, CSS et autre — titre et détails à documenter.</span><span class="i18n-en">Animation Js, CSS and other — title and details to be documented.</span></p>
                </div>
            </a>
        </div>
    </section>

    <!-- ==================================================================
                                06 - METHODE
    =================================================================== -->
    {{-- dérive horizontale : le scroll vertical translate la piste (setupDrift, core/page-systems.js) --}}
    <section id="methode" class="drift" data-drift>
        <div class="drift-sticky" style="min-height:600px;background:var(--bg);border-top:1px solid var(--divider)">
            <div class="drift-head" style="max-width:1240px;margin:0 auto;width:100%;padding:0 var(--space-8) calc(var(--space-8)*2)">
                <div style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300)">
                    06 — <span class="i18n-fr">Notre méthode</span><span class="i18n-en">Our process</span>
                </div>
            </div>
            <div class="drift-track" style="padding:0 var(--space-8)">
                @foreach ([
                    ['01', 'Cadrage', 'Scoping', "On définit le besoin, les contraintes techniques et ce qui reste hors périmètre. Écrit, chiffré.", 'We set the need, the technical constraints and what stays out of scope. Written down, priced.'],
                    ['02', 'Prototype', 'Prototype', "Une version jouable ou cliquable avant la production. On corrige la direction tant que c'est peu coûteux.", 'A playable or clickable version before production. Direction gets corrected while it is still cheap.'],
                    ['03', 'Production', 'Build', 'Le code est écrit ici, versionné, relu. Points d\'avancement réguliers, rien de caché.', 'The code is written here, versioned, reviewed. Regular check-ins, nothing hidden.'],
                    ['04', 'Livraison', 'Delivery', 'Mise en ligne ou build final, documentation, et le suivi qui va avec.', 'Deployment or final build, documentation, and the follow-up that goes with it.'],
                ] as $step)
                    <div class="drift-item" style="width:min(78vw,520px);padding:calc(var(--space-8)*1.6);border-radius:var(--radius-lg);background:rgba(35,37,50,.62);box-shadow:var(--shadow-sm);display:flex;flex-direction:column;justify-content:space-between;min-height:330px">
                        <div style="font-family:var(--font-heading);font-size:56px;line-height:1;color:var(--accent-700)">{{ $step[0] }}</div>
                        <div>
                            <h3 style="margin:0 0 var(--space-6);font-size:26px;letter-spacing:-.02em"><span class="i18n-fr">{{ $step[1] }}</span><span class="i18n-en">{{ $step[2] }}</span></h3>
                            <p style="margin:0;font-size:15px;line-height:1.7;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $step[3] }}</span><span class="i18n-en">{{ $step[4] }}</span></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ==================================================================
                                07 - STACK
    =================================================================== -->
    <section id="stack" style="border-top:1px solid var(--divider);background:linear-gradient(180deg,rgba(35,37,50,.3),transparent 70%)">
        <div style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:calc(var(--space-8)*2)">
                07 — <span class="i18n-fr">Technologies</span><span class="i18n-en">Technologies</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:calc(var(--space-8)*3)">
                <div>
                    <div data-reveal="rise" style="font-family:var(--font-heading);font-size:15px;letter-spacing:.16em;text-transform:uppercase;color:var(--text-2);padding-bottom:var(--space-6);margin-bottom:var(--space-8);border-bottom:1px solid var(--divider)">Web</div>
                    <div data-reveal="rise" style="display:flex;flex-wrap:wrap;gap:var(--space-3)">
                        @foreach (['JavaScript','CSS','Three.js','GLSL','GSAP','Barba','Laravel','Blade','Vite','Theatre','Anim'] as $t)
                            <span class="tag tag-outline">{{ $t }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================================================================
                                08 - CONTACT
    =================================================================== -->
    <section id="contact" style="border-top:1px solid var(--divider)">
        <div style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
                08 — <span class="i18n-fr">Contact</span><span class="i18n-en">Contact</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:calc(var(--space-8)*3);align-items:start">
                <div>
                    <h2 data-reveal="rise" style="margin:0 0 var(--space-8);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:18ch">
                        <span class="i18n-fr">Parlez-nous du projet.</span><span class="i18n-en">Tell us about the project.</span>
                    </h2>
                    <p data-reveal="rise" style="margin:0 0 calc(var(--space-8)*1.5);font-size:16px;line-height:1.65;color:var(--text-3);max-width:44ch;text-wrap:pretty">
                        <span class="i18n-fr">Jeu, site, effet précis à produire : décrivez le besoin, on répond avec un périmètre et un prix.</span>
                        <span class="i18n-en">A game, a site, one specific effect: describe the need and we answer with a scope and a price.</span>
                    </p>
                    <div data-reveal="rise" style="font-size:20px;font-family:var(--font-heading);color:var(--accent-300)">lakeustworks@gmail.com</div>
                    <!--<div style="margin-top:var(--space-3);font-size:12px;color:var(--text-4)"><span class="i18n-fr">Adresse à confirmer</span><span class="i18n-en">Address to confirm</span></div>-->
                </div>
                <!--<form data-reveal="rise" data-lw-form style="display:flex;flex-direction:column;gap:var(--space-6);padding:calc(var(--space-8)*1.5);border-radius:var(--radius-lg);background:rgba(35,37,50,.6);box-shadow:var(--shadow-sm)">
                    <div class="field">
                        <label for="lw-name"><span class="i18n-fr">Nom</span><span class="i18n-en">Name</span></label>
                        <input class="input" id="lw-name" type="text" required>
                    </div>
                    <div class="field">
                        <label for="lw-mail">Email</label>
                        <input class="input" id="lw-mail" type="email" required>
                    </div>
                    <div class="field">
                        <label for="lw-msg"><span class="i18n-fr">Le projet</span><span class="i18n-en">The project</span></label>
                        <textarea class="input" id="lw-msg" rows="5" style="resize:vertical"></textarea>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit" data-magnet="1"><span class="i18n-fr">Envoyer</span><span class="i18n-en">Send</span></button>
                    <div data-lw-form-note style="font-size:12px;color:var(--text-4);text-align:center">Démonstration — le formulaire n'envoie rien pour l'instant.</div>
                </form>-->
            </div>
        </div>
    </section>
@endsection
