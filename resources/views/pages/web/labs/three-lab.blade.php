@extends('layouts.site')
@section('title', 'Three Lab — Lakeust Works')
@section('content')
<head>
    @vite(['resources/css/lab.css', 'resources/js/labs/three/three-lab.js'])

    @php
        /* Les liens ?studio disparaissent au lieu de casser la page si la
           route cinématique correspondante n'existe pas sous ce nom. */
        $scenes = [
            'welcome' => ['n' => '02', 'title' => 'Welcome cinématique', 'studioRoute' => 'blackhole.cinematic', 'studioUrl' => '/blackhole-cinematic'],
            'forest'  => ['n' => '03', 'title' => 'Forêt',               'studioRoute' => 'forest.cinematic',  'studioUrl' => '/forest-cinematic'],
            'orbital' => ['n' => '04', 'title' => 'Orbitale',            'studioRoute' => 'home.cinematic',    'studioUrl' => '/home-cinematic'],
        ];
        $rail = [
            ['n' => 'X',  'title' => 'Catalogue'],
            ['n' => '01', 'title' => 'Trou noir'],
            ['n' => '02', 'title' => 'blackhole cinématique'],
            ['n' => '03', 'title' => 'Forêt'],
            ['n' => '04', 'title' => 'Orbitale'],
            ['n' => '05', 'title' => 'Le seam'],
            ['n' => '06', 'title' => 'Un seul shader'],
            ['n' => '07', 'title' => 'Deux mondes'],
            ['n' => '08', 'title' => 'Caméra sur rail'],
            ['n' => '09', 'title' => 'Budget GPU'],
            ['n' => '10', 'title' => 'Démontage'],
        ];

        /* Catalogue — même gabarit de fiche que Scroll Lab / Barba Lab
           (tag, titre, description, cinq champs, CTA). « Pilote » remplace
           « Scroll » : ici rien ne défile, chaque scène attend qu'on lui
           envoie setShot() (ou qu'elle se pilote elle-même en mode interne). */
        $sections = [
            ['tag' => '01', 'name' => 'Trou noir', 'open' => 1, 'i18n' => 'three.cat.1',
                'desc' => "black-hole-stage dans son mode natif — drive=\"internal\", le défaut. Elle possède son propre scroll, sa propre molette, son propre pavé tactile.",
                'pilote' => "Aucun — l'élément se pilote lui-même ; rien ici ne lui envoie de setShot().",
                'vu' => "Survol pour monter la scène, déplacement de la souris dedans pour orbiter.",
                'tech' => "Même élément que la page d'accueil ; c'est le mode par défaut de black-hole-stage.",
                'cout' => "Faible à intégrer — c'est l'usage prévu par défaut de l'élément.",
                'portfolio' => "Déjà en production sur la page d'accueil."],
            ['tag' => '02', 'name' => 'Blackhole cinématique', 'open' => 2, 'i18n' => 'three.cat.2',
                'desc' => "Le même élément, drive=\"external\" cette fois — scroll/molette/clavier internes coupés, une boucle locale appelle setShot() à sa place.",
                'pilote' => "Boucle locale du lab qui échantillonne un extrait de la vraie timeline (27 s, ×3.4).",
                'vu' => "Curseur de scrub et trois raccourcis de phase (approche, plongée, horizon).",
                'tech' => "Extrait, pas le montage de référence — sept champs sur la vingtaine que le seam accepte.",
                'cout' => "Moyen — écrire un pilote externe demande de connaître les champs attendus par le seam.",
                'portfolio' => "Mode déjà utilisé par /home-cinematic, avec Theatre.js à la place de la boucle locale."],
            ['tag' => '03', 'name' => 'Forêt', 'open' => 3, 'i18n' => 'three.cat.3',
                'desc' => "forest-stage charge le GLB de la route forestière (16 Mo compressés) puis attend setShot() — pas de mode interne, pas d'attribut drive à poser.",
                'pilote' => "Obligatoirement externe — la scène n'a aucun scroll ni molette à elle.",
                'vu' => "Curseur de scrub et raccourcis départ / route / titre.",
                'tech' => "Extrait de 20 s à ×2.5 ; la caméra est accrochée à l'axe de la route (voir Caméra sur rail).",
                'cout' => "Moyen à élevé — 16 Mo à charger avant la première image.",
                'portfolio' => "Déjà en production sur /forest-cinematic."],
            ['tag' => '04', 'name' => 'Orbitale', 'open' => 4, 'i18n' => 'three.cat.4',
                'desc' => "orbital-stage, la plus lourde des trois, deux mondes dans la même scène (voir Deux mondes). Pilotage par hold()/release().",
                'pilote' => "hold() prend la main ; le lab n'appelle jamais release() (voir Démontage).",
                'vu' => "Curseur de scrub et raccourcis orbite / nuages / révélation.",
                'tech' => "Extrait de 46 s à ×3.4 — la plus coûteuse à monter, laisse-lui une fraction de seconde après le survol.",
                'cout' => "Élevé — c'est la scène la plus lourde du site.",
                'portfolio' => "Déjà en production sur /home-cinematic."],
            ['tag' => '05', 'name' => 'Le seam', 'open' => 5, 'i18n' => 'three.cat.5',
                'desc' => "Quatre points de couture entre Three.js et n'importe quel pilote externe — Theatre.js sur les pages cinématiques, la table locale de ce lab, ou une autre timeline demain.",
                'pilote' => "N'importe lequel : aucune scène ne connaît son pilote.",
                'vu' => "Le détail des quatre points de couture (attribut drive, setShot, hold/release, événements).",
                'tech' => "C'est ce qui permet de rejouer les mêmes éléments ici, hors de leur page, sans dupliquer une ligne de rendu.",
                'cout' => "Faible — c'est déjà l'architecture en place, pas un ajout.",
                'portfolio' => "Ce qui rend ce lab possible — documentation d'une frontière, pas un effet à part."],
            ['tag' => '06', 'name' => 'Un seul shader', 'open' => 6, 'i18n' => 'three.cat.6',
                'desc' => "Le trou noir passe par cinq cibles de rendu, mais un seul fragment shader fait le travail optique : lentille gravitationnelle, aberration chromatique, grain, vignettage.",
                'pilote' => "Les uniforms du seam (lens, aberr…), lus à chaque frame.",
                'vu' => "Le rendu final du trou noir, sans rien à manipuler ici.",
                'tech' => "Changer lens ou aberr dans le seam ne recompile rien — ce sont des uniforms.",
                'cout' => "Élevé à écrire une fois, gratuit ensuite : aucun coût de recompilation en usage.",
                'portfolio' => "Déjà en production — c'est le shader réel du trou noir."],
            ['tag' => '07', 'name' => 'Deux mondes', 'open' => 7, 'i18n' => 'three.cat.7',
                'desc' => "La caméra spatiale et la caméra au sol coexistent dans la même scène orbitale ; world — 0 à 1 continu — bascule laquelle est active à 0.5.",
                'pilote' => "L'uniform world, pas un booléen malgré l'air.",
                'vu' => "La bascule se fait sous le voile de nuages (veil plafonne à 80 %, jamais 100 %).",
                'tech' => "La matière continue de bouger pendant que la vue est bouchée : on ne voit plus rien, mais on descend.",
                'cout' => "Élevé — synchroniser deux caméras et un voile qui ne doit jamais atteindre l'opacité totale.",
                'portfolio' => "Déjà en production — c'est la bascule espace → sol de /home-cinematic."],
            ['tag' => '08', 'name' => 'Caméra sur rail', 'open' => 8, 'i18n' => 'three.cat.8',
                'desc' => "La forêt n'a pas de caméra à six degrés de liberté : elle en a cinq, tous relatifs à la route. s — 0 à 1 — est la seule position réelle.",
                'pilote' => "s, la progression le long de la route ; le reste ne fait que cadrer depuis ce point.",
                'vu' => "Le détail des cinq degrés relatifs, sans manipulation directe ici.",
                'tech' => "Contrainte plutôt que liberté : la caméra ne peut pas quitter l'axe de la route.",
                'cout' => "Faible — une seule valeur à piloter au lieu de six.",
                'portfolio' => "Déjà en production — c'est la caméra réelle de /forest-cinematic."],
            ['tag' => '09', 'name' => 'Budget GPU', 'open' => 9, 'i18n' => 'three.cat.9',
                'desc' => "Trois économies reviennent dans les trois scènes : l'instancing pour tout ce qui se répète, le travail poussé dans le vertex shader plutôt que le fragment, le blending additif pour la lumière plutôt que des lampes réelles.",
                'pilote' => "Le palier LOW, qui resserre les trois budgets d'un cran plutôt que de couper des effets entiers.",
                'vu' => "Une explication, sans scène à manipuler ici.",
                'tech' => "C'est pourquoi LOW existe : sur mobile, un resserrement plutôt qu'une coupe.",
                'cout' => "Faible à appliquer une fois la discipline posée — coûteux à ajouter après coup sur une scène déjà écrite autrement.",
                'portfolio' => "Déjà en production dans les trois stages du site."],
            ['tag' => '10', 'name' => 'Démontage', 'open' => 10, 'i18n' => 'three.cat.10',
                'desc' => "Le compteur de l'en-tête ne dépasse jamais 1 — quatre garde-fous s'en assurent, en plus du survol lui-même : sortie d'écran, onglet caché, sortie de page.",
                'pilote' => "Le survol/tactile — un appui épingle la plaque, un second la relâche.",
                'vu' => "Une démonstration des garde-fous, sans scène à manipuler ici.",
                'tech' => "L'orbitale ne rend jamais la main à sa caméra libre dans ce lab — release() n'est jamais appelé, le lab garde le pilotage jusqu'au démontage.",
                'cout' => "Élevé si oublié : un navigateur plafonne les contextes WebGL et abandonne les plus anciens sans prévenir.",
                'portfolio' => "La règle qui permet à ce lab d'exister sans planter — un seul contexte WebGL vivant à la fois."],
        ];
    @endphp
</head>

<div data-tl-root style="position:relative;min-height:100vh;background:#05050a">

    <nav style="position:fixed;left:clamp(10px,2.4vw,26px);top:50%;transform:translateY(-50%);z-index:50;display:flex;flex-direction:column;padding-left:12px">
        <span style="position:absolute;left:0;top:8px;bottom:8px;width:1px;background:rgba(233,233,237,.12)"></span>
        <span data-lab-sec-dot style="position:absolute;left:0;top:8px;width:1px;height:10px;background:#9184d9;box-shadow:0 0 10px rgba(145,132,217,.7);transition:transform .45s cubic-bezier(.16,1,.3,1)"></span>
        @foreach ($rail as $i => $r)
            <button type="button" data-lab-sec="{{ $i }}" title="{{ $r['title'] }}" class="lab-sec-btn" style="appearance:none;background:none;border:0;cursor:pointer;display:flex;align-items:center;height:26px;padding:0;color:rgba(226,221,209,.40);font-family:inherit;font-size:10px;letter-spacing:.20em;text-transform:uppercase;transition:color .3s">
                <span>{{ $r['n'] }}</span>
            </button>
        @endforeach
    </nav>

    <div style="position:fixed;left:0;bottom:0;height:1px;width:100%;background:rgba(233,233,237,.10);z-index:50">
        <span data-tl-progress style="display:block;height:100%;width:100%;transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.25),#9184d9);transition:transform .5s cubic-bezier(.16,1,.3,1);transform:scaleX(0.1)"></span>
    </div>

    <main style="position:relative;z-index:1">

        {{-- ==================================================== 11 · Catalogue --- --}}
        <section class="lab-sec is-active tl-catalogue" data-tl-scr="0" data-tl-label="Catalogue" style="position:relative;min-height:100vh">
            <div style="min-height:52vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98" data-i18n="lab.title.catalogue">Catalogue</h1>
                <p style="margin:0;max-width:56ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="three.intro">Dix écrans autour des trois stages Three.js du site — quatre montent une vraie scène WebGL au survol (01 à 04), six documentent l'architecture qui les rend interchangeables (05 à 10).</p>
            </div>

            <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));padding:0 clamp(56px,9vw,180px) clamp(60px,8vw,120px)">
                @foreach ($sections as $c)
                    <article style="display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55)">
                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                            <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ $c['tag'] }}</span>
                            <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)" data-i18n="lab.status">Prototypé</span>
                        </div>
                        <h3 style="margin:0;font-size:clamp(20px,2.2vw,28px);font-weight:200;letter-spacing:-.02em;line-height:1.1">{{ $c['name'] }}</h3>
                        <p style="margin:0;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="{{ $c['i18n'] }}.desc">{{ $c['desc'] }}</p>
                        <div style="display:flex;flex-direction:column;gap:9px;padding-top:14px;border-top:1px solid rgba(233,233,237,.08)">
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.pilot">Pilote</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.pilot">{{ $c['pilote'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.seen">Vu</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.seen">{{ $c['vu'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.tech">Tech</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.tech">{{ $c['tech'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.cost">Coût</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.cost">{{ $c['cout'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.portfolio">Portfolio</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.portfolio">{{ $c['portfolio'] }}</span></span>
                        </div>
                        <button type="button" class="lab-cta" data-tl-cta="{{ $c['open'] }}"><span data-i18n="lab.open">Ouvrir</span> {{ $c['tag'] }}</button>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ==================================================== 01 · Trou noir --- --}}
        <section class="lab-sec" data-tl-scr="1" data-tl-label="Trou noir" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">01</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Trou noir</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)"><code style="color:#b3a9e6">&lt;black-hole-stage&gt;</code> dans son mode natif — <code style="color:#b3a9e6">drive="internal"</code>, le défaut. Elle possède son propre scroll, sa propre molette, son propre pavé tactile ; rien ici ne lui envoie de <code style="color:#b3a9e6">setShot()</code>.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">C'est le même élément que la page d'accueil. Survole la plaque pour la monter, déplace la souris dedans pour orbiter.</p>
            </div>

            <div data-tl-card data-tl-key="blackhole" data-tl-slug="interactive">
                <div data-tl-plate data-tl-state="idle">
                    <div data-tl-idle>
                        <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Trou noir</span>
                        <span data-tl-state-label>Survoler pour monter la scène</span>
                    </div>
                </div>
                <div data-tl-hud>
                    <span data-tl-hud="fps">— fps</span>
                    <span data-tl-hud="time">—</span>
                    <span data-tl-hud="phase">—</span>
                    <button type="button" data-tl-quality>LOW</button>
                    <span style="margin-left:auto;font-size:9px;color:rgba(226,221,209,.3)">drive: internal</span>
                </div>
            </div>
        </section>

        {{-- ==================================================== 02 · blackhole cinématique --- --}}
        <section class="lab-sec" data-tl-scr="2" data-tl-label="blackhole cinématique" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">02</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Blackhole cinématique</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le même élément, <code style="color:#b3a9e6">drive="external"</code> cette fois — le scroll/molette/clavier internes sont coupés, une boucle locale échantillonne un extrait de la vraie timeline (27 s) et appelle <code style="color:#b3a9e6">setShot()</code> à sa place.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Extrait, pas le montage de référence — sept champs sur la vingtaine que le seam accepte. Le lab joue à ×3.4 pour qu'un survol couvre la pièce.</p>
            </div>

            <div data-tl-card data-tl-key="blackhole" data-tl-slug="welcome">
                <div data-tl-plate data-tl-state="idle">
                    <div data-tl-idle>
                        <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Welcome — 27 s</span>
                        <span data-tl-state-label>Survoler pour monter la scène</span>
                    </div>
                </div>
                <div data-tl-hud>
                    <span data-tl-hud="fps">— fps</span>
                    <span data-tl-hud="time">0.0 s</span>
                    <span data-tl-hud="phase">—</span>
                    <input type="range" min="0" max="100" value="0" data-tl-scrub aria-label="position dans la timeline">
                    <button type="button" data-tl-quality>LOW</button>
                    @if (Route::has($scenes['welcome']['studioRoute']))
                        <a href="{{ $scenes['welcome']['studioUrl'] }}?studio" target="_blank" rel="noopener">?studio</a>
                    @endif
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <button type="button" data-tl-phase-jump="0" class="btn">approche</button>
                    <button type="button" data-tl-phase-jump="16" class="btn">plongée</button>
                    <button type="button" data-tl-phase-jump="22" class="btn">horizon</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 03 · Forêt --- --}}
        <section class="lab-sec" data-tl-scr="3" data-tl-label="Forêt" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">03</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Forêt</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)"><code style="color:#b3a9e6">&lt;forest-stage&gt;</code> charge le GLB de la route forestière (16 Mo compressés) puis attend <code style="color:#b3a9e6">setShot()</code> — cet élément n'a pas de mode interne du tout, ni d'attribut <code style="color:#b3a9e6">drive</code> à poser.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Extrait de 20 s à ×2.5. La caméra ne vole pas librement : elle est accrochée à l'axe de la route — voir l'écran 08.</p>
            </div>

            <div data-tl-card data-tl-key="forest" data-tl-slug="forest">
                <div data-tl-plate data-tl-state="idle">
                    <div data-tl-idle>
                        <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Forêt — 20 s</span>
                        <span data-tl-state-label>Survoler pour charger le GLB</span>
                    </div>
                </div>
                <div data-tl-hud>
                    <span data-tl-hud="fps">— fps</span>
                    <span data-tl-hud="time">0.0 s</span>
                    <span data-tl-hud="phase">—</span>
                    <input type="range" min="0" max="100" value="0" data-tl-scrub aria-label="position dans la timeline">
                    <button type="button" data-tl-quality>LOW</button>
                    @if (Route::has($scenes['forest']['studioRoute']))
                        <a href="{{ $scenes['forest']['studioUrl'] }}?studio" target="_blank" rel="noopener">?studio</a>
                    @endif
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <button type="button" data-tl-phase-jump="0" class="btn">départ</button>
                    <button type="button" data-tl-phase-jump="8" class="btn">route</button>
                    <button type="button" data-tl-phase-jump="16.4" class="btn">titre</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 04 · Orbitale --- --}}
        <section class="lab-sec" data-tl-scr="4" data-tl-label="Orbitale" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">04</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Orbitale</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)"><code style="color:#b3a9e6">&lt;orbital-stage&gt;</code> — la plus lourde des trois, deux mondes dans la même scène (voir l'écran 07). Pas d'attribut <code style="color:#b3a9e6">drive</code> ici : le pilotage se règle avec <code style="color:#b3a9e6">hold()</code>/<code style="color:#b3a9e6">release()</code>, et le lab n'appelle jamais <code style="color:#b3a9e6">release()</code> — voir l'écran 10.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Extrait de 46 s à ×3.4. C'est la plus coûteuse à monter — laisse-lui une fraction de seconde après le survol.</p>
            </div>

            <div data-tl-card data-tl-key="orbital" data-tl-slug="orbital">
                <div data-tl-plate data-tl-state="idle">
                    <div data-tl-idle>
                        <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Orbitale — 46 s</span>
                        <span data-tl-state-label>Survoler pour monter la scène</span>
                    </div>
                </div>
                <div data-tl-hud>
                    <span data-tl-hud="fps">— fps</span>
                    <span data-tl-hud="time">0.0 s</span>
                    <span data-tl-hud="phase">—</span>
                    <input type="range" min="0" max="100" value="0" data-tl-scrub aria-label="position dans la timeline">
                    <button type="button" data-tl-quality>LOW</button>
                    @if (Route::has($scenes['orbital']['studioRoute']))
                        <a href="{{ $scenes['orbital']['studioUrl'] }}?studio" target="_blank" rel="noopener">?studio</a>
                    @endif
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <button type="button" data-tl-phase-jump="0" class="btn">orbite</button>
                    <button type="button" data-tl-phase-jump="35.5" class="btn">nuages</button>
                    <button type="button" data-tl-phase-jump="39.5" class="btn">révélation</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 05 · Le seam --- --}}
        <section class="lab-sec" data-tl-scr="5" data-tl-label="Le seam" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">05</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Le seam</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Quatre points de couture entre Three.js et n'importe quel pilote externe — Theatre.js sur les pages cinématiques, la table locale de ce lab, ou une autre timeline demain.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Aucune scène ne connaît son pilote. C'est ce qui permet de rejouer les mêmes éléments ici, hors de leur page, sans dupliquer une ligne de rendu.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:0;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14">
                <div style="display:flex;gap:14px;padding:16px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                    <code style="color:#9184d9;width:110px;flex:none">setShot(o)</code>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">fusionne <code>o</code> dans l'état interne — aucune validation, aucun clamp ; c'est au pilote de rester dans les bornes.</span>
                </div>
                <div style="display:flex;gap:14px;padding:16px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                    <code style="color:#9184d9;width:110px;flex:none">shot()</code>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">relit l'état — copie sur le trou noir et la forêt, référence vivante sur l'orbitale : la muter la modifie directement.</span>
                </div>
                <div style="display:flex;gap:14px;padding:16px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                    <code style="color:#9184d9;width:110px;flex:none">release()</code>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">orbitale seulement — rend la main au pointeur, allume les points d'intérêt projetés.</span>
                </div>
                <div style="display:flex;gap:14px;padding:16px 18px">
                    <code style="color:#9184d9;width:110px;flex:none">hold()</code>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">reprend la main — un rejeu ne doit pas garder le mode interactif de la lecture précédente.</span>
                </div>
            </div>
        </section>

        {{-- ==================================================== 06 · Un seul shader --- --}}
        <section class="lab-sec" data-tl-scr="6" data-tl-label="Un seul shader" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">06</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Un seul shader</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le trou noir passe par cinq cibles de rendu — scène, disque brillant, deux passes de flou, composite — mais un seul fragment shader fait le travail optique : la lentille gravitationnelle, l'aberration chromatique, le grain, le vignettage.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Changer <code style="color:#b3a9e6">lens</code> ou <code style="color:#b3a9e6">aberr</code> dans le seam ne recompile rien : ce sont des uniforms, lus à chaque frame.</p>
            </div>
            <div style="display:flex;align-items:center;justify-content:center;gap:0;flex-wrap:wrap;padding:32px;border:1px solid rgba(233,233,237,.10);border-radius:10px;background:#0b0c14">
                @foreach (['Scène', 'Disque', 'Flou A', 'Flou B', 'Composite'] as $k => $stage)
                    <div style="display:flex;align-items:center">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;width:96px">
                            <div style="width:56px;height:56px;border-radius:8px;border:1px solid {{ $k === 4 ? 'rgba(145,132,217,.6)' : 'rgba(233,233,237,.14)' }};background:{{ $k === 4 ? 'rgba(145,132,217,.14)' : 'rgba(233,233,237,.03)' }}"></div>
                            <span style="font-size:9px;letter-spacing:.16em;text-transform:uppercase;text-align:center;color:{{ $k === 4 ? '#b3a9e6' : 'rgba(226,221,209,.4)' }}">{{ $stage }}</span>
                        </div>
                        @if ($k < 4)
                            <span style="width:20px;height:1px;background:rgba(233,233,237,.2);margin:0 -2px 22px">→</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ==================================================== 07 · Deux mondes --- --}}
        <section class="lab-sec" data-tl-scr="7" data-tl-label="Deux mondes" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">07</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Deux mondes</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">La caméra spatiale et la caméra au sol coexistent dans la même scène orbitale. <code style="color:#b3a9e6">world</code> — 0 à 1 continu — bascule laquelle est active à <code style="color:#b3a9e6">0.5</code> ; ce n'est pas un booléen malgré l'air.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">La bascule se fait sous le voile de nuages : <code style="color:#b3a9e6">veil</code> plafonne à 80 % — jamais 100 % — pour que la matière continue de bouger pendant que la vue est bouchée. On ne voit plus rien, mais on descend.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:16px">
                <div style="position:relative;height:64px;border-radius:32px;background:linear-gradient(90deg,#12131d,#191a33 50%,#0b0c14);border:1px solid rgba(233,233,237,.10);overflow:hidden">
                    <span style="position:absolute;left:0;top:0;bottom:0;width:50%;display:flex;align-items:center;justify-content:center;font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.5)">world = 0 · espace</span>
                    <span style="position:absolute;right:0;top:0;bottom:0;width:50%;display:flex;align-items:center;justify-content:center;font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.5)">world = 1 · sol</span>
                    <span style="position:absolute;left:50%;top:6px;bottom:6px;width:2px;background:#9184d9;box-shadow:0 0 10px rgba(145,132,217,.7);transform:translateX(-50%)"></span>
                </div>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.45)">Basculer un booléen ferait un saut. Un <code style="color:#b3a9e6">world</code> continu laisse la caméra spatiale et la caméra au sol se raccorder sous 80 % de voile, où l'œil ne peut de toute façon rien distinguer.</p>
            </div>
        </section>

        {{-- ==================================================== 08 · Caméra sur rail --- --}}
        <section class="lab-sec" data-tl-scr="8" data-tl-label="Caméra sur rail" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">08</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Caméra sur rail</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">La forêt n'a pas de caméra à six degrés de liberté : elle en a cinq, tous relatifs à la route. <code style="color:#b3a9e6">s</code> — 0 à 1 — est la seule position réelle ; le reste ne fait que cadrer depuis ce point.</p>
            </div>
            <div style="display:flex;flex-direction:column;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14">
                @foreach ([
                    ['s', 'position le long de l\'arc de la route, 0 → 1'],
                    ['lift', 'hauteur de la caméra au-dessus du sol'],
                    ['side', 'déport dans la largeur du chemin'],
                    ['ahead', 'distance devant, où porte le regard'],
                    ['aim', 'relèvement du regard, indépendant de lift'],
                ] as $field)
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                        <code style="color:#9184d9;width:64px;flex:none">{{ $field[0] }}</code>
                        <span style="font-size:13px;color:rgba(226,221,209,.6)">{{ $field[1] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ==================================================== 09 · Budget GPU --- --}}
        <section class="lab-sec" data-tl-scr="9" data-tl-label="Budget GPU" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">09</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Budget GPU</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Trois économies reviennent dans les trois scènes : l'instancing pour tout ce qui se répète, le travail poussé dans le vertex shader plutôt que le fragment, le blending additif pour la lumière plutôt que des lampes réelles.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">C'est aussi pourquoi le palier LOW existe : sur mobile, ces trois budgets se resserrent d'un cran plutôt que de couper des effets entiers.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:0;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14">
                <div style="display:flex;gap:14px;padding:16px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                    <span style="width:110px;flex:none;font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#9184d9">Instancing</span>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">étoiles, particules, végétation répétée — un seul appel de dessin pour des milliers d'occurrences.</span>
                </div>
                <div style="display:flex;gap:14px;padding:16px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                    <span style="width:110px;flex:none;font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#9184d9">Vertex shader</span>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">déplacement, dérive, respiration — calculés une fois par sommet, jamais par pixel.</span>
                </div>
                <div style="display:flex;gap:14px;padding:16px 18px">
                    <span style="width:110px;flex:none;font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#9184d9">Additifs</span>
                    <span style="font-size:13px;color:rgba(226,221,209,.6)">lueurs, traînées, fenêtres éclairées — une couleur ajoutée au rendu, pas une lumière de plus à calculer.</span>
                </div>
            </div>
        </section>

        {{-- ==================================================== 10 · Démontage --- --}}
        <section class="lab-sec" data-tl-scr="10" data-tl-label="Démontage" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">10</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Démontage</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le compteur de l'en-tête ne dépasse jamais 1 — quatre garde-fous s'en assurent, en plus du survol lui-même : sortie d'écran, onglet caché, sortie de page.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Le survol seul ne suffit pas au tactile : un appui épingle la plaque, un second la relâche. Et l'orbitale ne rend jamais la main à sa caméra libre ici — <code style="color:#b3a9e6">release()</code> n'est jamais appelé, le lab garde le pilotage jusqu'au démontage.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:16px">
                <div style="display:flex;align-items:baseline;gap:14px;padding:20px 22px;border:1px solid rgba(145,132,217,.28);border-radius:10px;background:rgba(145,132,217,.06)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.44)">Contextes vivants</span>
                    <span data-tl-live-count style="font-size:clamp(28px,4vw,40px);font-weight:200;color:#e2ddd1;font-variant-numeric:tabular-nums">0</span>
                    <span style="margin-left:auto;font-family:ui-monospace,monospace;font-size:11px;color:rgba(226,221,209,.42)">/ 1 — jamais plus</span>
                </div>
                <div style="display:flex;flex-direction:column;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14">
                    @foreach ([
                        ['Sortie de plaque', 'pointerleave, 320 ms de grâce puis remove() — un aller-retour rapide de la souris ne redémarre rien'],
                        ['Sortie d\'écran', 'IntersectionObserver — la scène quitte le viewport, démontage immédiat'],
                        ['Onglet caché', 'visibilitychange — l\'onglet passe en arrière-plan, démontage immédiat'],
                        ['Sortie de page', 'pagehide — la dernière scène ne survit jamais à la navigation'],
                    ] as $g)
                        <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid rgba(233,233,237,.06)">
                            <span style="flex:none;width:6px;height:6px;border-radius:50%;background:#9184d9"></span>
                            <span style="width:120px;flex:none;font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:rgba(226,221,209,.6)">{{ $g[0] }}</span>
                            <span style="font-size:12px;line-height:1.6;color:rgba(226,221,209,.45)">{{ $g[1] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
</div>
@endsection
