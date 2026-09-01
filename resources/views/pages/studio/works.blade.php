@extends('layouts.site')

@section('title', 'Nos Jeux — Lakeust Works')
@section('cat', 'Studio')

@section('content')
<head>
    @vite(['resources/css/app.css', 'resources/css/web.css'])

    <!-- Mise en page à deux colonnes (méta collante + média), qui s'aplatit
         en une seule colonne sous 980px — pas de classe partagée pour ce
         motif ailleurs sur le site, donc scopé ici. -->
    <style>
        @media (max-width: 980px) {
            [data-split] { grid-template-columns: 1fr !important; }
            [data-split] > * { grid-column: 1 !important; position: static !important; top: auto !important; }
        }
        .nj-cat-row { transition: background .25s; }
        .nj-cat-row:hover { background: var(--accent-ghost, rgba(145, 132, 217, .07)); }
    </style>

    @php
        /* Catalogue réel du studio (3 titres, pas 5 : pas de jeu inventé pour
           remplir la grille). Un seul featured — celui réellement en
           production. Les champs non confirmés (durée exacte, plateformes,
           date de sortie...) restent volontairement vagues plutôt
           qu'inventés — voir project.blade.php pour Jurassic Containment,
           seul titre avec une vraie fiche détaillée ailleurs sur le site. */
        $games = [
            [
                'route' => 'studio.project01',
                'id' => 'jurassic-containment', 'n' => '01',
                'titre' => 'Jurassic Containment',
                'genre' => ['fr' => 'Coopératif · FPS', 'en' => 'Co-op · FPS'],
                'moteur' => 'Unity 6', 'langage' => 'C#',
                'depuis' => ['fr' => 'Depuis 2026', 'en' => 'Since 2026'],
                'equipe' => ['fr' => '1 développeur', 'en' => '1 developer'],
                'plateformes' => 'PC · Steam',
                'statut' => ['fr' => 'En production', 'en' => 'In production'],
                'sortie' => ['fr' => 'Non annoncée', 'en' => 'Not announced'],
                'lien' => null,
                'img' => asset('images/jurassic-containment/hero-attack.webp'),
                'img2' => asset('images/jurassic-containment/env-day-02.webp'),
                'chapo' => ['fr' => "Lakeust BioGen travaillait sur le clonage de dinosaures — le confinement a échoué. À vous de sécuriser et contenir les spécimens.",
                    'en' => "Lakeust BioGen was working on dinosaur cloning — containment failed. It's on you to secure and contain the specimens."],
                'p1' => ['fr' => "Un jeu coopératif de chasse en première personne : localiser, traquer et contenir des spécimens dangereux sur des zones isolées, en équipe.",
                    'en' => "A first-person co-op hunting game: locate, track and contain dangerous specimens across isolated zones, as a team."],
                'p2' => ['fr' => "Le studio construit en parallèle le gameplay de chasse, l'IA des créatures et les systèmes d'environnement — cycle jour/nuit, météo dynamique — pensés comme des briques réutilisables pour les prochains titres.",
                    'en' => "The studio is building the hunting gameplay, creature AI and environment systems — day/night cycle, dynamic weather — as reusable building blocks for future titles."],
                'points' => [
                    ['k' => ['fr' => 'Détection', 'en' => 'Detection'], 'v' => ['fr' => "Ciblage et prise de tir pilotés par une IA de créature dédiée.", 'en' => "Targeting and firing driven by dedicated creature AI."]],
                    ['k' => ['fr' => 'Environnement', 'en' => 'Environment'], 'v' => ['fr' => "Cycle jour/nuit et météo dynamique construits comme systèmes indépendants.", 'en' => "Day/night cycle and dynamic weather built as independent systems."]],
                    ['k' => ['fr' => 'Arsenal', 'en' => 'Arsenal'], 'v' => ['fr' => "Armes modulables : poignées, viseurs et accessoires interchangeables en 3D.", 'en' => "Modular weapons: grips, sights and attachments swapped live in 3D."]],
                ],
            ],
            [
                'route' => null,
                'id' => 'ankronic', 'n' => '02',
                'titre' => 'Ankronic',
                'genre' => ['fr' => 'RTS', 'en' => 'RTS'],
                'moteur' => 'Unity 2022', 'langage' => 'C#',
                'depuis' => ['fr' => 'Depuis 2024', 'en' => 'Since 2024'],
                'equipe' => ['fr' => '3 développeur, 2 artiste', 'en' => '3 developer, 2 artist'],
                'plateformes' => 'PC · itch.io',
                'statut' => ['fr' => 'Terminée', 'en' => 'Finished'],
                'sortie' => ['fr' => '2024', 'en' => '2024'],
                'lien' => 'https://rexignis40.itch.io/ankronic',
                'img' => asset('images/ankronic/Ankronic.jpg'),
                'img2' => null,
                'chapo' => ['fr' => "Un RTS dans un univers où la temporalité s'est brisée : les époques se mélangent, et les guerres avec elles.",
                    'en' => "An RTS set in a world where time itself has broken: eras bleed into each other, and so do their wars."],
                'p1' => ['fr' => "Le concept explore la gestion de ressources et le combat à travers des époques qui se superposent sur la même carte.",
                    'en' => "The concept explores resource management and combat across eras that overlap on the same map."],
                'p2' => ['fr' => "Encore au stade de la conception : les systèmes centraux (temporalité, IA de faction) sont en cours de définition avant tout prototype jouable.",
                    'en' => "Still at the design stage: the core systems (temporality, faction AI) are being defined before any playable prototype."],
                'points' => [
                    ['k' => ['fr' => 'Concept', 'en' => 'Concept'], 'v' => ['fr' => "Combat et diplomatie entre factions issues d'époques différentes.", 'en' => "Combat and diplomacy between factions drawn from different eras."]],
                    ['k' => ['fr' => 'Statut', 'en' => 'Status'], 'v' => ['fr' => "Jeu crée en collaboration avec Rexignis40", 'en' => "In collaboration with Rexignis40"]],
                ],
            ],
            [
                'route' => 'studio.project02',
                'id' => 'novum', 'n' => '03',
                'titre' => "Novum : la terre d'après",
                'genre' => ['fr' => 'Survie', 'en' => 'Survival'],
                'moteur' => 'Unity 6', 'langage' => 'C#',
                'depuis' => ['fr' => 'Depuis 2025', 'en' => 'Since 2025'],
                'equipe' => ['fr' => '1 développeur', 'en' => '1 developer'],
                'plateformes' => 'PC · itch.io',
                'statut' => ['fr' => 'En pause', 'en' => 'On hold'],
                'sortie' => ['fr' => 'Non planifiée', 'en' => 'Not scheduled'],
                'lien' => 'https://lakeust-works.itch.io/novum-la-terre-dapres',
                'img' => asset('images/novum/background.png'),
                'img2' => asset('images/novum/Novum.mp4'),
                'chapo' => ['fr' => "Le monde est devenu hostile. Vous vivez la fin du monde à travers votre bunker.",
                    'en' => "The world has turned hostile. You live through the end of it from inside your bunker."],
                'p1' => ['fr' => "Un jeu de survie vu depuis un bunker : gestion des ressources, maintien des systèmes vitaux, sorties risquées vers l'extérieur.",
                    'en' => "A survival game seen from inside a bunker: managing resources, keeping life-support systems running, risky trips outside."],
                'p2' => ['fr' => "Le projet est en phase de cadrage : direction artistique et boucle de gameplay principale restent à valider avant la production.",
                    'en' => "The project is in scoping: art direction and the core gameplay loop still need validating before production starts."],
                'points' => [
                    ['k' => ['fr' => 'Concept', 'en' => 'Concept'], 'v' => ['fr' => "Gestion de bunker et sorties d'exploration risquées.", 'en' => "Bunker management and risky exploration runs."]],
                    ['k' => ['fr' => 'Statut', 'en' => 'Status'], 'v' => ['fr' => "Direction artistique et boucle de jeu en cours de cadrage.", 'en' => "Art direction and gameplay loop being scoped."]],
                ],
            ],
        ];
        $featured = $games[0];
        $alt = true; // dispositions alternées entre fiches (méta à gauche/droite en alternance)

        $nav = [
            ['route' => 'studio.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'studio.works',   'i18n' => 'nav.games',  'keep' => true],
            ['route' => 'studio.lab',     'i18n' => 'nav.lab',  'keep' => false, 'external' => true],
        ];
    @endphp
</head>

    <!-- ==================================================================
         À LA UNE 
    =================================================================== -->
    <section id="alaune" style="position:relative;min-height:100vh;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.05fr);align-items:center;gap:calc(var(--space-8)*3);padding:calc(var(--space-8)*5) var(--space-8) calc(var(--space-8)*4)">
        <div style="max-width:56ch">
            <div data-reveal="rise" style="display:flex;align-items:center;gap:var(--space-4);font-size:11px;letter-spacing:.3em;text-transform:uppercase;color:var(--accent-300)">
                <span class="i18n-fr">À la une</span><span class="i18n-en">Featured</span>
                <span style="width:64px;height:1px;background:linear-gradient(90deg,var(--accent-700),transparent)"></span>
                <span style="color:var(--text-3)"><span class="i18n-fr">{{ $featured['statut']['fr'] }}</span><span class="i18n-en">{{ $featured['statut']['en'] }}</span></span>
            </div>
            <div data-reveal="mask" style="margin-top:var(--space-8)">
                <span class="mask-line"><h1 style="margin:0;font-size:clamp(42px,6.6vw,85px);line-height:.94;letter-spacing:-.035em;font-weight:500;color:var(--text-hi)">{{ $featured['titre'] }}</h1></span>
            </div>
            <p data-reveal="blur" style="margin:var(--space-6) 0 0;font-size:clamp(16px,1.6vw,21px);line-height:1.5;color:var(--text-2);text-wrap:pretty">
                <span class="i18n-fr">{{ $featured['chapo']['fr'] }}</span><span class="i18n-en">{{ $featured['chapo']['en'] }}</span>
            </p>

            <div data-reveal="stagger" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:1px;margin:calc(var(--space-8)*2) 0 0;background:var(--divider);border-top:1px solid var(--divider);border-bottom:1px solid var(--divider)">
                <div style="background:var(--bg);padding:var(--space-6) var(--space-4)">
                    <div style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Moteur</span><span class="i18n-en">Engine</span></div>
                    <div style="margin-top:var(--space-3);font-family:var(--font-heading);font-size:17px;color:var(--text-hi)">{{ $featured['moteur'] }}</div>
                </div>
                <div style="background:var(--bg);padding:var(--space-6) var(--space-4)">
                    <div style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Développement</span><span class="i18n-en">Development</span></div>
                    <div style="margin-top:var(--space-3);font-family:var(--font-heading);font-size:17px;color:var(--text-hi)"><span class="i18n-fr">{{ $featured['depuis']['fr'] }}</span><span class="i18n-en">{{ $featured['depuis']['en'] }}</span></div>
                </div>
                <div style="background:var(--bg);padding:var(--space-6) var(--space-4)">
                    <div style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Statut</span><span class="i18n-en">Status</span></div>
                    <div style="margin-top:var(--space-3);font-family:var(--font-heading);font-size:17px;color:var(--text-hi)"><span class="i18n-fr">{{ $featured['statut']['fr'] }}</span><span class="i18n-en">{{ $featured['statut']['en'] }}</span></div>
                </div>
                <div style="background:var(--bg);padding:var(--space-6) var(--space-4)">
                    <div style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Plateformes</span><span class="i18n-en">Platforms</span></div>
                    <div style="margin-top:var(--space-3);font-family:var(--font-heading);font-size:17px;color:var(--text-hi)">{{ $featured['plateformes'] }}</div>
                </div>
            </div>

            <p data-reveal="blur" data-reveal-delay="120" style="margin:calc(var(--space-8)*2) 0 0;font-size:15px;line-height:1.75;color:var(--text-3);max-width:54ch;text-wrap:pretty">
                <span class="i18n-fr">{{ $featured['p2']['fr'] }}</span><span class="i18n-en">{{ $featured['p2']['en'] }}</span>
            </p>

            <div style="display:flex;flex-wrap:wrap;gap:var(--space-4);margin-top:calc(var(--space-8)*2)">
                <a class="btn btn-primary" href="#{{ $featured['id'] }}"><span class="i18n-fr">Lire la fiche</span><span class="i18n-en">Read the entry</span></a>
                <a class="btn btn-ghost" href="#catalogue"><span class="i18n-fr">Tous les jeux</span><span class="i18n-en">All games</span></a>
            </div>
        </div>

        <div data-reveal="frame" style="position:relative;align-self:stretch;min-height:64vh;margin:calc(var(--space-8)*3) 0;border-radius:var(--radius-lg);overflow:hidden;background:linear-gradient(160deg,var(--bg-2),#0b0c14);box-shadow:var(--shadow-md)">
            <div class="parallax" data-parallax="24" style="position:absolute;inset:-6%">
                @if ($featured['img'])
                    <img src="{{ $featured['img'] }}" alt="{{ $featured['titre'] }}" style="width:100%;height:100%;object-fit:cover">
                @endif
            </div>
            <div style="position:absolute;inset:0;pointer-events:none;background:linear-gradient(90deg, rgba(11,12,20,.55), transparent 38%)"></div>
        </div>

        <div style="position:absolute;left:var(--space-8);bottom:var(--space-6);display:flex;align-items:center;gap:var(--space-3);font-size:10px;letter-spacing:.3em;text-transform:uppercase;color:var(--text-3);animation:lw-cue 2.4s ease-in-out infinite">
            <span class="i18n-fr">Catalogue</span><span class="i18n-en">Catalogue</span>
            <span style="width:34px;height:1px;background:linear-gradient(90deg,var(--text-4),transparent)"></span>
        </div>
    </section>

    <!-- ==================================================================
         CATALOGUE — index de tous les titres
    =================================================================== -->
    <section id="catalogue" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
        <div data-reveal="rise" style="display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:var(--space-6);padding-bottom:var(--space-6);border-bottom:1px solid var(--divider)">
            <h2 style="margin:0;font-size:clamp(26px,3vw,44px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)"><span class="i18n-fr">Le catalogue</span><span class="i18n-en">The catalogue</span></h2>
            <span style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:var(--text-3)">{{ str_pad(count($games), 2, '0', STR_PAD_LEFT) }} <span class="i18n-fr">fiches — moteur, statut, genre</span><span class="i18n-en">entries — engine, status, genre</span></span>
        </div>
        <div data-reveal="converge" style="display:flex;flex-direction:column">
            @foreach ($games as $g)
                <a href="#{{ $g['id'] }}" class="nj-cat-row" style="display:grid;grid-template-columns:52px minmax(0,1.6fr) minmax(0,1fr) minmax(0,1fr) auto;gap:var(--space-6);align-items:baseline;padding:var(--space-6) 0;border-bottom:1px solid var(--divider);color:inherit"
                    <span style="font-family:var(--font-heading);font-size:11px;letter-spacing:.2em;color:var(--accent-300)">{{ $g['n'] }}</span>
                    <span style="font-size:18px;color:var(--text-hi)">{{ $g['titre'] }}</span>
                    <span style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--text-3)">{{ $g['moteur'] }}</span>
                    <span style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">{{ $g['genre']['fr'] }}</span><span class="i18n-en">{{ $g['genre']['en'] }}</span></span>
                    <span style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--text-4)"><span class="i18n-fr">{{ $g['statut']['fr'] }}</span><span class="i18n-en">{{ $g['statut']['en'] }}</span></span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- ==================================================================
         FICHES — une par jeu, disposition alternée
    =================================================================== -->
    @foreach ($games as $i => $g)
        @php $flip = $alt && $i % 2 === 1; @endphp
        <section id="{{ $g['id'] }}" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-split="1" style="display:grid;grid-template-columns:{{ $flip ? 'minmax(0,1fr) minmax(0,340px)' : 'minmax(0,340px) minmax(0,1fr)' }};gap:calc(var(--space-8)*3);align-items:start">

                <div style="position:sticky;top:110px;display:flex;flex-direction:column;gap:var(--space-6);grid-column:{{ $flip ? 2 : 1 }}">
                    <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300)">{{ $g['n'] }} — <span class="i18n-fr">{{ $g['genre']['fr'] }}</span><span class="i18n-en">{{ $g['genre']['en'] }}</span></div>
                    <div data-reveal="mask"><span class="mask-line"><h2 style="margin:0;font-size:clamp(32px,4vw,58px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">{{ $g['titre'] }}</h2></span></div>
                    <div data-reveal="stagger" style="display:flex;flex-direction:column;border-top:1px solid var(--divider)">
                        @foreach ([
                            ['fr' => 'Moteur', 'en' => 'Engine', 'v' => $g['moteur']],
                            ['fr' => 'Langage', 'en' => 'Language', 'v' => $g['langage']],
                            ['fr' => 'Développement', 'en' => 'Development', 'v' => $g['depuis']],
                            ['fr' => 'Équipe', 'en' => 'Team', 'v' => $g['equipe']],
                            ['fr' => 'Plateformes', 'en' => 'Platforms', 'v' => $g['plateformes']],
                            ['fr' => 'Statut', 'en' => 'Status', 'v' => $g['statut']],
                            ['fr' => 'Date de sortie', 'en' => 'Release date', 'v' => $g['sortie']],
                            ['fr' => 'Lien', 'en' => 'Link', 'v' => $g['lien']],
                        ] as $m)
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.15fr);gap:var(--space-4);align-items:baseline;padding:var(--space-4) 0;border-bottom:1px solid var(--divider)">
                                <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">{{ $m['fr'] }}</span><span class="i18n-en">{{ $m['en'] }}</span></span>
                                <span style="font-size:14px;color:var(--text-2)">
                                    @if (!empty($g['lien']) && $m['v'] === $g['lien'] && $g['lien'] !== '-')
                                        {{-- Cas lien --}}
                                        <a href="{{ $g['lien'] }}" target="_blank" rel="noopener noreferrer"
                                           style="color:var(--accent-400);text-decoration:underline">
                                            {{ $g['lien'] }}
                                        </a>

                                    @elseif (is_array($m['v']))
                                        {{-- Cas traduction FR/EN --}}
                                        <span class="i18n-fr">{{ $m['v']['fr'] }}</span>
                                        <span class="i18n-en">{{ $m['v']['en'] }}</span>
                                    @else
                                        {{-- Cas texte simple --}}
                                        {{ $m['v'] }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                    @if ($g['route'])
                    <div style="display:flex;gap:var(--space-4);">
                        <a class="btn btn-primary" href="{{ route($g['route']) }}"><span class="i18n-fr">Lire la fiche</span><span class="i18n-en">Read the entry</span></a>
                    </div>
                    @endif
                </div>

                <div style="display:flex;flex-direction:column;gap:calc(var(--space-8)*2);grid-column:{{ $flip ? 1 : 2 }}">
                    <div data-reveal="frame" style="position:relative;aspect-ratio:16/9;border-radius:var(--radius-lg);overflow:hidden;background:linear-gradient(160deg,var(--bg-2),#0b0c14);box-shadow:var(--shadow-sm)">
                        @if ($g['img'])
                            <img src="{{ $g['img'] }}" alt="{{ $g['titre'] }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                        @else
                            <div style="position:absolute;inset:0;display:flex;align-items:flex-end;background:radial-gradient(140% 90% at 20% 0%,rgba(145,132,217,.16),transparent 60%),repeating-linear-gradient(135deg,rgba(233,233,237,.045) 0 10px,transparent 10px 20px),var(--bg-2)">
                                <span style="padding:var(--space-4);font-size:10px;letter-spacing:.1em;line-height:1.5;color:var(--text-4)">{{ $g['titre'] }} — <span class="i18n-fr">capture à venir</span><span class="i18n-en">capture coming soon</span></span>
                            </div>
                        @endif
                    </div>

                    <div data-reveal="stagger" style="display:flex;flex-direction:column;gap:var(--space-6);max-width:64ch">
                        <p style="margin:0;font-size:19px;line-height:1.55;color:var(--text-2);text-wrap:pretty"><span class="i18n-fr">{{ $g['chapo']['fr'] }}</span><span class="i18n-en">{{ $g['chapo']['en'] }}</span></p>
                        <p style="margin:0;font-size:15px;line-height:1.75;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $g['p1']['fr'] }}</span><span class="i18n-en">{{ $g['p1']['en'] }}</span></p>
                        <p style="margin:0;font-size:15px;line-height:1.75;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $g['p2']['fr'] }}</span><span class="i18n-en">{{ $g['p2']['en'] }}</span></p>
                    </div>

                    <div data-reveal="converge" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1px;background:var(--divider);border-top:1px solid var(--divider);border-bottom:1px solid var(--divider)">
                        @foreach ($g['points'] as $p)
                            <div style="background:var(--bg);padding:var(--space-6) var(--space-6) ">
                                <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300)"><span class="i18n-fr">{{ $p['k']['fr'] }}</span><span class="i18n-en">{{ $p['k']['en'] }}</span></div>
                                <p style="margin:var(--space-3) 0 0;font-size:14px;line-height:1.65;color:var(--text-3)"><span class="i18n-fr">{{ $p['v']['fr'] }}</span><span class="i18n-en">{{ $p['v']['en'] }}</span></p>
                            </div>
                        @endforeach
                    </div>

                    @if ($g['img2'])
                        @php $isVideo = (bool) preg_match('/\.(mp4|webm|mov)$/i', $g['img2']); @endphp
                        <div style="display:flex;flex-direction:column;gap:var(--space-4)">
                            <div style="display:flex;align-items:center;gap:var(--space-4);font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--text-3)">
                                <span class="i18n-fr">En jeu</span><span class="i18n-en">In-game</span>
                                <span style="flex:1;height:1px;background:linear-gradient(90deg,var(--divider),transparent)"></span>
                            </div>
                            <div data-reveal="frame" style="position:relative;aspect-ratio:21/9;border-radius:var(--radius-lg);overflow:hidden;background:linear-gradient(160deg,var(--bg-1),var(--bg-2));border:1px solid var(--divider)">
                                @if ($isVideo)
                                    <video src="{{ $g['img2'] }}" controls preload="metadata" playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
                                @else
                                    <img src="{{ $g['img2'] }}" alt="{{ $g['titre'] }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </section>
    @endforeach

    <!-- ==================================================================
         CONTACT
    =================================================================== -->
    <section style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*3) var(--space-8) calc(var(--space-8)*6)">
        <div style="display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:var(--space-8);padding-top:calc(var(--space-8)*2);border-top:1px solid var(--divider)">
            <h2 data-reveal="rise" style="margin:0;max-width:22ch;font-size:clamp(26px,3.4vw,48px);line-height:1.05;letter-spacing:-.03em;color:var(--text-hi)"><span class="i18n-fr">Un projet de jeu à porter&nbsp;?</span><span class="i18n-en">A game project to carry&nbsp;?</span></h2>
            <a class="btn btn-primary" href="{{ route('studio.about') }}#contact"><span class="i18n-fr">Parler du projet</span><span class="i18n-en">Talk about it</span></a>
        </div>
    </section>
@endsection
