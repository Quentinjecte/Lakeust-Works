<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scroll Lab — Lakeust Works</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/lab.css', 'resources/js/lab.js'])
    @else
        <style>body{background:#05050a;color:#e2ddd1;font-family:system-ui,sans-serif;padding:40px}</style>
    @endif

    @php
        /* Ordre de navigation (clavier / sommaire) : Catalogue d'abord, puis
           les quinze mécaniques. Doit rester synchronisé avec ORDER dans lab.js. */
        $labs = [
            'Panneaux · scroll snap', 'Profondeur · parallaxe', 'Progression · scroll-driven', 'Récit · section épinglée',
            'Flux · scroll infini', 'Révélation · masques', 'Chambre orbitale · contrôleur orbital', 'Champ polarisé · vitesse et signe',
            'Corridor · traversée spatiale', 'Démontage · déconstruction', 'Quadrants · lois contrariées', 'Morphogenèse · interpolation de formes',
            'Entrelacs · tressage en profondeur', 'Repli · onde triangulaire', 'Focale verticale · fisheye de lecture', 'Catalogue · quinze mécaniques',
        ];
        $order = [15, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
        $rail = array_map(fn ($i) => [
            'n' => $i === 15 ? 'IX' : str_pad($i + 1, 2, '0', STR_PAD_LEFT),
            'title' => explode(' · ', $labs[$i])[0],
        ], $order);

        $concepts = [
            ['tag' => 'Ω · 07', 'name' => 'Chambre orbitale', 'built' => 6, 'status' => 'Prototypé',
                'desc' => "Les fiches d'un ensemble ne sont plus une grille mais une orbite. Le scroll fait arriver les objets sur l'anneau, bascule le plan de l'orbite vers l'horizontale, puis effondre l'anneau en liste lisible.",
                'scroll' => 'Angle, rayon, inclinaison du plan et effondrement — quatre paramètres lus dans la même progression.',
                'seen' => "Neuf fiches tournant autour d'un noyau lumineux, celles du fond floues et petites, qui finissent alignées en colonne.",
                'tech' => 'GSAP ScrollTrigger épinglé + trigonométrie par frame, aucune 3D.',
                'cost' => 'Moyen — la difficulté est le passage anneau → liste sans saut.',
                'use' => 'Fort : page « projets » ou « services » qui se range toute seule.'],
            ['tag' => 'Ω · 08', 'name' => 'Champ polarisé', 'built' => 7, 'status' => 'Prototypé',
                'desc' => "Le scroll n'est plus une position mais une force. Descendre attire la matière vers un noyau, remonter la repousse, s'arrêter laisse le champ se détendre lentement.",
                'scroll' => 'Vitesse et signe du scroll → polarité et amplitude du champ ; la position ne sert qu\'à déplacer le noyau.',
                'seen' => '135 nœuds qui se resserrent ou explosent selon le geste, et un anneau qui pulse avec la vitesse.',
                'tech' => 'Ticker GSAP + ressort simple, vitesse fournie par Lenis.',
                'cost' => 'Moyen — surveiller le coût par frame et le nombre de nœuds.',
                'use' => 'Moyen : superbe en page d\'accueil, à réserver au desktop.'],
            ['tag' => 'Ω · 09', 'name' => 'Corridor', 'built' => 8, 'status' => 'Prototypé',
                'desc' => 'La page ne défile pas : la caméra avance. Les plans viennent du fond, grossissent, passent derrière l\'observateur et disparaissent.',
                'scroll' => 'Progression → position Z de la caméra ; chaque plan garde sa profondeur propre.',
                'seen' => 'Une traversée : des plaques de contenu qui défilent de part et d\'autre du regard, avec un léger roulis.',
                'tech' => 'CSS 3D (perspective + translateZ), ScrollTrigger épinglé.',
                'cost' => 'Faible à moyen — attention au nombre de plans composités.',
                'use' => 'Fort : introduction de portfolio, ou traversée d\'un projet.'],
            ['tag' => 'Ω · 10', 'name' => 'Démontage', 'built' => 9, 'status' => 'Prototypé',
                'desc' => 'Aucune section n\'apparaît : la précédente est démontée. Lettres dispersées, lignes détachées, plaques envoyées vers leurs points de fuite — la suivante se réassemble depuis ces trajectoires.',
                'scroll' => 'Progression → avancement du démontage puis de la reconstruction, les deux se chevauchant.',
                'seen' => 'Un titre qui explose caractère par caractère pendant que le titre suivant se recompose.',
                'tech' => 'Découpe de caractères maison + timeline scrubbée, vecteurs déclarés en HTML.',
                'cost' => 'Moyen — il faut choisir les vecteurs à la main pour éviter le bruit.',
                'use' => 'Fort : transition entre deux chapitres d\'un même projet.'],
            ['tag' => 'Ω · 11', 'name' => 'Quadrants contrariés', 'built' => 10, 'status' => 'Prototypé',
                'desc' => 'Une seule section, quatre lois de mouvement. Deux zones suivent le scroll à des vitesses différentes, une le contredit, la dernière le traduit en échelle et rotation.',
                'scroll' => 'Une même progression, quatre coefficients — dont un négatif.',
                'seen' => 'Quatre fenêtres dont les contenus glissent dans des sens différents autour d\'un centre immobile.',
                'tech' => 'ScrollTrigger scrubbé par zone, overflow hidden.',
                'cost' => 'Faible — le plus simple des six.',
                'use' => 'Très fort : grille de projets à quatre entrées.'],
            ['tag' => 'Ω · 12', 'name' => 'Morphogenèse', 'built' => 11, 'status' => 'Prototypé',
                'desc' => 'Une surface unique change de forme au fil du scroll : rectangle, hexagone, cercle, plaque. Le contenu se révèle pendant la transformation.',
                'scroll' => 'Progression → curseur dans une séquence de formes, interpolation continue entre deux voisines.',
                'seen' => 'Une forme qui se déforme sans à-coup, un mot par état, une valeur d\'interpolation.',
                'tech' => 'Échantillonnage angulaire des formes (48 points) + clip-path recalculé par frame.',
                'cost' => 'Moyen — la partie mathématique est écrite une fois pour toutes.',
                'use' => 'Fort : révélation d\'une image de couverture.'],
            ['tag' => 'Ω · 13', 'name' => 'Entrelacs', 'built' => 12, 'status' => 'Prototypé',
                'desc' => "Trois brins de contenu se tressent : chacun passe devant les autres puis repasse derrière, sans qu'aucune règle de superposition ne soit écrite à la main.",
                'scroll' => "Progression → angle de phase ; le sinus donne la position latérale, le cosinus la profondeur.",
                'seen' => "Trois colonnes qui se croisent, celle du fond floue et rétrécie, l'ordre changeant à chaque demi-tour.",
                'tech' => "Trigonométrie par frame + z-index, flou et échelle dérivés du même cosinus.",
                'cost' => "Faible — une seule formule, aucun état à conserver.",
                'use' => "Fort : trois références mises en avant sur une page d'accueil."],
            ['tag' => "Ω · 14", 'name' => "Repli", 'built' => 13, 'status' => "Prototypé",
                'desc' => "Le scroll est replié en onde triangulaire : passé la moitié de la section, continuer à descendre rejoue la scène à l'envers. La page avance, le contenu recule.",
                'scroll' => "Progression → onde triangulaire ; le signe de la pente change le sol et le sens des entrées.",
                'seen' => "Quatre états qui se succèdent, puis les mêmes en marche arrière sur fond indigo avec un reflet inversé.",
                'tech' => "Une conversion de progression, une timeline scrubbée, aucune duplication de contenu.",
                'cost' => "Faible — le risque est la lisibilité, pas la performance.",
                'use' => "Moyen : conclusion d'un chapitre, ou page manifeste."],
            ['tag' => "Ω · 15", 'name' => "Focale verticale", 'built' => 14, 'status' => "Prototypé",
                'desc' => "Rien ne défile. Le document entier tient dans l'écran et le scroll ne déplace que le point de lecture : la bande visée s'ouvre, les autres se compriment en filets.",
                'scroll' => "Progression → position de la focale ; la hauteur de chaque bande est une gaussienne de sa distance à ce point.",
                'seen' => "Sept bandes empilées dont une seule est ouverte et lisible, les voisines à demi ouvertes.",
                'tech' => "Hauteurs normalisées sur la somme des poids, jamais mesurées sur le rendu — pas de rétroaction.",
                'cost' => "Faible à moyen — attention au reflow, les hauteurs seules changent.",
                'use' => "Très fort : sommaire, index de projets, ou page de services."],
            ['tag' => 'Ω · 16', 'name' => 'Mémoire inverse', 'built' => null, 'status' => 'Fiche',
                'desc' => 'Le scroll charge une valeur qui se décharge dès qu\'on s\'arrête. Rester immobile rembobine la scène : pour maintenir un état, il faut continuer à scroller.',
                'scroll' => 'Intégration de la vitesse avec fuite : la progression n\'est plus liée à la position de la page.',
                'seen' => 'Une scène qui avance tant qu\'on pousse et revient doucement en arrière dès qu\'on lâche.',
                'tech' => 'Vitesse Lenis + intégrateur à décroissance, rendu par ticker.',
                'cost' => 'Moyen — risque de désorientation, exige un indicateur clair.',
                'use' => 'Moyen : une seule scène manifeste, pas une page entière.'],
            ['tag' => 'Ω · 17', 'name' => 'Gravité réglable', 'built' => null, 'status' => 'Fiche',
                'desc' => 'Les blocs de contenu sont soumis à une gravité dont le scroll règle l\'intensité et la direction : ils tombent, s\'empilent, rebondissent, puis se remettent en orbite quand la gravité s\'annule.',
                'scroll' => 'Progression → vecteur de gravité (intensité et angle).',
                'seen' => 'Des cartes qui s\'empilent au bas de l\'écran puis remontent en flottant.',
                'tech' => 'Intégration Verlet maison (une vingtaine de corps), collisions AABB simplifiées.',
                'cost' => 'Élevé — stabilité de la simulation et reprise après retour arrière.',
                'use' => 'Moyen : page « à propos » ludique.'],
            ['tag' => 'Ω · 18', 'name' => 'Sédiments', 'built' => null, 'status' => 'Fiche',
                'desc' => 'Chaque section traversée laisse une strate empilée en bas de l\'écran : une coupe géologique de la lecture. Remonter creuse les strates et rouvre la section correspondante.',
                'scroll' => 'Sens du scroll → dépôt ou érosion ; hauteur cumulée des strates = position dans le document.',
                'seen' => 'Une barre stratifiée qui grandit, chaque strate portant le titre de sa section.',
                'tech' => 'ScrollTrigger par section + rendu de la pile en DOM.',
                'cost' => 'Faible à moyen.',
                'use' => 'Très fort : remplace la barre de progression par un objet mémorable.'],
            ['tag' => 'Ω · 19', 'name' => 'Fréquence', 'built' => null, 'status' => 'Fiche',
                'desc' => 'Le scroll est traité comme une fréquence d\'échantillonnage. Au-delà d\'un seuil de vitesse la typographie se dédouble et se déchire ; en dessous elle se stabilise. Lire vite abîme l\'image.',
                'scroll' => 'Vitesse → seuil de déformation ; l\'arrêt restaure la netteté.',
                'seen' => 'Un titre qui se dédouble en fantômes colorés quand on accélère.',
                'tech' => 'Shader GLSL sur un rendu de texte, ou empilement de couches CSS décalées.',
                'cost' => 'Élevé en GLSL, moyen en CSS.',
                'use' => 'Moyen : accroche de page d\'accueil.'],
            ['tag' => 'Ω · 20', 'name' => 'Scroll désynchronisé', 'built' => null, 'status' => 'Fiche',
                'desc' => 'Deux colonnes, deux temporalités : l\'une suit le scroll, l\'autre le rattrape avec retard élastique. Le texte et l\'image ne coïncident qu\'à l\'arrêt — le sens se forme quand on s\'immobilise.',
                'scroll' => 'Une colonne en position directe, l\'autre en ressort amorti sur la même valeur.',
                'seen' => 'Une image qui court derrière son texte puis le rejoint exactement.',
                'tech' => 'Deux interpolations, une seule source de scroll.',
                'cost' => 'Faible.',
                'use' => 'Très fort : page projet éditoriale, très adaptable.'],
            ['tag' => 'Ω · 21', 'name' => 'Plan de netteté', 'built' => null, 'status' => 'Fiche',
                'desc' => 'Le scroll ne déplace pas la page mais le plan de mise au point dans une pile d\'images superposées. On scrolle dans la profondeur de champ.',
                'scroll' => 'Progression → position du plan net ; le flou de chaque couche dépend de sa distance à ce plan.',
                'seen' => 'Une pile d\'images où une seule est nette à la fois, les autres respirant autour.',
                'tech' => 'filter: blur() par couche, ou passe GLSL pour un rendu propre.',
                'cost' => 'Moyen — le flou CSS coûte cher, à limiter en nombre de couches.',
                'use' => 'Fort : galerie photo ou séquence de références.'],
        ];
    @endphp
</head>
<body>

<div data-lab-root style="position:relative;min-height:100vh;background:#05050a">

    <header style="position:fixed;top:0;left:0;right:0;z-index:50;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px clamp(16px,4vw,40px);pointer-events:none;mix-blend-mode:difference">
        <div style="display:flex;align-items:baseline;gap:12px">
            <a href="{{ route('welcome') }}" style="pointer-events:auto;font-size:11px;letter-spacing:.30em;text-transform:uppercase;color:#e2ddd1">Scroll Lab</a>
            <span data-lab-title style="font-size:11px;letter-spacing:.18em;color:rgba(226,221,209,.45)">Catalogue · quinze mécaniques</span>
        </div>
        <span data-lab-count style="font-size:11px;letter-spacing:.22em;color:rgba(226,221,209,.45)">IX / 15</span>
    </header>

    <nav style="position:fixed;left:clamp(10px,2.4vw,26px);top:50%;transform:translateY(-50%);z-index:50;display:flex;flex-direction:column;gap:0;padding-left:12px">
        <span data-rail-indicator style="position:absolute;left:0;top:8px;width:1px;height:10px;background:#9184d9;box-shadow:0 0 10px rgba(145,132,217,.7);transition:transform .45s cubic-bezier(.16,1,.3,1)"></span>
        <span style="position:absolute;left:0;top:8px;bottom:8px;width:1px;background:rgba(233,233,237,.12)"></span>
        @foreach ($rail as $r)
            <button data-rail title="{{ $r['title'] }}" class="lab-rail-btn" style="appearance:none;background:none;border:0;cursor:pointer;display:flex;align-items:center;height:26px;padding:0;color:rgba(226,221,209,.40);font-family:inherit;font-size:10px;letter-spacing:.20em;text-transform:uppercase;transition:color .3s">
                <span>{{ $r['n'] }}</span>
            </button>
        @endforeach
    </nav>

    <div style="position:fixed;left:0;bottom:0;height:1px;width:100%;background:rgba(233,233,237,.10);z-index:50">
        <span data-lab-progress-bar style="display:block;height:100%;width:100%;transform:scaleX(0);transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.25),#9184d9)"></span>
    </div>

    <div data-geo-hex style="position:fixed;inset:-25vmax;z-index:70;visibility:hidden;pointer-events:none;background:radial-gradient(circle at 50% 50%,#191a33,#05050a 70%);clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)"></div>
    <div data-geo-top style="position:fixed;left:0;top:0;width:100%;height:50.5vh;z-index:71;visibility:hidden;pointer-events:none;background:linear-gradient(180deg,#05050a,#0b0c14)"></div>
    <div data-geo-bot style="position:fixed;left:0;bottom:0;width:100%;height:50.5vh;z-index:71;visibility:hidden;pointer-events:none;background:linear-gradient(0deg,#05050a,#0b0c14)"></div>
    <span data-geo-line style="position:fixed;left:0;top:50%;width:100%;height:1px;z-index:72;visibility:hidden;pointer-events:none;background:linear-gradient(90deg,transparent,#9184d9,transparent);transform:scaleX(0)"></span>

    <main style="position:relative;z-index:1">

        {{-- ==================================================== 01 --- --}}
        <section class="lab-sec" data-lab="panels" data-screen-label="Lab 01 Panneaux">
            <div style="min-height:64vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(48px,7vw,110px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Panneaux aimantés</h1>
                <p data-reveal="up" data-reveal-delay="120" style="margin:0;max-width:52ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Quatre plans pleine hauteur. Le scroll reste libre, puis se cale sur le plan le plus proche dès qu'il s'immobilise. Chaque plan porte sa propre valeur de fond et une parallaxe interne.</p>
            </div>

            <div data-panels>
                <div data-panel style="position:relative;min-height:100vh;display:grid;place-items:center;overflow:hidden;background:#05050a;border-top:1px solid rgba(233,233,237,.08)">
                    <div data-panel-speed="-0.5" style="position:absolute;inset:0;background:radial-gradient(60% 50% at 20% 40%,rgba(145,132,217,.16),transparent 70%)"></div>
                    <span data-panel-speed="0.55" style="position:absolute;right:clamp(20px,8vw,140px);top:22vh;font-size:clamp(120px,22vw,320px);font-weight:200;line-height:1;color:rgba(233,233,237,.045);letter-spacing:-.05em">01</span>
                    <div data-panel-speed="0.18" style="position:relative;display:flex;flex-direction:column;gap:20px;max-width:min(640px,74vw);padding-left:clamp(20px,4vw,40px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Plan 01</span>
                        <h2 style="margin:0;font-size:clamp(30px,4.6vw,62px);font-weight:200;letter-spacing:-.025em;line-height:1.02">Orbite</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.7;color:rgba(226,221,209,.62)">Le point de départ. Fond le plus sombre du système, une seule source de lumière hors cadre.</p>
                    </div>
                    <span data-panel-speed="-0.9" style="position:absolute;left:14vw;bottom:16vh;width:clamp(90px,12vw,170px);aspect-ratio:1;border:1px solid rgba(145,132,217,.30);border-radius:50%"></span>
                </div>

                <div data-panel style="position:relative;min-height:100vh;display:grid;place-items:center;overflow:hidden;background:#0b0c14">
                    <div data-panel-speed="-0.35" style="position:absolute;inset:-10%;background-image:linear-gradient(rgba(233,233,237,.055) 1px,transparent 1px),linear-gradient(90deg,rgba(233,233,237,.055) 1px,transparent 1px);background-size:80px 80px"></div>
                    <span data-panel-speed="0.55" style="position:absolute;left:clamp(20px,8vw,140px);top:20vh;font-size:clamp(120px,22vw,320px);font-weight:200;line-height:1;color:rgba(233,233,237,.045);letter-spacing:-.05em">02</span>
                    <div data-panel-speed="0.18" style="position:relative;display:flex;flex-direction:column;gap:20px;max-width:min(640px,74vw);text-align:right;align-items:flex-end;padding-right:clamp(20px,4vw,40px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Plan 02</span>
                        <h2 style="margin:0;font-size:clamp(30px,4.6vw,62px);font-weight:200;letter-spacing:-.025em;line-height:1.02">Silence</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.7;color:rgba(226,221,209,.62)">Grille technique et alignement à droite. Le même contenu, une autre densité.</p>
                    </div>
                </div>

                <div data-panel style="position:relative;min-height:100vh;display:grid;place-items:center;overflow:hidden;background:#12131d">
                    <svg data-panel-speed="-0.75" viewBox="0 0 100 100" preserveAspectRatio="none" style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:min(74vh,58vw);aspect-ratio:1;overflow:visible"><polygon points="25,1 75,1 99,50 75,99 25,99 1,50" fill="none" stroke="rgba(233,233,237,.12)" stroke-width="1" vector-effect="non-scaling-stroke"></polygon></svg>
                    <svg data-panel-speed="-0.4" viewBox="0 0 100 100" preserveAspectRatio="none" style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:min(48vh,38vw);aspect-ratio:1;overflow:visible"><polygon points="25,1 75,1 99,50 75,99 25,99 1,50" fill="none" stroke="rgba(145,132,217,.28)" stroke-width="1" vector-effect="non-scaling-stroke"></polygon></svg>
                    <div data-panel-speed="0.2" style="position:relative;display:flex;flex-direction:column;align-items:center;gap:18px;text-align:center;padding:0 24px">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Plan 03</span>
                        <h2 style="margin:0;font-size:clamp(30px,4.6vw,62px);font-weight:200;letter-spacing:-.025em;line-height:1.02">Densité</h2>
                        <p style="margin:0;max-width:38ch;font-size:15px;line-height:1.7;color:rgba(226,221,209,.62)">Deux hexagones concentriques dérivent à des vitesses opposées au texte.</p>
                    </div>
                </div>

                <div data-panel style="position:relative;min-height:100vh;display:grid;place-items:center;overflow:hidden;background:#191a33">
                    <div data-panel-speed="-0.5" style="position:absolute;inset:0;background:radial-gradient(70% 60% at 70% 80%,rgba(145,132,217,.28),transparent 70%)"></div>
                    <span data-panel-speed="0.55" style="position:absolute;right:clamp(20px,8vw,140px);bottom:14vh;font-size:clamp(120px,22vw,320px);font-weight:200;line-height:1;color:rgba(233,233,237,.06);letter-spacing:-.05em">04</span>
                    <div data-panel-speed="0.16" style="position:relative;display:flex;flex-direction:column;gap:20px;max-width:min(640px,74vw);padding-left:clamp(20px,4vw,40px)">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#b3a9e6">Plan 04</span>
                        <h2 style="margin:0;font-size:clamp(30px,4.6vw,62px);font-weight:200;letter-spacing:-.025em;line-height:1.02">Seuil</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.7;color:rgba(226,221,209,.72)">Le seul fond saturé de la série. Il marque la sortie de la séquence.</p>
                    </div>
                </div>
            </div>

            <div style="position:fixed;right:clamp(14px,2.6vw,30px);top:50%;transform:translateY(-50%);z-index:40;display:flex;flex-direction:column;align-items:flex-end;gap:12px">
                <span data-panel-label style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.45)">Orbite</span>
                <div data-panel-dots style="display:flex;flex-direction:column;gap:10px;align-items:flex-end">
                    <span style="width:16px;height:1px;background:rgba(233,233,237,.22);transition:width .4s,background .4s"></span>
                    <span style="width:16px;height:1px;background:rgba(233,233,237,.22);transition:width .4s,background .4s"></span>
                    <span style="width:16px;height:1px;background:rgba(233,233,237,.22);transition:width .4s,background .4s"></span>
                    <span style="width:16px;height:1px;background:rgba(233,233,237,.22);transition:width .4s,background .4s"></span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:46ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Le calage est piloté par Lenis, pas par <code style="color:#b3a9e6">scroll-snap</code> : un seul système contrôle le scroll, et la position finale reste interruptible.</p>
            </div>
        </section>

        {{-- ==================================================== 02 --- --}}
        <section class="lab-sec" data-lab="depth" data-screen-label="Lab 02 Profondeur">
            <div style="min-height:58vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Profondeur 2.5D</h1>
                <p data-reveal="up" style="margin:0;max-width:52ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Cinq couches, cinq vitesses. Aucune 3D : uniquement des translations proportionnelles au scroll.</p>
            </div>

            <div data-parallax-scope style="position:relative;min-height:150vh;overflow:hidden;background:linear-gradient(180deg,#05050a,#0b0c14 40%,#05050a)">
                <div data-speed="0.9" style="position:absolute;inset:-20% 0;background-image:linear-gradient(rgba(233,233,237,.05) 1px,transparent 1px);background-size:100% 120px"></div>
                <div data-speed="0.6" style="position:absolute;left:8%;top:22%;width:min(46vw,520px);aspect-ratio:1;border-radius:50%;background:radial-gradient(circle,rgba(145,132,217,.30),transparent 68%);filter:blur(18px);animation:breathe 9s ease-in-out infinite"></div>

                <div data-speed="0.28" data-scrub-scale="1.10" style="position:absolute;left:50%;top:26%;translate:-50% 0;width:min(58vw,760px);aspect-ratio:16/10;overflow:hidden;border:1px solid rgba(233,233,237,.12);background:linear-gradient(140deg,#12131d,#05050a 60%,#191a33)">
                    <span style="position:absolute;inset:0;background-image:linear-gradient(90deg,rgba(233,233,237,.06) 1px,transparent 1px);background-size:44px 100%"></span>
                    <span style="position:absolute;left:16px;bottom:12px;font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Couche image</span>
                </div>

                <h2 data-speed="0.05" style="position:absolute;left:0;right:0;top:44%;margin:0;text-align:center;font-size:clamp(48px,13vw,190px);font-weight:200;letter-spacing:-.05em;line-height:.9;color:#e2ddd1;mix-blend-mode:difference">DÉRIVE</h2>

                <span data-speed="-0.7" style="position:absolute;left:12%;bottom:22%;width:clamp(60px,8vw,120px);aspect-ratio:1;border:1px solid rgba(145,132,217,.4);rotate:12deg"></span>
                <span data-speed="-1.1" style="position:absolute;right:14%;top:34%;width:clamp(40px,5vw,80px);aspect-ratio:1;background:rgba(145,132,217,.16);clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)"></span>
                <span data-speed="-1.4" style="position:absolute;right:26%;bottom:12%;width:1px;height:clamp(90px,14vh,180px);background:linear-gradient(180deg,transparent,#9184d9,transparent)"></span>

                <div data-speed="0.14" style="position:absolute;left:clamp(56px,9vw,180px);bottom:14%;max-width:34ch;display:flex;flex-direction:column;gap:12px">
                    <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Couche contenu</span>
                    <p style="margin:0;font-size:15px;line-height:1.75;color:rgba(226,221,209,.72)">Le titre passe derrière l'image en fondu de différence. Les marques géométriques vont plus vite que le scroll, la grille plus lentement.</p>
                </div>

                <div style="position:absolute;inset:0;pointer-events:none;background:radial-gradient(120% 90% at 50% 50%,transparent 45%,rgba(5,5,10,.85))"></div>
            </div>

            <div style="position:relative;padding:clamp(60px,10vw,140px) clamp(56px,9vw,180px);display:grid;gap:clamp(24px,4vw,60px);grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr));border-top:1px solid rgba(233,233,237,.08)" data-reveal-stagger="90">
                <div style="display:flex;flex-direction:column;gap:8px"><span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Grille</span><span style="font-size:26px;font-weight:200">0.9×</span></div>
                <div style="display:flex;flex-direction:column;gap:8px"><span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Image</span><span style="font-size:26px;font-weight:200">0.28×</span></div>
                <div style="display:flex;flex-direction:column;gap:8px"><span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Titre</span><span style="font-size:26px;font-weight:200">0.05×</span></div>
                <div style="display:flex;flex-direction:column;gap:8px"><span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Marques</span><span style="font-size:26px;font-weight:200">−1.4×</span></div>
            </div>
        </section>

        {{-- ==================================================== 03 --- --}}
        <section class="lab-sec" data-lab="progress" data-screen-label="Lab 03 Progression">
            <div style="min-height:58vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">État = position</h1>
                <p data-reveal="up" style="margin:0;max-width:52ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">L'animation ne se joue pas à l'entrée de la section : sa valeur est lue directement dans la progression du scroll. Remonte, elle revient en arrière.</p>
            </div>

            <div data-driven style="position:relative;height:100vh;overflow:hidden;background:#05050a;border-top:1px solid rgba(233,233,237,.08)">
                <div data-driven-bg style="position:absolute;inset:0;background:radial-gradient(70% 60% at 50% 50%,rgba(145,132,217,.10),transparent 70%)"></div>
                <div style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:min(72vmin,520px);aspect-ratio:1;border:1px solid rgba(233,233,237,.08);border-radius:50%"></div>
                <div data-driven-shape style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:min(30vmin,210px);aspect-ratio:1;background:linear-gradient(140deg,rgba(145,132,217,.9),rgba(145,132,217,.15));clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)"></div>
                <svg data-driven-ring viewBox="0 0 100 100" preserveAspectRatio="none" style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:min(46vmin,330px);aspect-ratio:1;overflow:visible"><polygon points="25,1 75,1 99,50 75,99 25,99 1,50" fill="none" stroke="rgba(145,132,217,.45)" stroke-width="1" vector-effect="non-scaling-stroke"></polygon></svg>

                <div style="position:absolute;left:clamp(56px,9vw,180px);top:50%;translate:0 -50%;display:flex;flex-direction:column;gap:6px">
                    <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.34)">Progression</span>
                    <span data-driven-readout style="font-size:clamp(40px,7vw,96px);font-weight:200;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums;color:#e2ddd1">0.000</span>
                </div>

                <div style="position:absolute;right:clamp(56px,9vw,180px);top:50%;translate:0 -50%;display:flex;flex-direction:column;gap:14px;text-align:right">
                    <span data-driven-step style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30);transition:color .3s">00 · hors champ</span>
                    <span data-driven-step style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30);transition:color .3s">25 · apparition</span>
                    <span data-driven-step style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30);transition:color .3s">50 · centre</span>
                    <span data-driven-step style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30);transition:color .3s">75 · transformation</span>
                    <span data-driven-step style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30);transition:color .3s">100 · état final</span>
                </div>

                <div style="position:absolute;left:clamp(56px,9vw,180px);right:clamp(56px,9vw,180px);bottom:clamp(40px,6vh,80px);height:1px;background:rgba(233,233,237,.12)">
                    <span data-driven-bar style="display:block;height:1px;width:100%;transform:scaleX(0);transform-origin:0 50%;background:#9184d9"></span>
                </div>
            </div>

            <div style="min-height:60vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px)">
                <p data-reveal="blur" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Un seul <code style="color:#b3a9e6">ScrollTrigger</code> épinglé, une timeline en <code style="color:#b3a9e6">scrub</code>. Le compteur, la barre, la forme et le fond lisent tous la même valeur.</p>
            </div>
        </section>

        {{-- ==================================================== 04 --- --}}
        <section class="lab-sec" data-lab="story" data-screen-label="Lab 04 Récit">
            <div style="min-height:58vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Récit épinglé</h1>
                <p data-reveal="up" style="margin:0;max-width:52ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">La section reste à l'écran pendant que quatre étapes se succèdent, puis la page reprend son cours.</p>
            </div>

            <div data-story style="position:relative;height:100vh;overflow:hidden;background:#0b0c14;border-top:1px solid rgba(233,233,237,.08)">
                <div data-story-bg style="position:absolute;inset:0;background:radial-gradient(80% 70% at 22% 30%,rgba(145,132,217,.16),transparent 70%)"></div>

                <div style="position:relative;height:100%;display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,320px),1fr));align-items:center;gap:clamp(24px,5vw,80px);padding:0 clamp(56px,9vw,180px)">
                    <div style="display:flex;flex-direction:column;gap:22px;max-width:46ch">
                        <span data-story-index style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Étape 01 / 04</span>
                        <h2 data-story-title style="margin:0;font-size:clamp(28px,4.4vw,60px);font-weight:200;letter-spacing:-.025em;line-height:1.04">Repérage</h2>
                        <p data-story-body style="margin:0;font-size:15px;line-height:1.75;color:rgba(226,221,209,.62)">On relève les contraintes réelles avant de dessiner : supports, contenus, volumes, délais.</p>
                        <div style="display:flex;flex-direction:column;gap:10px;padding-top:8px">
                            <span data-story-bullet style="display:flex;gap:12px;font-size:13px;color:rgba(226,221,209,.45)"><span style="color:#9184d9">—</span>Audit des pages existantes</span>
                            <span data-story-bullet style="display:flex;gap:12px;font-size:13px;color:rgba(226,221,209,.45)"><span style="color:#9184d9">—</span>Inventaire des composants</span>
                            <span data-story-bullet style="display:flex;gap:12px;font-size:13px;color:rgba(226,221,209,.45)"><span style="color:#9184d9">—</span>Budget d'animation</span>
                        </div>
                    </div>

                    <div style="position:relative;aspect-ratio:4/5;max-height:58vh;overflow:hidden;border:1px solid rgba(233,233,237,.12)">
                        <div data-story-plate style="position:absolute;inset:0;background:linear-gradient(160deg,#12131d,#05050a 55%,#191a33)"></div>
                        <span data-story-plate style="position:absolute;inset:0;background:linear-gradient(200deg,#191a33,#0b0c14 60%);opacity:0"></span>
                        <span data-story-plate style="position:absolute;inset:0;background:radial-gradient(70% 60% at 60% 40%,rgba(145,132,217,.34),#05050a 72%);opacity:0"></span>
                        <span data-story-plate style="position:absolute;inset:0;background:linear-gradient(20deg,#05050a,#12131d);opacity:0"></span>
                        <svg data-story-mark viewBox="0 0 100 100" preserveAspectRatio="none" style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:56%;aspect-ratio:1;overflow:visible"><polygon points="25,1 75,1 99,50 75,99 25,99 1,50" fill="none" stroke="rgba(233,233,237,.20)" stroke-width="1" vector-effect="non-scaling-stroke"></polygon></svg>
                        <span style="position:absolute;left:14px;bottom:12px;font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Planche</span>
                    </div>
                </div>

                <div style="position:absolute;left:clamp(56px,9vw,180px);right:clamp(56px,9vw,180px);bottom:clamp(30px,5vh,60px);display:flex;gap:8px">
                    <span data-story-tick style="flex:1;height:1px;background:rgba(233,233,237,.14);transform-origin:0 50%"></span>
                    <span data-story-tick style="flex:1;height:1px;background:rgba(233,233,237,.14);transform-origin:0 50%"></span>
                    <span data-story-tick style="flex:1;height:1px;background:rgba(233,233,237,.14);transform-origin:0 50%"></span>
                    <span data-story-tick style="flex:1;height:1px;background:rgba(233,233,237,.14);transform-origin:0 50%"></span>
                </div>
            </div>

            <div style="min-height:58vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px)">
                <p data-reveal="wipe" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Le titre, la planche, les puces, le fond et la barre d'étapes appartiennent à la même timeline épinglée sur 3200 px de scroll.</p>
            </div>
        </section>

        {{-- ==================================================== 05 --- --}}
        <section class="lab-sec" data-lab="flux" data-screen-label="Lab 05 Flux">
            <div style="min-height:52vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(36px,5vw,70px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Flux continu</h1>
                <p data-reveal="up" style="margin:0;max-width:52ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Une liste qui ne se termine pas, rendue par un pool de nœuds recyclés. Le nombre d'éléments dans le DOM reste constant, quel que soit le nombre de lignes parcourues.</p>
                <div style="display:flex;gap:clamp(20px,4vw,48px);flex-wrap:wrap;padding-top:12px">
                    <span style="display:flex;flex-direction:column;gap:4px"><span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Nœuds</span><span data-flux-nodes style="font-size:22px;font-weight:200;font-variant-numeric:tabular-nums">—</span></span>
                    <span style="display:flex;flex-direction:column;gap:4px"><span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Index atteint</span><span data-flux-index style="font-size:22px;font-weight:200;font-variant-numeric:tabular-nums">0</span></span>
                </div>
            </div>
            <div data-flux style="position:relative;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)"></div>
        </section>

        {{-- ==================================================== 06 --- --}}
        <section class="lab-sec" data-lab="reveal" data-screen-label="Lab 06 Révélation">
            <div style="min-height:58vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Masques</h1>
                <p data-reveal="up" style="margin:0;max-width:52ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Quatre transitions de section liées au scroll : hexagone, deux côtés, ligne centrale, image révélée.</p>
            </div>

            <div data-mask-hex style="position:relative;min-height:120vh;overflow:hidden;background:#05050a;border-top:1px solid rgba(233,233,237,.08)">
                <div data-mask-hex-in style="position:absolute;inset:0;display:grid;place-items:center;background:radial-gradient(70% 60% at 50% 45%,#191a33,#05050a 72%);clip-path:polygon(50% 50%,50% 50%,50% 50%,50% 50%,50% 50%,50% 50%)">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:16px;text-align:center;padding:0 24px">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#b3a9e6">Hexagone</span>
                        <h2 style="margin:0;font-size:clamp(28px,5vw,68px);font-weight:200;letter-spacing:-.03em">Ouverture</h2>
                        <p style="margin:0;max-width:34ch;font-size:14px;line-height:1.7;color:rgba(226,221,209,.62)">Le masque suit exactement la position du scroll.</p>
                    </div>
                </div>
                <span style="position:absolute;left:clamp(56px,9vw,180px);top:clamp(30px,5vh,60px);font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">01 · clip-path hexagonal</span>
            </div>

            <div data-mask-split style="position:relative;min-height:120vh;overflow:hidden;background:#0b0c14">
                <div style="position:absolute;inset:0;display:grid;place-items:center">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:16px;text-align:center;padding:0 24px">
                        <span style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Deux côtés</span>
                        <h2 style="margin:0;font-size:clamp(28px,5vw,68px);font-weight:200;letter-spacing:-.03em">Écartement</h2>
                        <p style="margin:0;max-width:34ch;font-size:14px;line-height:1.7;color:rgba(226,221,209,.62)">Deux panneaux arrivent puis s'ouvrent sur le contenu.</p>
                    </div>
                </div>
                <div data-mask-left style="position:absolute;left:0;top:0;width:50.5%;height:100%;background:linear-gradient(90deg,#05050a,#12131d)"></div>
                <div data-mask-right style="position:absolute;right:0;top:0;width:50.5%;height:100%;background:linear-gradient(270deg,#05050a,#12131d)"></div>
                <span style="position:absolute;left:clamp(56px,9vw,180px);top:clamp(30px,5vh,60px);z-index:2;font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">02 · panneaux latéraux</span>
            </div>

            <div data-mask-line style="position:relative;min-height:120vh;overflow:hidden;background:#05050a;display:grid;grid-template-rows:1fr auto 1fr;align-items:center">
                <div data-mask-line-top style="display:flex;align-items:flex-end;justify-content:flex-start;padding:0 clamp(56px,9vw,180px) clamp(24px,4vh,48px)">
                    <h2 style="margin:0;font-size:clamp(26px,4.4vw,58px);font-weight:200;letter-spacing:-.03em">Avant</h2>
                </div>
                <span data-mask-line-rule style="display:block;height:1px;width:100%;transform:scaleX(0);transform-origin:50% 50%;background:linear-gradient(90deg,transparent,#9184d9 20%,#9184d9 80%,transparent)"></span>
                <div data-mask-line-bot style="display:flex;align-items:flex-start;justify-content:flex-end;padding:clamp(24px,4vh,48px) clamp(56px,9vw,180px) 0">
                    <h2 style="margin:0;font-size:clamp(26px,4.4vw,58px);font-weight:200;letter-spacing:-.03em;color:rgba(226,221,209,.62)">Après</h2>
                </div>
                <span style="position:absolute;left:clamp(56px,9vw,180px);top:clamp(30px,5vh,60px);font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">03 · ligne séparatrice</span>
            </div>

            <div data-mask-img style="position:relative;min-height:130vh;overflow:hidden;background:#0b0c14;display:grid;place-items:center">
                <div style="position:relative;width:min(64vw,820px);aspect-ratio:16/9;overflow:hidden;border:1px solid rgba(233,233,237,.12)">
                    <div data-mask-img-in style="position:absolute;inset:0;background:linear-gradient(140deg,#191a33,#05050a 55%,#12131d);clip-path:inset(100% 0% 0% 0%)">
                        <span style="position:absolute;inset:0;background-image:linear-gradient(90deg,rgba(233,233,237,.06) 1px,transparent 1px);background-size:52px 100%"></span>
                        <svg viewBox="0 0 100 100" preserveAspectRatio="none" style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:34%;aspect-ratio:1;overflow:visible"><polygon points="25,1 75,1 99,50 75,99 25,99 1,50" fill="none" stroke="rgba(145,132,217,.5)" stroke-width="1" vector-effect="non-scaling-stroke"></polygon></svg>
                    </div>
                    <span style="position:absolute;left:14px;bottom:12px;font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">04 · image révélée</span>
                </div>
            </div>
        </section>

        {{-- ==================================================== 07 --- --}}
        <section class="lab-sec" data-lab="orbit" data-screen-label="Lab 07 Orbital">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Chambre orbitale</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Le scroll n'avance plus dans la page : il pilote une orbite. Les fiches arrivent sur l'anneau, le plan bascule vers l'horizontale, puis l'anneau s'effondre en liste lisible. Chaque position est recalculée à la frame depuis la progression — remonter inverse exactement le mouvement.</p>
            </div>

            <div data-orbit style="position:relative;height:100vh;overflow:hidden;background:radial-gradient(58% 50% at 50% 50%,#12131d,#05050a 72%);border-top:1px solid rgba(233,233,237,.08)">
                <span style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:min(66vw,760px);aspect-ratio:1;border:1px solid rgba(233,233,237,.05);border-radius:50%"></span>
                <span style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:6px;height:6px;border-radius:50%;background:#9184d9;box-shadow:0 0 24px rgba(145,132,217,.8)"></span>

                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω1</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Champ magnétique</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Identité</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω2</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Table rase</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Direction artistique</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω3</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Ligne de fuite</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Éditorial</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω4</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Vitesse limite</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Produit</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω5</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Halo</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Motion</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω6</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Pierre calcaire</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Identité</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω7</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Second souffle</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Web · WebGL</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω8</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Angle mort</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Recherche</span>
                </article>
                <article data-orb style="position:absolute;left:0;top:0;width:clamp(148px,16vw,232px);display:flex;flex-direction:column;gap:10px;padding:16px 18px 18px;border:1px solid rgba(233,233,237,.14);background:rgba(11,12,20,.74)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Ω9</span>
                    <span style="font-size:clamp(15px,1.5vw,20px);font-weight:200;letter-spacing:-.01em">Périhélie</span>
                    <span style="font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">Éditorial</span>
                </article>

                <div style="position:absolute;left:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;flex-direction:column;gap:8px;z-index:60">
                    <span data-orbit-phase style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:#9184d9">Approche</span>
                    <span data-orbit-val style="font-size:clamp(26px,3.4vw,44px);font-weight:200;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1">0.000</span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Trois régimes dans une seule section : arrivée, rotation du plan, effondrement. Aucune interpolation GSAP sur les fiches — un seul <code style="color:#b3a9e6">layout(p)</code> écrit les neuf transformations.</p>
            </div>
        </section>

        {{-- ==================================================== 08 --- --}}
        <section class="lab-sec" data-lab="magnet" data-screen-label="Lab 08 Magnétique">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Champ polarisé</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Ici ce n'est pas la position du scroll qui compte, mais sa vitesse et son signe. Descendre attire la matière vers le noyau, remonter la repousse, s'arrêter laisse le champ se détendre. Le geste devient une force.</p>
            </div>

            <div data-magnet style="position:relative;height:100vh;overflow:hidden;background:#05050a;border-top:1px solid rgba(233,233,237,.08)">
                <div data-field style="position:absolute;inset:0"></div>
                <span data-attractor style="position:absolute;left:50%;top:50%;width:clamp(64px,9vw,132px);aspect-ratio:1;border:1px solid rgba(145,132,217,.85);border-radius:50%;pointer-events:none;transform:translate(-50%,-50%)"></span>

                <div style="position:absolute;left:clamp(30px,5vw,70px);top:50%;translate:0 -50%;display:flex;flex-direction:column;gap:12px;max-width:26ch;pointer-events:none">
                    <span data-mag-pol style="font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Attraction</span>
                    <span data-mag-v style="font-size:clamp(30px,4vw,56px);font-weight:200;letter-spacing:-.04em;line-height:1;font-variant-numeric:tabular-nums">0.0</span>
                    <span style="font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:rgba(226,221,209,.34)">px / frame</span>
                </div>

                <div style="position:absolute;right:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;flex-direction:column;align-items:flex-end;gap:10px;pointer-events:none">
                    <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Amplitude</span>
                    <span style="position:relative;display:block;width:clamp(120px,18vw,240px);height:1px;background:rgba(233,233,237,.14)">
                        <span data-mag-amp style="position:absolute;left:0;top:0;height:1px;width:100%;transform:scaleX(0);transform-origin:0 50%;background:#9184d9"></span>
                    </span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">135 nœuds, un seul <code style="color:#b3a9e6">ticker</code> GSAP, aucune bibliothèque de physique : ressort simple vers une cible calculée depuis la vitesse de Lenis.</p>
            </div>
        </section>

        {{-- ==================================================== 09 --- --}}
        <section class="lab-sec" data-lab="corridor" data-screen-label="Lab 09 Corridor">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Corridor</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">La page ne défile pas : la caméra avance. Les plans viennent de loin, grossissent, passent derrière l'observateur et disparaissent. Le scroll est une position dans l'espace, pas dans un document.</p>
            </div>

            <div data-corridor-stage style="position:relative;height:100vh;overflow:hidden;background:linear-gradient(180deg,#05050a,#0b0c14 55%,#05050a);border-top:1px solid rgba(233,233,237,.08);perspective:900px;perspective-origin:50% 50%">
                <div data-corridor style="position:absolute;inset:0;transform-style:preserve-3d;will-change:transform">
                    <div data-plane="-6400" data-plane-x="-300" data-plane-y="-40" data-plane-rot="-2" style="position:absolute;left:50%;top:50%;width:min(30vw,400px);aspect-ratio:4/3;border:1px solid rgba(233,233,237,.14);background:linear-gradient(140deg,#12131d,#05050a 65%);display:grid;place-items:center">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.38)">Plan 01 · repérage</span>
                    </div>
                    <div data-plane="-5500" data-plane-x="280" data-plane-y="60" data-plane-rot="3" style="position:absolute;left:50%;top:50%;width:min(26vw,340px);aspect-ratio:1;border:1px solid rgba(145,132,217,.34);background:radial-gradient(70% 60% at 40% 40%,rgba(145,132,217,.22),#05050a 72%);display:grid;place-items:center">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.38)">Plan 02 · lumière</span>
                    </div>
                    <div data-plane="-4400" data-plane-x="-180" data-plane-y="140" style="position:absolute;left:50%;top:50%;width:min(36vw,460px);aspect-ratio:16/9;border:1px solid rgba(233,233,237,.14);background:linear-gradient(200deg,#191a33,#0b0c14 60%);display:grid;place-items:center">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.38)">Plan 03 · matière</span>
                    </div>
                    <div data-plane="-3400" data-plane-x="240" data-plane-y="-160" data-plane-rot="-4" style="position:absolute;left:50%;top:50%;width:min(22vw,300px);aspect-ratio:3/4;border:1px solid rgba(233,233,237,.12);background:linear-gradient(20deg,#05050a,#12131d);display:grid;place-items:center">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.38)">Plan 04 · grille</span>
                    </div>
                    <div data-plane="-2400" data-plane-x="-40" data-plane-y="0" style="position:absolute;left:50%;top:50%;width:min(44vw,560px);aspect-ratio:2/1;border:1px solid rgba(145,132,217,.28);background:linear-gradient(160deg,#12131d,#191a33);display:grid;place-items:center">
                        <span style="font-size:clamp(18px,2.4vw,34px);font-weight:200;letter-spacing:-.02em">Traversée</span>
                    </div>
                    <div data-plane="-1500" data-plane-x="-360" data-plane-y="120" data-plane-rot="5" style="position:absolute;left:50%;top:50%;width:min(20vw,260px);aspect-ratio:1;border:1px solid rgba(233,233,237,.12);background:linear-gradient(120deg,#0b0c14,#191a33);display:grid;place-items:center">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.38)">Plan 06</span>
                    </div>
                    <div data-plane="-800" data-plane-x="320" data-plane-y="-120" style="position:absolute;left:50%;top:50%;width:min(24vw,320px);aspect-ratio:4/5;border:1px solid rgba(233,233,237,.12);background:linear-gradient(220deg,#12131d,#05050a);display:grid;place-items:center">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.38)">Plan 07</span>
                    </div>
                    <div data-plane="-200" data-plane-x="0" data-plane-y="0" style="position:absolute;left:50%;top:50%;width:min(34vw,440px);aspect-ratio:1;border:1px solid rgba(145,132,217,.4);background:radial-gradient(60% 55% at 50% 50%,rgba(145,132,217,.26),#05050a 74%);clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)"></div>
                </div>

                <div style="position:absolute;left:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;flex-direction:column;gap:8px;pointer-events:none">
                    <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Caméra · Z</span>
                    <span data-corridor-z style="font-size:clamp(24px,3vw,40px);font-weight:200;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1">0</span>
                </div>
                <div style="position:absolute;inset:0;pointer-events:none;background:radial-gradient(120% 90% at 50% 50%,transparent 42%,rgba(5,5,10,.9))"></div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">CSS 3D uniquement : <code style="color:#b3a9e6">perspective</code> sur la scène, <code style="color:#b3a9e6">translateZ</code> sur huit plans. La visibilité est coupée dès qu'un plan dépasse la caméra.</p>
            </div>
        </section>

        {{-- ==================================================== 10 --- --}}
        <section class="lab-sec" data-lab="deconstruct" data-screen-label="Lab 10 Déconstruction">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Démontage</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Aucune section n'apparaît : la précédente est démontée. Les lettres se dispersent, les lignes se détachent, les plaques partent vers leurs points de fuite — et la section suivante se réassemble depuis ces mêmes trajectoires.</p>
            </div>

            <div data-decon style="position:relative;height:100vh;overflow:hidden;background:#0b0c14;border-top:1px solid rgba(233,233,237,.08)">
                <h2 data-decon-title style="position:absolute;left:clamp(40px,7vw,140px);top:34%;margin:0;font-size:clamp(38px,7vw,110px);font-weight:200;letter-spacing:-.04em;line-height:.95">Structure</h2>
                <span data-part="620,-180,24" style="position:absolute;left:clamp(40px,7vw,140px);top:52%;display:block;width:clamp(160px,26vw,420px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <span data-part="-480,260,-40" style="position:absolute;left:clamp(40px,7vw,140px);top:58%;display:block;width:clamp(120px,18vw,300px);height:1px;background:rgba(233,233,237,.22)"></span>
                <span data-part="520,420,60" style="position:absolute;right:clamp(40px,8vw,160px);top:26%;width:clamp(90px,12vw,180px);aspect-ratio:1;border:1px solid rgba(233,233,237,.16);background:linear-gradient(140deg,#12131d,#05050a)"></span>
                <span data-part="-560,-340,-72" style="position:absolute;right:clamp(120px,18vw,340px);bottom:20%;width:clamp(70px,9vw,140px);aspect-ratio:1;background:rgba(145,132,217,.16);clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)"></span>
                <span data-part="380,-520,18" style="position:absolute;left:34%;bottom:16%;display:block;width:1px;height:clamp(80px,14vh,180px);background:linear-gradient(180deg,transparent,#9184d9,transparent)"></span>

                <div style="position:absolute;inset:0;display:grid;place-items:center;padding:0 clamp(40px,8vw,160px)">
                    <div style="display:flex;flex-direction:column;gap:20px;max-width:44ch">
                        <span data-build="-520,-120,-14" style="font-size:10px;letter-spacing:.30em;text-transform:uppercase;color:#9184d9">Reconstruction</span>
                        <h2 data-build="560,180,10" style="margin:0;font-size:clamp(30px,5vw,72px);font-weight:200;letter-spacing:-.035em;line-height:1">Matière</h2>
                        <p data-build="-420,320,-8" style="margin:0;font-size:15px;line-height:1.75;color:rgba(226,221,209,.62)">Les pièces libérées par la section précédente forment la suivante. La transition n'est pas un fondu : c'est un transfert de matière.</p>
                        <span data-build="480,-280,22" style="display:block;width:clamp(120px,20vw,320px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                    </div>
                </div>

                <div style="position:absolute;left:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;gap:24px;align-items:baseline;pointer-events:none">
                    <span data-decon-phase style="font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Démontage</span>
                    <span data-decon-val style="font-size:11px;letter-spacing:.20em;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">0.00</span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Les vecteurs de sortie et d'entrée sont déclarés dans le HTML (<code style="color:#b3a9e6">data-part</code>, <code style="color:#b3a9e6">data-build</code>) : le module ne connaît pas la mise en page.</p>
            </div>
        </section>

        {{-- ==================================================== 11 --- --}}
        <section class="lab-sec" data-lab="fracture" data-screen-label="Lab 11 Quadrants">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Quadrants contrariés</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Une seule section, quatre lois de mouvement. Deux zones suivent le scroll à des vitesses différentes, une le contredit — le contenu remonte quand on descend — et la dernière le traduit en échelle. Le regard perd le repère commun.</p>
            </div>

            <div data-fracture style="position:relative;min-height:200vh;display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;gap:1px;background:rgba(233,233,237,.10);border-top:1px solid rgba(233,233,237,.08)">
                <div data-zone="0.85" style="position:relative;overflow:hidden;background:#05050a">
                    <div data-zone-inner style="display:flex;flex-direction:column;gap:26px;padding:clamp(30px,5vw,70px)">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">A · 0.85×</span>
                        <span style="font-size:clamp(40px,7vw,110px);font-weight:200;letter-spacing:-.05em;line-height:.9">01<br>02<br>03<br>04<br>05</span>
                    </div>
                </div>
                <div data-zone="-0.55" style="position:relative;overflow:hidden;background:#0b0c14">
                    <div data-zone-inner style="display:flex;flex-direction:column;gap:20px;padding:clamp(30px,5vw,70px);max-width:40ch">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">B · −0.55× inversé</span>
                        <p style="margin:0;font-size:clamp(15px,1.5vw,20px);font-weight:200;line-height:1.6;color:rgba(226,221,209,.72)">Cette colonne monte quand la page descend. Le texte reste lisible parce que la vitesse relative reste faible ; la sensation d'erreur est volontaire.</p>
                        <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.45)">Le point de rencontre des quatre zones est le seul repère stable de l'écran.</p>
                    </div>
                </div>
                <div data-zone="0.30" data-zone-scale="1.22" style="position:relative;overflow:hidden;background:#12131d">
                    <div data-zone-inner style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;padding:clamp(30px,5vw,70px)">
                        <span style="aspect-ratio:1;border:1px solid rgba(233,233,237,.14)"></span>
                        <span style="aspect-ratio:1;border:1px solid rgba(145,132,217,.4)"></span>
                        <span style="aspect-ratio:1;border:1px solid rgba(233,233,237,.14)"></span>
                        <span style="aspect-ratio:1;background:rgba(145,132,217,.14)"></span>
                        <span style="aspect-ratio:1;border:1px solid rgba(233,233,237,.14)"></span>
                        <span style="aspect-ratio:1;border:1px solid rgba(233,233,237,.14)"></span>
                        <span style="aspect-ratio:1;border:1px solid rgba(233,233,237,.14)"></span>
                        <span style="aspect-ratio:1;background:rgba(233,233,237,.05)"></span>
                        <span style="aspect-ratio:1;border:1px solid rgba(145,132,217,.28)"></span>
                    </div>
                </div>
                <div data-zone="-1.15" data-zone-rot="5" style="position:relative;overflow:hidden;background:#191a33">
                    <div data-zone-inner style="display:flex;flex-direction:column;gap:18px;padding:clamp(30px,5vw,70px);align-items:flex-end;text-align:right">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#b3a9e6">D · −1.15× + rotation</span>
                        <span style="font-size:clamp(44px,8vw,130px);font-weight:200;letter-spacing:-.05em;line-height:.9">Dérive</span>
                        <span style="display:block;width:clamp(90px,14vw,200px);height:1px;background:linear-gradient(270deg,#9184d9,transparent)"></span>
                    </div>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Chaque zone est une fenêtre en <code style="color:#b3a9e6">overflow:hidden</code> ; seul son contenu bouge. Utilisable en production pour une page projets à quatre entrées.</p>
            </div>
        </section>

        {{-- ==================================================== 12 --- --}}
        <section class="lab-sec" data-lab="morph" data-screen-label="Lab 12 Morphogenèse">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Morphogenèse</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Rectangle, hexagone, cercle, plaque. Une seule surface change de forme au fil du scroll, et le contenu se révèle pendant la transformation. Les formes sont échantillonnées sur le même nombre de points, donc l'interpolation est exacte.</p>
            </div>

            <div data-morphstage style="position:relative;height:100vh;overflow:hidden;display:grid;place-items:center;background:radial-gradient(60% 55% at 50% 50%,#0b0c14,#05050a 74%);border-top:1px solid rgba(233,233,237,.08)">
                <div style="position:relative;width:min(58vh,54vw);aspect-ratio:1">
                    <div data-morph style="position:absolute;inset:0;background:linear-gradient(140deg,#191a33,#05050a 58%,#12131d);will-change:clip-path,transform">
                        <span style="position:absolute;inset:0;background-image:linear-gradient(90deg,rgba(233,233,237,.06) 1px,transparent 1px),linear-gradient(rgba(233,233,237,.06) 1px,transparent 1px);background-size:46px 46px"></span>
                        <span style="position:absolute;inset:0;background:radial-gradient(50% 45% at 50% 50%,rgba(145,132,217,.28),transparent 70%)"></span>
                    </div>
                </div>

                <div style="position:absolute;left:clamp(30px,5vw,70px);top:50%;translate:0 -50%;display:grid;pointer-events:none">
                    <span data-morph-at="0" data-morph-win="0.22" style="grid-area:1/1;font-size:clamp(20px,2.6vw,38px);font-weight:200;letter-spacing:-.02em">Rectangle</span>
                    <span data-morph-at="0.333" data-morph-win="0.22" style="grid-area:1/1;font-size:clamp(20px,2.6vw,38px);font-weight:200;letter-spacing:-.02em;opacity:0">Hexagone</span>
                    <span data-morph-at="0.666" data-morph-win="0.22" style="grid-area:1/1;font-size:clamp(20px,2.6vw,38px);font-weight:200;letter-spacing:-.02em;opacity:0">Cercle</span>
                    <span data-morph-at="1" data-morph-win="0.22" style="grid-area:1/1;font-size:clamp(20px,2.6vw,38px);font-weight:200;letter-spacing:-.02em;opacity:0">Plaque</span>
                </div>

                <div style="position:absolute;right:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;flex-direction:column;align-items:flex-end;gap:8px;pointer-events:none">
                    <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Interpolation</span>
                    <span data-morph-val style="font-size:clamp(24px,3vw,40px);font-weight:200;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1">0.000</span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">48 points échantillonnés par lancer de rayon depuis le centre de la forme. N'importe quel couple de formes devient interpolable, y compris cercle vers hexagone.</p>
            </div>
        </section>

        {{-- ==================================================== 13 --- --}}
        <section class="lab-sec" data-lab="braid" data-screen-label="Lab 13 Entrelacs">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Entrelacs</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Un seul angle de phase pilote la position latérale (son sinus) et la profondeur (son cosinus). Les trois brins ne se croisent donc pas en apparence : celui qui passe devant repasse réellement derrière un demi-tour plus loin, flou et rétréci.</p>
            </div>

            <div data-braid style="position:relative;height:100vh;overflow:hidden;display:grid;place-items:center;background:radial-gradient(70% 60% at 50% 45%,#0b0c14,#05050a 76%);border-top:1px solid rgba(233,233,237,.08)">
                <div style="position:relative;width:min(1080px,88vw);height:min(66vh,560px)">
                    <article data-strand style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:clamp(190px,21vw,290px);display:flex;flex-direction:column;gap:16px;will-change:transform,filter">
                        <span style="display:block;aspect-ratio:3/4;border:1px solid rgba(233,233,237,.12);background:linear-gradient(150deg,#191a33,#05050a 62%,#12131d)"></span>
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Brin A</span>
                        <span style="font-size:clamp(20px,2.2vw,30px);font-weight:200;letter-spacing:-.02em;line-height:1.05">Champ magnétique</span>
                    </article>
                    <article data-strand style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:clamp(190px,21vw,290px);display:flex;flex-direction:column;gap:16px;will-change:transform,filter">
                        <span style="display:block;aspect-ratio:3/4;border:1px solid rgba(145,132,217,.34);background:linear-gradient(30deg,#12131d,#05050a 58%,#191a33)"></span>
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Brin B</span>
                        <span style="font-size:clamp(20px,2.2vw,30px);font-weight:200;letter-spacing:-.02em;line-height:1.05">Ligne de fuite</span>
                    </article>
                    <article data-strand style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:clamp(190px,21vw,290px);display:flex;flex-direction:column;gap:16px;will-change:transform,filter">
                        <span style="display:block;aspect-ratio:3/4;border:1px solid rgba(233,233,237,.12);background:linear-gradient(210deg,#191a33,#0b0c14 66%,#05050a)"></span>
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">Brin C</span>
                        <span style="font-size:clamp(20px,2.2vw,30px);font-weight:200;letter-spacing:-.02em;line-height:1.05">Halo</span>
                    </article>
                </div>

                <div style="position:absolute;left:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;gap:24px;align-items:baseline;pointer-events:none">
                    <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Au premier plan</span>
                    <span data-braid-front style="font-size:12px;letter-spacing:.14em;color:#b3a9e6">Champ magnétique</span>
                    <span data-braid-val style="font-size:11px;letter-spacing:.20em;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">0.000</span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Aucune règle de superposition n'est écrite : le <code style="color:#b3a9e6">z-index</code>, le flou et l'échelle sont tous dérivés du même cosinus, donc l'ordre de profondeur ne peut pas se désynchroniser du mouvement.</p>
            </div>
        </section>

        {{-- ==================================================== 14 --- --}}
        <section class="lab-sec" data-lab="foldback" data-screen-label="Lab 14 Repli">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Repli</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">La progression est repliée en onde triangulaire. Passé la moitié de la section, continuer à descendre rejoue la scène à l'envers : la page avance, le contenu recule. Le retour a son propre sol et son reflet inversé, pour que le lecteur sache qu'il n'a pas remonté par erreur.</p>
            </div>

            <div data-fold-stage style="position:relative;height:100vh;overflow:hidden;display:grid;place-items:center;background:#05050a;border-top:1px solid rgba(233,233,237,.08)">
                <span data-fold-ground style="position:absolute;inset:0;background:radial-gradient(62% 58% at 50% 50%,#191a33,#0b0c14 74%);opacity:0"></span>
                <span data-fold-ghost style="position:absolute;left:50%;top:56%;translate:-50% -50%;scale:1 -1;font-size:clamp(60px,13vw,200px);font-weight:200;letter-spacing:-.05em;color:#9184d9;opacity:0;pointer-events:none">Repli</span>

                <div style="position:relative;z-index:1;width:min(880px,86vw);height:min(58vh,480px)">
                    <div data-fold="0" data-fold-win="0.24" style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:16px;will-change:transform,opacity">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">État 01</span>
                        <h2 style="margin:0;font-size:clamp(30px,5.4vw,78px);font-weight:200;letter-spacing:-.04em;line-height:1">Aller</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.75;color:rgba(226,221,209,.62)">Le geste habituel : descendre pour avancer dans la séquence.</p>
                    </div>
                    <div data-fold="0.34" data-fold-win="0.24" style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:16px;opacity:0;will-change:transform,opacity">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">État 02</span>
                        <h2 style="margin:0;font-size:clamp(30px,5.4vw,78px);font-weight:200;letter-spacing:-.04em;line-height:1">Pli</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.75;color:rgba(226,221,209,.62)">Le sommet de l'onde. La séquence est complète, la page ne l'est pas.</p>
                    </div>
                    <div data-fold="0.67" data-fold-win="0.24" style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:16px;opacity:0;will-change:transform,opacity">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">État 03</span>
                        <h2 style="margin:0;font-size:clamp(30px,5.4vw,78px);font-weight:200;letter-spacing:-.04em;line-height:1">Retour</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.75;color:rgba(226,221,209,.62)">Les mêmes états, dans l'ordre inverse, avec les entrées retournées.</p>
                    </div>
                    <div data-fold="1" data-fold-win="0.24" style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;gap:16px;opacity:0;will-change:transform,opacity">
                        <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">État 04</span>
                        <h2 style="margin:0;font-size:clamp(30px,5.4vw,78px);font-weight:200;letter-spacing:-.04em;line-height:1">Sortie</h2>
                        <p style="margin:0;max-width:44ch;font-size:15px;line-height:1.75;color:rgba(226,221,209,.62)">On quitte la section par où l'on est entré, sans jamais remonter.</p>
                    </div>
                </div>

                <div style="position:absolute;right:clamp(30px,5vw,70px);bottom:clamp(30px,5vh,60px);display:flex;flex-direction:column;align-items:flex-end;gap:8px;pointer-events:none">
                    <span data-fold-dir style="font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:#9184d9">Aller</span>
                    <span data-fold-val style="font-size:clamp(24px,3vw,40px);font-weight:200;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1">0.00</span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Une seule ligne de code fait le repli : la progression est convertie en onde triangulaire avant d'être distribuée aux éléments. Chaque état est déclaré une fois et se joue deux fois.</p>
            </div>
        </section>

        {{-- ==================================================== 15 --- --}}
        <section class="lab-sec" data-lab="fisheye" data-screen-label="Lab 15 Focale verticale">
            <div style="min-height:56vh;display:flex;flex-direction:column;justify-content:flex-end;gap:18px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,90px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Focale verticale</h1>
                <p data-reveal="up" style="margin:0;max-width:54ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Rien ne défile. Le document tient entièrement dans l'écran et le scroll ne déplace que le point de lecture : la bande visée s'ouvre, les autres se compriment en filets. La hauteur totale reste constante parce que les hauteurs sont normalisées entre elles.</p>
            </div>

            <div data-focal style="position:relative;height:100vh;overflow:hidden;background:radial-gradient(80% 60% at 20% 50%,#0b0c14,#05050a 78%);border-top:1px solid rgba(233,233,237,.08)">
                <div data-bands style="position:absolute;inset:clamp(24px,5vh,54px) clamp(30px,6vw,90px);display:flex;flex-direction:column">
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">01</span>Repérage</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">Les contraintes réelles avant le dessin : supports, volumes de contenu, délais de production.</p>
                        </div>
                    </article>
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">02</span>Structure</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">Grille, échelle typographique et rythme vertical fixés une fois, réutilisés partout.</p>
                        </div>
                    </article>
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">03</span>Matière</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">Lumière, masques et vitesses différentielles donnent la profondeur sans recourir à la 3D.</p>
                        </div>
                    </article>
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">04</span>Vitesse</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">Un budget par frame est fixé avant l'animation : ce qui ne tient pas dedans n'est pas retenu.</p>
                        </div>
                    </article>
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">05</span>Seuil</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">Chaque système a un mode réduit déclaré, pas un mode réduit improvisé au moment du port.</p>
                        </div>
                    </article>
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">06</span>Densité</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">L'espace se règle par la lecture, pas par la décoration : ici la hauteur elle-même est l'information.</p>
                        </div>
                    </article>
                    <article data-band style="flex:none;position:relative;overflow:hidden;border-top:1px solid rgba(233,233,237,.10);border-bottom:1px solid rgba(233,233,237,.10)">
                        <div style="display:flex;flex-direction:column;gap:10px;padding:12px 0">
                            <span data-band-head style="display:flex;gap:18px;align-items:baseline;font-size:clamp(18px,2.2vw,32px);font-weight:200;letter-spacing:-.02em;line-height:1.1"><span style="font-size:10px;letter-spacing:.24em;color:#9184d9">07</span>Livraison</span>
                            <p data-band-body style="margin:0;max-width:64ch;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62);opacity:0">Chaque système est isolé pour être remonté sur une nouvelle page sans copier de code.</p>
                        </div>
                    </article>
                </div>

                <div style="position:absolute;right:clamp(30px,5vw,70px);bottom:clamp(24px,4vh,44px);display:flex;align-items:baseline;gap:16px;pointer-events:none">
                    <span style="font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.34)">Focale</span>
                    <span data-focal-name style="font-size:12px;letter-spacing:.14em;color:#b3a9e6">Repérage</span>
                    <span data-focal-val style="font-size:clamp(20px,2.4vw,32px);font-weight:200;font-variant-numeric:tabular-nums;line-height:1">01</span>
                </div>
            </div>

            <div style="min-height:48vh;display:flex;align-items:center;padding:0 clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08)">
                <p data-reveal="up" style="margin:0;max-width:48ch;font-size:14px;line-height:1.75;color:rgba(226,221,209,.45)">Les hauteurs sont réparties en proportion d'une gaussienne, jamais mesurées sur le rendu obtenu : la mise en page ne peut donc pas s'auto-alimenter et osciller.</p>
            </div>
        </section>

        {{-- ================================================ Catalogue --- --}}
        <section class="lab-sec" data-lab="catalogue" data-screen-label="Lab IX Catalogue">
            <div style="min-height:52vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
                <span data-reveal="line" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 data-chars style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">Catalogue</h1>
                <p data-reveal="up" style="margin:0;max-width:56ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">Quinze mécaniques de scroll inventées pour ce laboratoire, au-delà des six systèmes de base. Neuf sont prototypées et navigables ; six restent à l'état de fiche. Chaque fiche indique ce que le scroll pilote, ce que l'on voit, la difficulté réelle et l'intérêt pour le portfolio.</p>
            </div>

            <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));padding:0 clamp(56px,9vw,180px) clamp(60px,8vw,120px)">
                @foreach ($concepts as $c)
                    <article style="display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55)">
                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                            <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ $c['tag'] }}</span>
                            <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)">{{ $c['status'] }}</span>
                        </div>
                        <h3 style="margin:0;font-size:clamp(20px,2.2vw,28px);font-weight:200;letter-spacing:-.02em;line-height:1.1">{{ $c['name'] }}</h3>
                        <p style="margin:0;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62)">{{ $c['desc'] }}</p>
                        <div style="display:flex;flex-direction:column;gap:9px;padding-top:14px;border-top:1px solid rgba(233,233,237,.08)">
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)">Scroll</span><span style="color:rgba(226,221,209,.72)">{{ $c['scroll'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)">Vu</span><span style="color:rgba(226,221,209,.72)">{{ $c['seen'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)">Tech</span><span style="color:rgba(226,221,209,.72)">{{ $c['tech'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)">Coût</span><span style="color:rgba(226,221,209,.72)">{{ $c['cost'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)">Portfolio</span><span style="color:rgba(226,221,209,.72)">{{ $c['use'] }}</span></span>
                        </div>
                        @if ($c['built'] !== null)
                            <button data-cta="{{ $c['built'] }}" class="lab-cta" style="appearance:none;margin-top:6px;align-self:flex-start;background:none;border:1px solid rgba(145,132,217,.5);color:#b3a9e6;cursor:pointer;font-family:inherit;font-size:10px;letter-spacing:.22em;text-transform:uppercase;padding:9px 16px;border-radius:8px;transition:background .3s,color .3s">Ouvrir {{ str_pad($c['built'] + 1, 2, '0', STR_PAD_LEFT) }}</button>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

    </main>

    <footer style="position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:12px 32px;justify-content:space-between;padding:clamp(40px,6vw,80px) clamp(56px,9vw,180px);border-top:1px solid rgba(233,233,237,.08);font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.34)">
        <span>Lenis + GSAP ScrollTrigger</span>
        <span>Touches 1 – 9 · 0 · I · ← →</span>
        <span>Laravel · Lakeust Works</span>
    </footer>
</div>
</body>
</html>
