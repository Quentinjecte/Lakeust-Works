<!-- Catalogue — même gabarit de fiche que les autres labs (tag, titre,
    description, cinq champs, CTA). « Déclenche » remplace « Scroll » :
    les neuf concepts se jouent à l'entrée du bloc dans le viewport
    (IntersectionObserver), pas au défilement continu.-->

@extends('layouts.site')
@section('title', 'Animation Lab — Lakeust Works')
@section('content')
<head>
    @vite(['resources/css/lab.css', 'resources/css/labs/animation-lab.css', 'resources/js/labs/animation/animation-lab.js'])

    @php
    $sections = [
        ['n' => 'X', 'name' => 'Catalogue', 'type' => ''],
        ['n' => '01', 'name' => 'Broken Glass', 'i18n' => 'animation.cat.1', 'tech' => 'SVG · 20 fragments · stagger aléatoire',
            'desc' => "Le cadre se fissure en 20 éclats SVG disposés autour du périmètre, avec quelques lignes de fracture qui filent vers le centre.",
            'vu' => "Des éclats triangulaires qui se referment vers le centre en cascade, dans un ordre légèrement aléatoire.",
            'cout' => "Moyen — géométrie recalculée à chaque resize, mais pas de canvas à redessiner par frame.",
            'portfolio' => "Fort pour une accroche de section ponctuelle — trop marqué pour un usage répété sur la même page."],
        ['n' => '02', 'name' => 'Industrial Scan', 'i18n' => 'animation.cat.2', 'tech' => 'DOM · arêtes tiretées · balayage',
            'desc' => "Un cadre technique se dessine — bords tiretés, coins en équerre, rampe de graduations — puis un balayage lumineux traverse le bloc de haut en bas.",
            'vu' => "Bordures et graduations qui apparaissent en premier, puis une bande lumineuse qui balaye le contenu.",
            'cout' => "Faible — que des éléments DOM statiques, rien à recalculer par frame.",
            'portfolio' => "Fort pour une section technique ou un HUD — le vocabulaire visuel est déjà celui du reste du site."],
        ['n' => '03', 'name' => 'Organic Reveal', 'i18n' => 'animation.cat.3', 'tech' => 'Canvas · destination-out · blur',
            'desc' => "Une tache d'encre organique grandit depuis un coin et dévoile le contenu derrière elle, contour flou plutôt que net.",
            'vu' => "Une forme irrégulière qui s'étend en respirant légèrement, jusqu'à couvrir tout le cadre.",
            'cout' => "Moyen — redessiné par frame le temps de la révélation, mais léger (une seule forme, pas de particules).",
            'portfolio' => "Fort et singulier — se démarque des transitions géométriques déjà nombreuses ailleurs sur le site."],
        ['n' => '04', 'name' => 'Glitch Frame', 'i18n' => 'animation.cat.4', 'tech' => 'DOM · 7 tranches · dédoublement',
            'desc' => "Le cadre se découpe en 7 tranches horizontales qui rentrent en place indépendamment, pendant que deux doublures colorées se désalignent puis se rejoignent.",
            'vu' => "Un effet de dédoublement chromatique bref (accent + encre) qui se résorbe en un cadre net.",
            'cout' => "Faible — 9 éléments DOM fixes, aucune géométrie recalculée en boucle.",
            'portfolio' => "Moyen : lisible et technique, mais à réserver à un contexte qui assume le vocabulaire « erreur/signal »."],
        ['n' => '05', 'name' => 'Wireframe', 'i18n' => 'animation.cat.5', 'tech' => 'CSS 3D · 12 arêtes · aplatissement',
            'desc' => "Un volume filaire (12 arêtes, perspective CSS) s'aplatit progressivement jusqu'à se confondre avec le plan du cadre.",
            'vu' => "Une boîte 3D en fil de fer qui perd sa profondeur et devient un rectangle plat.",
            'cout' => "Moyen — la 3D CSS est peu coûteuse, mais sensible aux erreurs de perspective sur petits écrans.",
            'portfolio' => "Fort pour une section qui parle de structure ou d'architecture — le concept illustre littéralement l'idée."],
        ['n' => '06', 'name' => 'Particle Assembly', 'i18n' => 'animation.cat.6', 'tech' => 'Canvas · glyphes échantillonnés',
            'desc' => "Des particules dispersées convergent vers des points échantillonnés sur le contenu et le recomposent, comme un nuage qui se condense en texte.",
            'vu' => "Un nuage de points qui se rassemble en glyphes lisibles.",
            'cout' => "Élevé — nombre de particules proportionnel à la densité de texte, à surveiller sur mobile.",
            'portfolio' => "Très fort pour un titre d'accroche isolé — trop coûteux pour plusieurs blocs sur la même page."],
        ['n' => '07', 'name' => 'Shattered Reveal', 'i18n' => 'animation.cat.7', 'tech' => 'DOM · clones clippés · réalignement',
            'desc' => "Le contenu est cloné en 8 bandes découpées en dents de scie, dispersées puis réalignées jusqu'à ne plus faire qu'un bloc net.",
            'vu' => "Des lignes de texte qui semblent déchirées puis qui se recollent en place.",
            'cout' => "Moyen à élevé — chaque bande clone le DOM réel du bloc, donc son coût suit celui du contenu.",
            'portfolio' => "Fort pour un bloc de texte éditorial — l'effet reste lisible, contrairement à des concepts plus abstraits."],
        ['n' => '08', 'name' => 'Liquid Glass', 'i18n' => 'animation.cat.8', 'tech' => 'backdrop-filter · clip-path',
            'desc' => "Une bande de verre dépoli (backdrop-filter) balaye le cadre en biais pendant que le contenu se révèle derrière elle par un clip-path progressif.",
            'vu' => "Un reflet flouté qui glisse sur le contenu, laissant le texte net derrière son passage.",
            'cout' => "Élevé — backdrop-filter est cher à composer, à limiter à une bande étroite et à un seul passage.",
            'portfolio' => "Fort visuellement mais à tester sur les GPU faibles avant un usage répété."],
        ['n' => '09', 'name' => 'Chevron Mosaic', 'i18n' => 'animation.cat.9', 'tech' => 'SVG · maille triangulaire · masque chevron',
            'desc' => "Une maille de triangles en chevrons se dévoile depuis le centre vers les bords, chaque triangle rejoignant sa place avec un délai qui dépend de sa distance à l'axe.",
            'vu' => "Un masque en dents de scie qui se résorbe du centre vers les bords, en V inversé.",
            'cout' => "Moyen — nombre de triangles proportionnel à la largeur de l'écran, calculé une fois au build.",
            'portfolio' => "Fort pour une bannière ou un hero — motif directement réutilisable comme élément de marque."],
        ['n' => '10', 'name' => 'Orbite', 'i18n' => 'animation.cat.10', 'tech' => 'DOM · ellipse en perspective · rideau de sortie',
            'desc' => "Le carrefour du site (voir '/'), isolé ici pour l'étudier seul : deux nœuds tournent sur une ellipse en perspective, celui que le pointeur approche passe devant et ouvre sa fiche.",
            'vu' => "Deux points qui orbitent devant un halo central ; le survol en avance un, révèle son panneau et son bouton d'entrée, un rideau couvre l'écran avant de partir.",
            'cout' => "Faible — une boucle trigonométrique par nœud et par frame, aucun DOM recréé en cours de route.",
            'portfolio' => "C'est la page d'accueil réelle du site — vue ici hors contexte, seule, pour l'étudier sans le reste de la page."],
        ];

        $branches = [
        [
            'label' => 'Lakeust Web',
            'tag'   => 'three.js · GSAP · WebGL',
            'fr'    => "Sites, expériences temps réel et laboratoires d'interaction.",
            'en'    => 'Sites, real-time experiences and interaction labs.',
            'href'  => route('web.works'),
            'open'  => true,
        ],
        [
            'label' => 'Lakeust Studio',
            'tag'   => 'Unity · C# · shaders',
            'fr'    => 'Jeux, moteurs maison et outils internes.',
            'en'    => 'Games, in-house engines and internal tools.',
            'href'  => '#top',
            'open'  => false,
        ],
    ];
    @endphp
</head>

{{-- Dix scènes, une visible à la fois (voir .lab-sec / .is-active) : les neuf
     premières rejouent un concept d'animation d'entrée de section porté depuis
     lab/section-anim.js (interface play/reset/resize/destroy inchangée), la
     dixième les liste et saute directement dessus. --}}
<div data-sa-root style="position:relative;min-height:100vh;background:#05050a">

    <nav style="position:fixed;left:clamp(10px,2.4vw,26px);top:50%;transform:translateY(-50%);z-index:50;display:flex;flex-direction:column;padding-left:12px">
        <span style="position:absolute;left:0;top:8px;bottom:8px;width:1px;background:rgba(233,233,237,.12)"></span>
        <span data-lab-sec-dot style="position:absolute;left:0;top:8px;width:1px;height:10px;background:#9184d9;box-shadow:0 0 10px rgba(145,132,217,.7);transition:transform .45s cubic-bezier(.16,1,.3,1)"></span>
        @foreach ($sections as $r)
            <button type="button" data-lab-sec title="{{ $r['name'] }}" class="lab-sec-btn" style="appearance:none;background:none;border:0;cursor:pointer;display:flex;align-items:center;height:26px;padding:0;color:{{ $loop->first ? '#e2ddd1' : 'rgba(226,221,209,.40)' }};font-family:inherit;font-size:10px;letter-spacing:.20em;text-transform:uppercase;transition:color .3s">
                <span>{{ $r['n'] }}</span>
            </button>
        @endforeach
    </nav>

    <div style="position:fixed;left:0;bottom:0;height:1px;width:100%;background:rgba(233,233,237,.10);z-index:50">
        <span data-sa-progress style="display:block;height:100%;width:100%;transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.25),#9184d9);transition:transform .5s cubic-bezier(.16,1,.3,1);transform:scaleX(0.111)"></span>
    </div>

    <main style="position:relative;z-index:1">

        {{-- ==================================================== X · Catalogue --- --}}
        <section class="lab-sec is-active sa-registre" data-sa-scr="0" style="position:relative;min-height:100vh;flex-direction:column">
            <div style="min-height:52vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
                <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">X</span>
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98" data-i18n="lab.title.catalogue">Catalogue</h1>
                <p style="margin:0;max-width:56ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="animation.intro">Neuf animations d'entrée de section, chacune vivant dans <code style="color:#b3a9e6">section-anim.js</code> sous la même interface — <code style="color:#b3a9e6">play</code>, <code style="color:#b3a9e6">reset</code>, <code style="color:#b3a9e6">resize</code>, <code style="color:#b3a9e6">destroy</code> — et n'utilisant que gsap déjà booté par <code style="color:#b3a9e6">portfolio/motion.js</code>. La dixième, Orbite, est différente&nbsp;: c'est le carrefour du site (voir <code style="color:#b3a9e6">/</code>), isolé ici sous la même mécanique d'écran unique.</p>
            </div>

            <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,60px)">
                @foreach (array_slice($sections, 1, count($sections) - 1, true) as $k => $c)
                    <article style="display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55)">
                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                            <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ $c['n'] }}</span>
                            <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)" data-i18n="lab.status">Prototypé</span>
                        </div>
                        <h3 style="margin:0;font-size:clamp(20px,2.2vw,28px);font-weight:200;letter-spacing:-.02em;line-height:1.1">{{ $c['name'] }}</h3>
                        <p style="margin:0;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="{{ $c['i18n'] }}.desc">{{ $c['desc'] }}</p>
                        <div style="display:flex;flex-direction:column;gap:9px;padding-top:14px;border-top:1px solid rgba(233,233,237,.08)">
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.trigger">Déclenche</span><span style="color:rgba(226,221,209,.72)" data-i18n="animation.trigger.fixed">Entrée du bloc dans le viewport (IntersectionObserver).</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.seen">Vu</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.seen">{{ $c['vu'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.tech">Tech</span><span style="color:rgba(226,221,209,.72)">{{ $c['tech'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.cost">Coût</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.cost">{{ $c['cout'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.portfolio">Portfolio</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.portfolio">{{ $c['portfolio'] }}</span></span>
                        </div>
                        <button type="button" class="lab-cta" data-sa-registre-open="{{ $k }}"><span data-i18n="lab.open">Ouvrir</span> {{ $c['n'] }}</button>
                    </article>
                @endforeach
            </div>
            <div style="padding:0 clamp(56px,9vw,180px) clamp(56px,8vh,96px)">
                <span style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">1–9 et 0 ciblent un croquis · R rejoue · flèches parcourent</span>
            </div>
        </section>

        {{-- ==================================================== 01 · Broken Glass --- --}}
        <section class="lab-sec" data-sa-scr="1" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">01</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Broken Glass</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le contour se recolle depuis des fragments dispersés, puis deux fissures se propagent vers l'intérieur.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Vingt fragments SVG posés le long du périmètre, ramenés à leur place avec un décalage pseudo-aléatoire déterministe : le même ordre à chaque lecture.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:linear-gradient(160deg,#0b0c14,#05050a 62%)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 01</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">EXPERIENCE</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Douze projets, deux ans, une seule ligne de conduite.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">SVG · 20 fragments · stagger aléatoire</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 02 · Industrial Scan --- --}}
        <section class="lab-sec" data-sa-scr="2" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">02</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Industrial Scan</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le cadre se déclare arête par arête, se mesure, puis autorise le texte à s'afficher.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Arêtes tiretées, équerres d'angle, graduations et un balayage qui remonte : le relevé s'écrit avant le contenu.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:#05050a">
                    <span style="position:absolute;inset:0;background-image:linear-gradient(rgba(233,233,237,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(233,233,237,.045) 1px,transparent 1px);background-size:64px 64px"></span>
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 02</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">PROCESS</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Cadrage, maquette, intégration, mise en ligne.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">DOM · arêtes tiretées · balayage</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 03 · Organic Reveal --- --}}
        <section class="lab-sec" data-sa-scr="3" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">03</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Organic Reveal</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Une nappe d'encre couvre la scène et s'ouvre depuis un point bas, bord irrégulier et liseré vivant.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Le canvas est gommé en <code style="color:#b3a9e6">destination-out</code> ; le bord est flouté par <code style="color:#b3a9e6">ctx.filter</code> — sans lui, sur Safari ancien, l'ouverture reste nette.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:radial-gradient(70% 60% at 24% 74%,#191a33,#05050a 72%)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 03</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">ARCHIVE</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Tout ce qui a été livré depuis 2023, par ordre d'entrée.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">Canvas · destination-out · blur</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 04 · Glitch Frame --- --}}
        <section class="lab-sec" data-sa-scr="4" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">04</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Glitch Frame</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Sept tranches décalées, un dédoublement chromatique très bref, puis un réalignement sec.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Le plus court de la série : deux salves de décalage, deux calques fantômes teintés, retour à zéro.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:linear-gradient(200deg,#12131d,#05050a 58%)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 04</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">MANIFESTE</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Une page se juge à ce qu'elle laisse de côté.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">DOM · 7 tranches · dédoublement</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 05 · Wireframe --- --}}
        <section class="lab-sec" data-sa-scr="5" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">05</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Wireframe</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Un volume filaire se construit en perspective, puis s'aplatit sur le cadre de la section.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Douze arêtes en CSS 3D, aucune bibliothèque de rendu : la perspective retombe à plat sur le contour.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:linear-gradient(160deg,#05050a,#0b0c14)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 05</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">STRUCTURE</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Une grille, quatre colonnes, aucune exception.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">CSS 3D · 12 arêtes · aplatissement</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 06 · Particle Assembly --- --}}
        <section class="lab-sec" data-sa-scr="6" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">06</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Particle Assembly</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Les particules convergent sur deux cibles : le contour, et les glyphes du titre échantillonnés.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Le titre est rasterisé hors écran puis relu pixel par pixel : changer le texte change le nuage, et un titre très long fait beaucoup de particules.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:#05050a">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 06</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">INDEX</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Chaque entrée porte un numéro, une date, une discipline.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">Canvas · glyphes échantillonnés</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 07 · Shattered Reveal --- --}}
        <section class="lab-sec" data-sa-scr="7" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">07</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Shattered Reveal</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">La section entre déjà fragmentée : huit bandes obliques se réalignent sur leur propre contenu.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Huit clones du contenu, chacun clippé en bande ; une fois alignés, le vrai contenu reprend la main.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:linear-gradient(140deg,#0b0c14,#05050a 55%,#12131d)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 07</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">TERRAIN</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Relevés de site, photographies, notes de chantier.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">DOM · clones clippés · réalignement</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 08 · Liquid Glass --- --}}
        <section class="lab-sec" data-sa-scr="8" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">08</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Liquid Glass</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Une dalle de verre traverse le cadre ; tout ce qu'elle dépasse est révélé derrière elle.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)"><code style="color:#b3a9e6">backdrop-filter</code> pour la matière, <code style="color:#b3a9e6">clip-path</code> pour la révélation : la bande ne masque rien, elle ouvre.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:radial-gradient(80% 70% at 70% 30%,#191a33,#05050a 74%)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 08</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">SEUIL</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">Le passage d'une page à l'autre appartient à la page.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">backdrop-filter · clip-path</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 09 · Chevron Mosaic --- --}}
        <section class="lab-sec" data-sa-scr="9" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:12vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">09</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Chevron Mosaic</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Une maille de triangles suspendue au bord haut : nulle à l'apex central, elle descend vers le milieu de l'écran sur les deux flancs.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Tuilage triangulaire posé en SVG, découpé par un masque en chevron dont la profondeur suit la distance au centre. La densité tombe le long de la diagonale : les triangles se raréfient au lieu de s'arrêter net.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div data-stage style="position:relative;overflow:hidden;height:min(64vh,620px);border:1px solid rgba(233,233,237,.10);border-radius:10px;background:linear-gradient(180deg,#0d0e1d,#05050a 62%)">
                    <div data-anim-content style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:flex-end;gap:14px;padding:clamp(22px,3.4vw,44px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Section 09</span>
                        <h3 data-anim-title style="margin:0;font-size:clamp(28px,3.4vw,48px);font-weight:200;letter-spacing:-.02em;line-height:1">VERSANT</h3>
                        <p style="margin:0;max-width:34ch;font-size:13px;line-height:1.7;color:rgba(226,221,209,.55)">La maille tient le haut du cadre et laisse le texte respirer dessous.</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.32)">SVG · maille triangulaire · masque chevron</span>
                    <button type="button" class="btn btn-primary" data-sa-replay>Rejouer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 10 · Orbite --- --}}
        {{-- Carrefour du site (welcome.blade.php lignes 55-136), isolé tel quel :
             mêmes classes .wl-* (voir lab.css, portées depuis web.css), même
             mécanique JS (voir orbit-lab.js, adapté depuis welcome-lakeust.js) —
             sans le switch FR/EN, qui n'a pas été isolé avec le reste. Le style
             inline force display:flex : .lab-sec.is-active impose display:grid
             (lab.css), qui l'emporterait sur la règle .wl-hero{display:flex} à
             spécificité égale sans lui. --}}

        <section class="lab-sec" data-sa-scr="10" style="flex-direction:column;position:relative;overflow:hidden;min-height:100vh">
            <div class="wl-hero-grid">

                <div class="wl-panels" data-panels>s
                    <div class="wl-panel" data-panel="idle">
                        <div class="label label-accent">
                            <span class="i18n-fr">Point d'entrée</span><span class="i18n-en">Entry point</span>
                        </div>
                        <div class="t-display" style="color:var(--text);">Lakeust Works</div>
                        <div class="wl-panel-divider">
                            <span class="mark"></span>
                            <span class="label" style="letter-spacing:.42em;">
                                <span class="i18n-fr">Deux versants</span><span class="i18n-en">Two sides</span>
                            </span>
                        </div>
                        <p class="t-lead" style="margin:0;max-width:38ch;">
                            <span class="i18n-fr">Un studio, deux domaines. Choisis celui qui t'intéresse&nbsp;: le reste attendra son tour.</span>
                            <span class="i18n-en">One studio, two domains. Pick the one you came for; the other can wait.</span>
                        </p>
                        <div class="label" style="margin-top:var(--s-4);">
                            <span class="i18n-fr">Survole un versant &#8594;</span><span class="i18n-en">Hover a side &#8594;</span>
                        </div>
                    </div>

                    @foreach ($branches as $i => $b)
                        <div class="wl-panel" data-panel="branch">
                            <div class="wl-panel-kicker">
                                <span class="num label-accent">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="label">{{ $b['tag'] }}</span>
                            </div>
                            <h2 class="t-h1">{{ $b['label'] }}</h2>
                            <p class="t-lead" style="margin:0;max-width:36ch;">
                                <span class="i18n-fr">{{ $b['fr'] }}</span><span class="i18n-en">{{ $b['en'] }}</span>
                            </p>
                            <div class="media media-16-9 wl-shot"><div class="media-fill"></div></div>
                            <div style="display:flex;align-items:center;gap:var(--s-4);margin-top:var(--s-2);">
                                @if ($b['open'])
                                    <a class="btn btn-primary" data-node="{{ $i }}" data-open data-label="{{ $b['label'] }}" href="{{ $b['href'] }}">
                                        <span class="i18n-fr">Entrer</span><span class="i18n-en">Enter</span>
                                    </a>
                                @else
                                    <a class="tag tag-accent" data-node="{{ $i }}" data-label="{{ $b['label'] }}" href="{{ $b['href'] }}" style="text-decoration:none;">
                                        <span class="i18n-fr">Ouverture prochaine</span><span class="i18n-en">Opening soon</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="wl-orbit" data-orbit>
                    <div class="wl-orbit-ring wl-orbit-ring-outer"></div>
                    <div class="wl-orbit-ring wl-orbit-ring-inner"></div>

                    <div class="wl-orbit-hub">
                        <span class="wl-orbit-glow"></span>
                        <span class="wl-orbit-dashed"></span>
                        <span class="wl-orbit-core"></span>
                        <span class="wl-orbit-count">{{ str_pad(count($branches), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    @foreach ($branches as $i => $b)
                        <a class="wl-node" data-node="{{ $i }}" @if ($b['open']) data-open @endif data-label="{{ $b['label'] }}" href="{{ $b['href'] }}" aria-label="{{ $b['label'] }}">
                            <span class="wl-node-dot"></span>
                            <span class="wl-node-label">{{ $b['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="wl-exit" data-exit-plate>
                <div class="label label-accent">
                    <span class="i18n-fr">Ouverture</span><span class="i18n-en">Opening</span>
                </div>
                <div class="t-h1" data-exit-name></div>
            </div>
        </section>
    </main>
</div>
@endsection
