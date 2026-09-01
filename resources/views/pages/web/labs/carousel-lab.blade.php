<!-- Carousel Lab — douze écrans, un seul actif à la fois (rien ne défile,
     même convention que les autres labs), commutés par le rail gauche ou les
     touches 1-9 (10-12 par le rail/catalogue). Six lois de placement rejouent
     le même jeu de huit plans ; deux écrans passent par un post-traitement
     (dissolve / fracture) ; trois occupent l'écran entier plutôt qu'une carte
     (bandeau / rideau / travelling) ; le dernier est la fiche technique.
     Toute la mécanique vit dans resources/js/labs/carousel/carousel-lab.js —
     ce gabarit ne rend que le DOM qu'elle pilote. Page autonome (pas de
     layout partagé, pas de Barba) : son propre en-tête + rail + barre de
     progression suffisent.-->

@extends('layouts.site')
@section('title', 'Carousel Lab — Lakeust Works')
@section('content')
<head>
    @vite(['resources/css/lab.css', 'resources/js/labs/carousel/carousel-lab.js'])

    @php
    $items = [
        ['n' => '01', 'title' => 'Projet 01', 'year' => '2026', 'disc' => 'Interaction / WebGL'],
        ['n' => '02', 'title' => 'Projet 02', 'year' => '2026', 'disc' => 'Direction artistique'],
        ['n' => '03', 'title' => 'Projet 03', 'year' => '2025', 'disc' => 'Site vitrine'],
        ['n' => '04', 'title' => 'Projet 04', 'year' => '2025', 'disc' => 'Jeu — Unity'],
        ['n' => '05', 'title' => 'Projet 05', 'year' => '2025', 'disc' => 'Identité'],
        ['n' => '06', 'title' => 'Projet 06', 'year' => '2024', 'disc' => 'Motion'],
        ['n' => '07', 'title' => 'Projet 07', 'year' => '2024', 'disc' => 'Outil interne'],
        ['n' => '08', 'title' => 'Projet 08', 'year' => '2023', 'disc' => 'Prototype R&D'],
    ];

    $sections = [
        ['n' => 'IX', 'name' => 'Catalogue', 'type' => 'sheet'],
        ['n' => '01', 'name' => 'Arc', 'type' => 'layout', 'i18n' => 'carousel.cat.1',
            'lead' => "Cover-flow spatial : la profondeur croît au carré de la distance, la rotation reste plafonnée. Le plan actif est le seul parfaitement de face.",
            'tech' => "Profondeur en d², rotation bornée à ±44°, flou progressif au-delà d'un tiers de plan d'écart.",
            'scroll' => "Position fractionnaire → décalage X, profondeur en d², rotation plafonnée.",
            'vu' => "Le plan actif de face, les suivants qui reculent et pivotent jusqu'à ±44°, floutés au-delà d'un tiers de plan.",
            'cout' => "Faible — une seule loi trigonométrique par plan, pas de géométrie recalculée en boucle.",
            'portfolio' => "Fort — le plus lisible des six, bon candidat pour une grille de projets."],
        ['n' => '02', 'name' => 'Anneau', 'type' => 'layout', 'i18n' => 'carousel.cat.2',
            'lead' => "Un cercle horizontal dont le rayon vaut deux fois la carte : on est dedans, les voisins passent en périphérie plutôt que derrière.",
            'tech' => "Rayon à 1,95 × la carte, pas angulaire de 0,52 rad, aucun changement d'échelle.",
            'scroll' => "Position fractionnaire → angle sur le cercle, 0,52 rad par pas.",
            'vu' => "Les huit plans répartis sur un cercle, aucun changement d'échelle — seule la profondeur trahit la position.",
            'cout' => "Faible — une rotation, un rayon fixe.",
            'portfolio' => "Moyen — élégant mais les plans latéraux restent petits à l'écran."],
        ['n' => '03', 'name' => 'Orbite', 'type' => 'layout', 'i18n' => 'carousel.cat.3',
            'lead' => "Même cercle, mais le déport vertical prend le signe de la distance : ce qui est passé remonte, ce qui vient descend. Les plans arrivent des quatre côtés.",
            'tech' => "Pas de 0,60 rad, déport vertical signé à 0,58 × le rayon, léger roulis en Z.",
            'scroll' => "Position fractionnaire → angle + déport vertical signé (0,58 × le rayon).",
            'vu' => "Les plans qui montent ou descendent selon qu'ils sont passés ou à venir, arrivée par les quatre côtés de l'écran.",
            'cout' => "Moyen — même base que l'anneau, une composante verticale signée en plus.",
            'portfolio' => "Fort — la variation la plus dynamique visuellement du lot."],
        ['n' => '04', 'name' => 'Hélice', 'type' => 'layout', 'i18n' => 'carousel.cat.4',
            'lead' => "Une seule phase pilote deux choses : la hauteur monte linéairement, la position latérale suit son sinus. La colonne tourne en s'élevant.",
            'tech' => "Pas de 0,78 rad, hauteur de 0,40 × la carte par rang, échelle plancher à 0,70.",
            'scroll' => "Position fractionnaire → hauteur linéaire + position latérale en sinus de la même phase.",
            'vu' => "Une colonne qui tourne en s'élevant, chaque plan un peu plus haut que le précédent.",
            'cout' => "Faible — une seule phase, deux fonctions trigonométriques.",
            'portfolio' => "Moyen — lecture verticale, adaptée à un historique ou une chronologie."],
        ['n' => '05', 'name' => 'Pile', 'type' => 'layout', 'i18n' => 'carousel.cat.5',
            'lead' => "Tout en Z, presque rien en X : l'actif devant, les suivants empilés derrière avec un décalage constant. Le cas dense, pour lire un ordre.",
            'tech' => "126 px de recul et 20 px de décalage latéral par rang ; l'échelle ne descend pas sous 0,72.",
            'scroll' => "Position fractionnaire → recul Z (126 px/rang) + décalage latéral (20 px/rang).",
            'vu' => "Une pile compacte, l'actif devant, les suivants qui reculent avec un léger décalage latéral.",
            'cout' => "Faible — la disposition la plus simple des six.",
            'portfolio' => "Fort — le cas dense, utile quand l'ordre compte plus que l'espace."],
        ['n' => '06', 'name' => 'Grille', 'type' => 'layout', 'i18n' => 'carousel.cat.6',
            'lead' => "Trois colonnes qui défilent en continu ; le plan actif quitte sa cellule, vient au centre et repousse les autres. La grille se recompose derrière lui.",
            'tech' => "La ligne suit la position fractionnaire ; la sortie de cellule s'achève aux deux tiers d'un pas.",
            'scroll' => "Position fractionnaire → sortie/entrée de cellule, interpolée sur les deux tiers d'un pas.",
            'vu' => "Une grille qui se recompose en direct : le plan actif sort de sa cellule, vient au centre, puis regagne sa place.",
            'cout' => "Moyen — la loi combine une position de grille et une interpolation vers le centre.",
            'portfolio' => "Fort — seule disposition qui montre l'ensemble et l'actif en même temps."],
        ['n' => '07', 'name' => 'Dissolve', 'type' => 'fx', 'cursor' => 'pointer', 'hint' => "Seuil sur bruit fractal", 'i18n' => 'carousel.cat.7',
            'lead' => "Le plan sortant n'est pas fondu : il est détruit. Un seuil balaye un champ de bruit, les pixels tombent au-dessous, et le plan suivant se reforme dans le même mouvement.",
            'tech' => "Graphe de filtres SVG sur du DOM vivant : bruit fractal, seuil dur, seconde coupe décalée de 0.10 pour la bande de front, teintée en accent. Deux graines différentes pour que l'entrant ne soit pas l'exact négatif du sortant.",
            'scroll' => "Position fractionnaire → index élu ; le franchissement d'un demi-plan déclenche la passe de seuil.",
            'vu' => "Un seuil qui balaye un bruit fractal, le plan sortant mangé pixel par pixel, l'entrant qui se reforme dans un bruit différent.",
            'cout' => "Élevé — graphe de filtres SVG monté une fois, mais coûteux à l'exécution sur GPU faible.",
            'portfolio' => "Fort — la transition la plus mémorable des huit, à réserver à un moment fort."],
        ['n' => '08', 'name' => 'Fracture', 'type' => 'fx', 'cursor' => 'pointer', 'hint' => "Displacement · split RVB", 'i18n' => 'carousel.cat.8',
            'lead' => "Une displacement map disloque le plan pendant un quart de seconde ; les trois couches de couleur se séparent, la coupe a lieu au pic, et l'image se recompose derrière.",
            'tech' => "Turbulence anisotrope pour des bandes horizontales, déplacement jusqu'à 52 px, séparation RVB par décalage inverse des canaux et recomposition en screen. Le sens du décalage suit celui de la navigation.",
            'scroll' => "Position fractionnaire → index élu ; le franchissement déclenche la passe de displacement.",
            'vu' => "Le plan qui se disloque en bandes, les trois couches de couleur qui se séparent puis se recomposent.",
            'cout' => "Élevé — displacement map + séparation RVB, même famille de coût que Dissolve.",
            'portfolio' => "Fort — plus abrupt que Dissolve, adapté à un changement de registre plutôt qu'une continuité."],
        ['n' => '09', 'name' => 'Bandeau', 'type' => 'full', 'hint' => "Glisser · Plein cadre", 'i18n' => 'carousel.cat.9',
            'lead' => "Plus de cartes : chaque plan occupe la section entière. Les panneaux glissent bord à bord, l'image se déplace moins vite que son cadre.",
            'tech' => "Un plan par largeur d'écran, parallaxe interne à 22 %, légère surcote d'échelle pour éviter les bords.",
            'scroll' => "Position fractionnaire → glissement bord à bord, parallaxe interne du média à 22 %.",
            'vu' => "Un plan par écran, plein cadre, qui glisse latéralement pendant que son image dérive plus lentement à l'intérieur.",
            'cout' => "Faible — une translation du plan, une contre-translation du média.",
            'portfolio' => "Fort — le format le plus immersif du lot, adapté à une galerie de captures."],
        ['n' => '10', 'name' => 'Rideau', 'type' => 'full', 'hint' => "Glisser · Recouvrement", 'i18n' => 'carousel.cat.10',
            'lead' => "Rien ne sort : le plan suivant se découvre par le bas et recouvre le précédent, qui recule d'un cran sans jamais bouger latéralement.",
            'tech' => "Découpe en inset sur le panneau entrant, contre-glissement interne de 14 %, empilement par index et non par distance.",
            'scroll' => "Position fractionnaire → découpe progressive (clip-path inset) du panneau entrant.",
            'vu' => "Un rideau qui monte depuis le bas et recouvre le plan précédent, empilé derrière sans jamais glisser latéralement.",
            'cout' => "Moyen — clip-path animé par plan, empilement d'index à maintenir.",
            'portfolio' => "Fort — lecture d'un empilement plutôt que d'un défilement, bon candidat pour une chronologie."],
        ['n' => '11', 'name' => 'Travelling', 'type' => 'full', 'hint' => "Glisser · Dolly", 'i18n' => 'carousel.cat.11',
            'lead' => "Aucun déplacement dans le plan : la caméra avance. Le suivant arrive de loin en s'ouvrant, le précédent se retire vers le fond.",
            'tech' => "Échelle 1,16 en amont, 0,90 en aval, opacité en 1,35 × la distance, contre-zoom interne pour tenir la profondeur.",
            'scroll' => "Position fractionnaire → échelle du plan (amont/aval) + contre-zoom interne du média.",
            'vu' => "Un travelling avant : le plan à venir s'ouvre en grandissant, le plan passé recule en rétrécissant, jamais de mouvement latéral.",
            'cout' => "Faible — une échelle par plan, une contre-échelle du média.",
            'portfolio' => "Moyen — effet de profondeur marqué, à réserver à un nombre restreint de plans."],
        ];
    @endphp
</head>



<div data-cl-root style="position:relative;min-height:100vh;background:var(--bg);color:var(--text);font-family:var(--font);font-weight:300;-webkit-font-smoothing:antialiased;text-wrap:pretty;">

    <carousel-field data-cl-field quality="auto" style="position:fixed;inset:0;z-index:0;display:block;overflow:hidden;"></carousel-field>

    <header style="position:fixed;top:0;left:0;right:0;z-index:60;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px clamp(16px,4vw,40px);pointer-events:none;mix-blend-mode:difference;">
        <div style="display:flex;align-items:baseline;gap:12px;">
            <span style="font-size:11px;letter-spacing:.30em;text-transform:uppercase;color:#e2ddd1;">Carousel Lab</span>
            <span data-lab-title style="font-size:11px;letter-spacing:.18em;color:rgba(226,221,209,.45);"></span>
        </div>
        <span data-lab-count style="font-size:11px;letter-spacing:.22em;color:rgba(226,221,209,.38);font-variant-numeric:tabular-nums;"></span>
    </header>

    <nav style="position:fixed;left:clamp(10px,2.4vw,26px);top:50%;transform:translateY(-50%);z-index:60;display:flex;flex-direction:column;padding-left:12px;">
        <span style="position:absolute;left:0;top:8px;bottom:8px;width:1px;background:var(--line-2);"></span>
        <span data-rail-indicator style="position:absolute;left:0;top:8px;width:1px;height:10px;background:var(--accent);box-shadow:0 0 10px rgba(145,132,217,.7);transition:transform .45s cubic-bezier(.16,1,.3,1);"></span>
        @foreach ($sections as $s)
            <button type="button" data-rail title="{{ $s['name'] }}" style="color:rgba(226,221,209,.40);">
                <span>{{ $s['n'] }}</span>
            </button>
        @endforeach
    </nav>

    <div style="position:fixed;left:0;bottom:0;height:1px;width:100%;background:var(--line);z-index:60;">
        <span data-progress-bar style="display:block;height:100%;width:100%;transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.25),var(--accent));transition:transform .5s cubic-bezier(.16,1,.3,1);"></span>
    </div>

    <main style="position:relative;z-index:1;">
        @foreach ($sections as $idx => $s)
            <section data-cl-section data-cl-index="{{ $idx }}" data-screen-label="{{ $s['n'] }} {{ $s['name'] }}" @if ($idx !== 0) style="display:none;" @endif>

                <div class="cl-intro">
                    <span class="cl-ghost">{{ $s['n'] }}</span>
                    <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,var(--accent),transparent);"></span>
                    <h1 style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98;">{{ $s['name'] }}</h1>
                    @if ($s['type'] !== 'sheet')
                        <p style="margin:0;max-width:56ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:var(--text-2);">{{ $s['lead'] }}</p>
                        <p style="margin:0;max-width:52ch;font-size:13px;line-height:1.7;color:var(--text-3);">{{ $s['tech'] }}</p>
                    @else
                        <p style="margin:0;max-width:60ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:var(--text-2);">Une seule valeur pilote la scène&nbsp;: la position fractionnaire dans la liste. Le scroll l'écrit, le drag et le clavier la réécrivent en déplaçant le scroll — donc aucune timeline ne s'empile et aucune interaction n'entre en conflit avec une autre. Six dispositions se commutent à chaud sur les mêmes huit plans.</p>
                    @endif
                </div>

                @if ($s['type'] === 'layout')
                    <div data-stage style="position:relative;height:100vh;overflow:hidden;touch-action:pan-y;cursor:grab;">
                        <div data-deck style="position:absolute;inset:0;perspective:1500px;perspective-origin:50% 46%;transform-style:preserve-3d;">
                            @foreach ($items as $it)
                                <article data-card>
                                    <div class="cl-shot"><div class="media-fill" style="position:absolute;inset:0;"></div></div>
                                    <div class="cl-body">
                                        <div class="cl-row">
                                            <span style="font-family:ui-monospace,monospace;font-size:10px;letter-spacing:.22em;color:var(--accent);">{{ $it['n'] }}</span>
                                            <span style="font-family:ui-monospace,monospace;font-size:10px;letter-spacing:.16em;color:rgba(226,221,209,.34);">{{ $it['year'] }}</span>
                                        </div>
                                        <span class="cl-rule"></span>
                                        <h2 style="margin:0;font-size:clamp(15px,1.5vw,19px);font-weight:300;letter-spacing:-.01em;line-height:1.15;">{{ $it['title'] }}</h2>
                                        <p class="cl-disc">{{ $it['disc'] }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="cl-hint">
                            <span>Glisser</span><span class="sep"></span>
                            <span>Molette</span><span class="sep"></span>
                            <span>← →</span><span class="sep"></span>
                            <span>1–6 dispositions</span>
                        </div>
                    </div>
                @elseif ($s['type'] === 'full')
                    <div data-stage style="position:relative;height:100vh;overflow:hidden;touch-action:pan-y;cursor:grab;">
                        <div data-full-deck style="position:absolute;inset:0;">
                            @foreach ($items as $it)
                                <article data-full-card style="position:absolute;inset:0;overflow:hidden;opacity:0;visibility:hidden;will-change:transform,opacity,clip-path;background:#080910;">
                                    <div data-full-media style="position:absolute;inset:0;will-change:transform;">
                                        <div class="media-fill" style="position:absolute;inset:0;"></div>
                                    </div>
                                    <div style="position:absolute;inset:0;pointer-events:none;background:linear-gradient(to top,rgba(5,5,10,.88),rgba(5,5,10,.10) 52%,rgba(5,5,10,.42));"></div>
                                    <div data-full-copy style="position:absolute;left:clamp(32px,7vw,150px);right:clamp(32px,7vw,150px);bottom:clamp(56px,10vh,120px);display:flex;flex-direction:column;gap:12px;will-change:transform,opacity;">
                                        <div style="display:flex;align-items:baseline;gap:16px;">
                                            <span style="font-family:ui-monospace,monospace;font-size:10px;letter-spacing:.22em;color:var(--accent);">{{ $it['n'] }}</span>
                                            <span style="flex:1;max-width:180px;height:1px;background:linear-gradient(90deg,rgba(145,132,217,.55),transparent);"></span>
                                            <span style="font-family:ui-monospace,monospace;font-size:10px;letter-spacing:.16em;color:rgba(226,221,209,.42);">{{ $it['year'] }}</span>
                                        </div>
                                        <h2 style="margin:0;font-size:clamp(30px,5.4vw,78px);font-weight:200;letter-spacing:-.03em;line-height:1;">{{ $it['title'] }}</h2>
                                        <p style="margin:0;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.46);">{{ $it['disc'] }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="cl-hint" style="z-index:200;">
                            <span>Glisser</span><span class="sep"></span>
                            <span>{{ $s['hint'] }}</span>
                        </div>
                    </div>
                @elseif ($s['type'] === 'fx')
                    <div data-stage style="position:relative;height:100vh;overflow:hidden;touch-action:pan-y;cursor:pointer;">
                        <div data-fx-deck style="position:absolute;inset:0;display:grid;place-items:center;">
                            @foreach ($items as $it)
                                <article data-fx-card>
                                    <div class="cl-shot"><div class="media-fill" style="position:absolute;inset:0;"></div></div>
                                    <div class="cl-foot">
                                        <div style="display:flex;align-items:baseline;gap:14px;min-width:0;">
                                            <span style="font-family:ui-monospace,monospace;font-size:10px;letter-spacing:.22em;color:var(--accent);">{{ $it['n'] }}</span>
                                            <h2 style="margin:0;font-size:clamp(16px,1.7vw,22px);font-weight:300;letter-spacing:-.01em;line-height:1.15;">{{ $it['title'] }}</h2>
                                        </div>
                                        <span style="font-family:ui-monospace,monospace;font-size:10px;letter-spacing:.16em;color:rgba(226,221,209,.34);white-space:nowrap;">{{ $it['disc'] }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div class="cl-hint" style="z-index:5;">
                            <span>Molette</span><span class="sep"></span>
                            <span>Clic</span><span class="sep"></span>
                            <span>← →</span><span class="sep"></span>
                            <span>{{ $s['hint'] }}</span>
                        </div>
                    </div>
                @else
                    <div style="position:relative;z-index:1;display:flex;flex-direction:column;gap:clamp(28px,4vw,52px);padding:0 clamp(56px,9vw,180px) clamp(70px,10vw,130px);">
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr));gap:clamp(20px,3vw,44px);padding-top:clamp(8px,2vw,20px);border-top:1px solid var(--line);">
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--text-3);" data-i18n="carousel.driver.label">Le pilote</span>
                                <p style="margin:0;font-size:13px;line-height:1.7;color:var(--text-2);" data-i18n-html data-i18n="carousel.driver.desc">Une section épinglée, <code style="font-size:12px;color:var(--accent-2);">scrub</code> vrai. La progression devient la position fractionnaire&nbsp;; le rendu lisse la valeur d'une frame à l'autre au lieu de tweener chaque plan.</p>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--text-3);" data-i18n="carousel.inputs.label">Les entrées</span>
                                <p style="margin:0;font-size:13px;line-height:1.7;color:var(--text-2);" data-i18n="carousel.inputs.desc">Drag, clic, flèches et tactile n'écrivent jamais la position&nbsp;: ils déplacent le scroll par Lenis. Un seul système tient le scroll, donc rien à réconcilier.</p>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--text-3);" data-i18n="carousel.background.label">Le fond</span>
                                <p style="margin:0;font-size:13px;line-height:1.7;color:var(--text-2);" data-i18n-html data-i18n="carousel.background.desc"><code style="font-size:12px;color:var(--accent-2);">&lt;carousel-field&gt;</code> possède le renderer, la scène et la boucle&nbsp;; la page ne lui pousse que des valeurs par <code style="font-size:12px;color:var(--accent-2);">setField()</code> — même contrat que les stages existants.</p>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--text-3);" data-i18n="carousel.data.label">Les données</span>
                                <p style="margin:0;font-size:13px;line-height:1.7;color:var(--text-2);" data-i18n="carousel.data.desc">Un tableau d'items — titre, année, discipline. Ajouter ou retirer une entrée ne touche à aucune loi de placement.</p>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--text-3);" data-i18n="carousel.postfx.label">Le post-traitement</span>
                                <p style="margin:0;font-size:13px;line-height:1.7;color:var(--text-2);" data-i18n-html data-i18n="carousel.postfx.desc">Les écrans 07 et 08 passent par un graphe de filtres SVG monté une fois&nbsp;: chaque image ne réécrit qu'un seuil ou une amplitude — <code style="font-size:12px;color:var(--accent-2);">intercept</code>, <code style="font-size:12px;color:var(--accent-2);">scale</code> — et le filtre est retiré du plan dès la fin.</p>
                            </div>
                        </div>

                        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));">
                            @foreach (array_slice($sections, 1, count($sections) - 1, true) as $rn => $r)
                                <article style="display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;border:1px solid var(--line);background:rgba(11,12,20,.55);">
                                    <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px;">
                                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent);">{{ $r['n'] }}</span>
                                        <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:var(--text-3);" data-i18n="lab.status">Prototypé</span>
                                    </div>
                                    <h3 style="margin:0;font-size:clamp(20px,2.2vw,28px);font-weight:200;letter-spacing:-.02em;line-height:1.1;">{{ $r['name'] }}</h3>
                                    <p style="margin:0;font-size:13.5px;line-height:1.7;color:var(--text-2);" data-i18n="{{ $r['i18n'] }}.desc">{{ $r['lead'] }}</p>
                                    <div style="display:flex;flex-direction:column;gap:9px;padding-top:14px;border-top:1px solid var(--line);">
                                        <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55;"><span style="letter-spacing:.16em;text-transform:uppercase;color:var(--text-3);" data-i18n="lab.field.scroll">Scroll</span><span style="color:rgba(226,221,209,.72);" data-i18n="{{ $r['i18n'] }}.scroll">{{ $r['scroll'] }}</span></span>
                                        <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55;"><span style="letter-spacing:.16em;text-transform:uppercase;color:var(--text-3);" data-i18n="lab.field.seen">Vu</span><span style="color:rgba(226,221,209,.72);" data-i18n="{{ $r['i18n'] }}.seen">{{ $r['vu'] }}</span></span>
                                        <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55;"><span style="letter-spacing:.16em;text-transform:uppercase;color:var(--text-3);" data-i18n="lab.field.tech">Tech</span><span style="color:rgba(226,221,209,.72);">{{ $r['tech'] }}</span></span>
                                        <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55;"><span style="letter-spacing:.16em;text-transform:uppercase;color:var(--text-3);" data-i18n="lab.field.cost">Coût</span><span style="color:rgba(226,221,209,.72);" data-i18n="{{ $r['i18n'] }}.cost">{{ $r['cout'] }}</span></span>
                                        <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55;"><span style="letter-spacing:.16em;text-transform:uppercase;color:var(--text-3);" data-i18n="lab.field.portfolio">Portfolio</span><span style="color:rgba(226,221,209,.72);" data-i18n="{{ $r['i18n'] }}.portfolio">{{ $r['portfolio'] }}</span></span>
                                    </div>
                                    <button type="button" class="lab-cta" data-registre="{{ $rn }}"><span data-i18n="lab.open">Ouvrir</span> {{ $r['n'] }}</button>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endforeach
    </main>
</div>
@endsection
