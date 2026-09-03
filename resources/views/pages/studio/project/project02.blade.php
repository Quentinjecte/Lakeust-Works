@extends('layouts.site')

@section('title', "Novum : la terre d'après — Lakeust Works")

@section('content')
<head>
    {{-- resources/js/pages/project.js n'est plus une entrée Vite statique :
         Barba l'importe à la demande en entrant dans un namespace "*.project*"
         (voir isProjectPage() dans resources/js/core/barba-transitions.js —
         cette fiche studio.project02 était jusqu'ici hors de ce filtre, donc
         data-env-stage/data-corridor-stage/data-braid n'y tournaient jamais). --}}

    @vite(['resources/css/app.css', 'resources/css/web.css', 'resources/js/ui/carousel.js'])
    @section('cat', 'Studio')

    @php
        /* Contrairement à Jurassic Containment, Novum n'a pas une bibliothèque
           de captures « monde » aussi large (pas de variantes hq-… ou env-…
           par lieu) : cette fiche s'appuie donc davantage sur le texte —
           voir $journal, tiré directement du lore fourni — et moins sur des
           galeries d'images répétées. Les catégories d'arsenal/consommables
           reflètent les vrais assets présents dans public/images/novum
           (armes, modifications, kit médical), pas un inventaire inventé. */
        $img = fn ($name) => asset('images/novum/' . rawurlencode($name));

        $survieAttrs = [
            ['fr' => 'Ressources', 'i18n' => 'project02.survie.ressources'],
            ['fr' => 'Systèmes vitaux', 'i18n' => 'project02.survie.systemes'],
            ['fr' => 'Sorties extérieures', 'i18n' => 'project02.survie.sorties'],
            ['fr' => 'Anomalies', 'i18n' => 'project02.survie.anomalies'],
            ['fr' => 'Journal de bord', 'i18n' => 'project02.survie.journal'],
        ];

        $devAxes = [
            ['n' => '01', 'titre' => 'Gameplay', 'i18n' => 'project02.dev.gameplay', 'fr' => 'Développer la boucle bunker ↔ sorties extérieures et la gestion des ressources.'],
            ['n' => '02', 'titre' => 'World', 'i18n' => 'project02.dev.world', 'fr' => "Construire le cycle jour/nuit et les zones extérieures explorables."],
            ['n' => '03', 'titre' => 'Threat', 'i18n' => 'project02.dev.threat', 'fr' => "Définir la nature de ce qui rôde dehors — encore volontairement flou côté design."],
            ['n' => '04', 'titre' => 'Systems', 'i18n' => 'project02.dev.systems', 'fr' => "Construire le craft médical, l'entretien du bunker et la narration par journal."],
        ];

        $arsenal = [
            ['cat' => 'Armes', 'i18n' => 'project02.arsenal.weapons', 'items' => [
                ['src' => $img('AK-74N.png'), 'name' => 'AK-74N'],
                ['src' => $img('AN94.png'), 'name' => 'AN-94'],
                ['src' => $img('Couteau.png'), 'name' => 'Couteau'],
            ]],
            ['cat' => 'Modifications', 'i18n' => 'project02.arsenal.mods', 'items' => [
                ['src' => $img('Holo.png'), 'name' => 'Viseur holographique'],
                ['src' => $img('RedDot.png'), 'name' => 'Point rouge'],
                ['src' => $img('Trijicon.png'), 'name' => 'Trijicon'],
                ['src' => $img('1P29.png'), 'name' => '1P29'],
                ['src' => $img('NiperScope.png'), 'name' => 'Lunette longue distance'],
                ['src' => $img('GripVertical.png'), 'name' => 'Poignée verticale'],
                ['src' => $img('GripDiagonal.png'), 'name' => 'Poignée diagonale'],
                ['src' => $img('AKSilencer.png'), 'name' => 'Silencieux AK'],
                ['src' => $img('mk18Silencer.png'), 'name' => 'Silencieux Mk18'],
            ]],
            ['cat' => 'Kit médical', 'i18n' => 'project02.arsenal.medkit', 'items' => [
                ['src' => $img('Syringe_Metal.png'), 'name' => 'Seringue'],
                ['src' => $img('Vaccine_Blue.png'), 'name' => 'Vaccin — bleu'],
                ['src' => $img('Vaccine_Green.png'), 'name' => 'Vaccin — vert'],
                ['src' => $img('Vaccine_Purple.png'), 'name' => 'Vaccin — violet'],
                ['src' => $img('Pills_Red.png'), 'name' => 'Cachets — rouge'],
                ['src' => $img('Herb_GreenPot.png'), 'name' => 'Herbe cultivée — verte'],
            ]],
        ];

        /* Trois spécimens de flore mutée, chacun lié à un élément extrême —
           images générées fournies directement pour Aranox (glace) et Icarus
           (feu, ex-"Icanox"), Electa (électrique) réutilise son asset existant. */
        $flore = [
            ['nom' => 'Aranox', 'i18n' => 'project02.flore.aranox', 'img' => $img('Aranox.png'),
                'element' => ['fr' => 'Glace', 'en' => 'Ice'],
                'fr' => "Ses feuilles cristallines restent gelées même en plein été — elle capte la chaleur ambiante pour alimenter un noyau de glace qui ne fond jamais. Là où elle pousse, le sol reste anormalement froid sur plusieurs mètres.",
                'en' => "Its crystalline leaves stay frozen even in midsummer — it draws ambient heat to feed a core of ice that never melts. Wherever it grows, the ground stays abnormally cold for several meters around it."],
            ['nom' => 'Icarus', 'i18n' => 'project02.flore.icarus', 'img' => $img('Icarus.png'),
                'element' => ['fr' => 'Feu', 'en' => 'Fire'],
                'fr' => "Une combustion interne continue anime ses feuilles sans jamais les consumer — une réaction chimique que personne n'a encore réussi à expliquer. Sa chaleur repousse les prédateurs, mais attire tout aussi sûrement les curieux.",
                'en' => "A continuous internal combustion animates its leaves without ever consuming them — a chemical reaction no one has managed to explain yet. Its heat keeps predators away, but just as surely draws in the curious."],
            ['nom' => 'Electa', 'i18n' => 'project02.flore.electa', 'img' => $img('Plante Electa.png'),
                'element' => ['fr' => 'Électrique', 'en' => 'Electric'],
                'fr' => "Un léger crépitement accompagne chacun de ses mouvements : elle accumule une charge électrique qu'elle relâche par décharges courtes, sans logique apparente. Approcher sans précaution revient à s'exposer à une décharge bien réelle.",
                'en' => "A faint crackle accompanies its every movement: it stores an electrical charge it releases in short, seemingly random bursts. Approaching it without care means exposing yourself to a very real shock."],
        ];

        $gallery = [
            ['src' => $img('jour.png'), 'cap' => 'Extérieur — jour', 'cat' => 'ENVIRONMENT'],
            ['src' => $img('nuit.png'), 'cap' => 'Extérieur — nuit', 'cat' => 'ENVIRONMENT'],
            ['src' => $img('invasion-zombie-1.png'), 'cap' => 'Menace en approche', 'cat' => 'THREAT'],
            ['src' => $img('invasion-zombie-2.png'), 'cap' => 'Menace — vue rapprochée', 'cat' => 'THREAT'],
            ['src' => $img('Plante Electa.png'), 'cap' => 'Flore mutée — Electa', 'cat' => 'FIELD'],
            ['src' => $img('Aranox.png'), 'cap' => 'Flore mutée — Aranox', 'cat' => 'FIELD'],
            ['src' => $img('Icarus.png'), 'cap' => 'Flore mutée — Icarus', 'cat' => 'FIELD'],
            ['src' => $img('sortie-bunker.jpg'), 'cap' => 'Sortie — camp de ravitaillement', 'cat' => 'FIELD'],
            ['src' => $img('arme-modification-ui.jpg'), 'cap' => "Modification d'arme — interface", 'cat' => 'SYSTEMS'],
            ['src' => $img('arme-modifiee-test.jpg'), 'cap' => 'Arme modifiée — essai au tir', 'cat' => 'SYSTEMS'],
            ['src' => $img('confrontation-rapprochee.jpg'), 'cap' => 'Confrontation rapprochée', 'cat' => 'THREAT'],
            ['src' => $img('vigie-distance.jpg'), 'cap' => 'Vigie à distance', 'cat' => 'THREAT'],
            ['src' => $img('background&title.png'), 'cap' => 'Titre', 'cat' => 'TITLE'],
        ];

        /* Extraits choisis du lore fourni, dans l'ordre chronologique du
           journal — la colonne vertébrale narrative de cette fiche. */
        $journal = [
            ['n' => 'J +5', 'i18n' => 'project02.journal.0',
                'fr' => "Une unité de reconnaissance est envoyée à l'extérieur. Depuis leur départ : plus rien. Aucun signal, aucune transmission. Dernier contact.",
                'en' => "A recon unit is sent outside. Since they left: nothing. No signal, no transmission. Last contact."],
            ['n' => 'J +8', 'i18n' => 'project02.journal.1',
                'fr' => "La lumière est réapparue cette nuit, plus proche. Des traces étranges près du périmètre — comme si quelque chose avait glissé, ou rampé. Aucune trace de l'unité disparue.",
                'en' => "The light came back tonight, closer. Strange marks near the perimeter — as if something had slid, or crawled. No trace of the missing unit."],
            ['n' => 'J +365', 'i18n' => 'project02.journal.2',
                'fr' => "Un an aujourd'hui. Pour la première fois depuis longtemps, une vraie nouvelle : le générateur secondaire a redémarré seul, sans intervention. Personne ne comprend comment.",
                'en' => "One year today. For the first time in a long while, real news: the backup generator restarted on its own, with no intervention. No one understands how."],
            ['n' => 'J +1000', 'i18n' => 'project02.journal.3',
                'fr' => "Un nouveau marquage est apparu sur une porte du module de maintenance — gravé dans le métal, sans outil identifié. Il ne correspond à aucune langue connue.",
                'en' => "A new mark has appeared on a maintenance module door — carved into the metal, with no identified tool. It matches no known language."],
            ['n' => 'J +2000', 'i18n' => 'project02.journal.4',
                'fr' => "Une silhouette humanoïde, immobile à l'horizon. Deux drones envoyés en reconnaissance reviennent endommagés avant d'avoir franchi cinquante mètres. La forme n'a pas bougé. Mais elle nous observe.",
                'en' => "A humanoid shape, motionless on the horizon. Two drones sent to scout return damaged before covering fifty meters. The shape hasn't moved. But it's watching us."],
            ['n' => 'J +2200', 'i18n' => 'project02.journal.5',
                'fr' => "Une brèche s'ouvre dans le blindage. Après l'engagement, une créature immense est repérée à la limite de la brèche — elle porte le badge de l'unité disparue sept ans plus tôt.",
                'en' => "A breach opens in the armor plating. After the engagement, an immense creature is spotted at the edge of the breach — it's wearing the badge of the unit that vanished seven years earlier."],
            ['n' => 'J +2500', 'i18n' => 'project02.journal.6',
                'fr' => "Sept ans après l'impact. La porte du bunker grince. Le ciel est dégagé, calme, comme si rien ne s'était passé. « Ce journal s'arrête ici. À partir de maintenant, c'est ton équipe et toi qui jouez. »",
                'en' => "Seven years after the impact. The bunker door creaks open. The sky is clear, calm, as if nothing had happened. \"This journal ends here. From now on, it's you and your team playing.\""],
        ];

        $nav = [
            ['route' => 'studio.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'studio.works',   'i18n' => 'nav.games',  'keep' => true],
            ['route' => 'studio.lab',   'i18n' => 'nav.lab',  'keep' => true],
        ];
    @endphp
</head>

    {{-- ==================================================================
         HERO
    =================================================================== --}}
    <section style="position:relative;height:92vh;min-height:600px;overflow:hidden;">
        <div class="parallax" data-parallax="50" style="position:absolute;inset:0;">
            <img src="{{ $img('background.png') }}" alt="Vue extérieure hostile depuis le bunker"
                 class="sc-unzoom" data-scrub="0 1"
                 style="position:absolute;inset:-8%;width:calc(100% + 16%);height:calc(100% + 16%);object-fit:cover;">
        </div>
        <div style="position:absolute;inset:0;background:linear-gradient(180deg, rgba(5,5,10,.35), rgba(5,5,10,.25) 40%, rgba(5,5,10,.97));"></div>
        <div class="wrap" style="position:relative;height:100%;display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:var(--s-8);">
            <div data-reveal="mask">
                <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);" data-i18n="project02.hero.kicker">En développement — PC</span></span>
                <span class="mask-line"><h1 class="t-display">Novum : la terre d'après</h1></span>
            </div>
            <p class="t-lead" data-reveal="blur" data-reveal-delay="180" style="margin-top:var(--s-4);max-width:52ch;">
                <span data-i18n="project02.hero.lead">2034. Une météorite d'origine inconnue percute la Terre. Vous survivez depuis un bunker — jusqu'au jour où il faut enfin sortir.</span>
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:var(--s-4);align-items:center;margin-top:var(--s-6);" data-reveal="stagger" data-reveal-delay="320">
                <a class="btn" href="#projet"><span aria-hidden="true">▷</span> <span data-i18n="project02.hero.cta.discover">Découvrir le projet</span> <span class="arrow" aria-hidden="true">→</span></a>
                <a class="btn btn-ghost" href="#galerie" data-i18n="project.hero.cta.showcase">Voir le showcase</a>
                <span class="tag tag-accent">Unity 6</span>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         THE PROJECT
    =================================================================== --}}
    <section class="section-tight" id="projet">
        <div class="wrap split">
            <aside class="split-aside">
                <div style="display:flex;flex-direction:column;gap:var(--s-4);" data-reveal="stagger">
                    <div>
                        <span class="label" style="display:block;margin-bottom:4px;" data-i18n="project.meta.role">Rôle</span>
                        <span class="t-body" style="font-size:14px;" data-i18n="project.meta.role.value">Conception &amp; développement</span>
                    </div>
                    <div>
                        <span class="label" style="display:block;margin-bottom:4px;">Stack</span>
                        <span class="t-body" style="font-size:14px;">Unity 6 · C#</span>
                    </div>
                    <div>
                        <span class="label" style="display:block;margin-bottom:4px;" data-i18n="project.meta.status">Statut</span>
                        <span class="t-body" style="font-size:14px;" data-i18n="project02.meta.status.value">En pause — cadrage</span>
                    </div>
                    <span class="mark"></span>
                    <a class="btn btn-ghost" href="#galerie" style="align-self:flex-start;"><span data-i18n="project.meta.captures">Voir les captures</span> <span class="arrow" aria-hidden="true">→</span></a>
                </div>
            </aside>
            <div class="split-flow">
                <div>
                    <span class="label" data-reveal="rise" data-i18n="project02.hunt.kicker">Le monde d'après</span>
                    <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.hunt.title">Survivre. Attendre. Comprendre. Sortir.</h2>
                    <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.hunt.p1">
                        Novum est un jeu de survie vu depuis un bunker. Après l'impact d'une météorite aux
                        propriétés inconnues, vous et votre équipe tenez le fort : gestion des ressources,
                        maintien des systèmes vitaux, et le compte des jours qui s'accumulent.
                    </p>
                    <p class="t-body" data-reveal="blur" data-reveal-delay="80" data-i18n="project02.hunt.p2">
                        Le monde extérieur ne se raconte pas en une cinématique : il se découvre par le journal
                        de bord, entrée après entrée, jusqu'au jour où la porte du bunker s'ouvre enfin.
                    </p>
                </div>

                <div class="grid-3" data-reveal="stagger">
                    <div class="stat" data-count="2">
                        <span class="stat-value num" data-count-value>0</span>
                        <span class="stat-label" data-i18n="project02.stat.env">États environnement</span>
                    </div>
                    <div class="stat" data-count="7">
                        <span class="stat-value num" data-count-value>0</span>
                        <span class="stat-label" data-i18n="project02.stat.years">Ans avant la sortie</span>
                    </div>
                    <div class="stat" data-count="2500" data-plain="1">
                        <span class="stat-value num" data-count-value>0</span>
                        <span class="stat-label" data-i18n="project02.stat.journal">Jours de journal couverts</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         GAMEPLAY — assemblage géométrique
    =================================================================== --}}
    <section class="section">
        <div class="wrap">
            <div style="max-width:60ch;margin:0 auto var(--s-8);text-align:center;">
                <span class="label" data-reveal="rise">Gameplay</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.gameplay.title">Chaque silence cache quelque chose</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.gameplay.lead">
                    L'objectif immédiat est simple : tenir. Mais tenir demande de sortir — chercher des
                    ressources, réparer un système, comprendre une anomalie — et chaque sortie est un risque.
                </p>
            </div>

            <div style="display:grid;grid-template-columns:1fr minmax(240px,42%) 1fr;gap:clamp(16px,3vw,40px);align-items:center;">
                <div class="sc-in-left" data-scrub="0.05 0.7" data-scrub-ease="out" style="--amp:-84px;display:flex;flex-direction:column;gap:var(--s-3);align-items:flex-end;text-align:right;">
                    <span class="label" data-i18n="project02.relay.label">Panne système</span>
                    <span class="mark" data-reveal="line" style="width:100%;"></span>
                    <span class="t-body" style="font-size:13px;" data-i18n="project02.relay.desc">Aucune alerte, aucun rapport d'erreur — puis tout s'arrête, sans raison retrouvée dans les journaux.</span>
                </div>

                <div style="position:relative;aspect-ratio:1/1;">
                    <div class="sc-sharp" data-scrub="0.1 0.62" data-reveal="hex" style="--amp:9px;position:absolute;inset:0;">
                        <img src="{{ $img('invasion-zombie-1.png') }}" alt="Silhouette aperçue en périphérie du bunker" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <span class="label label-accent" data-reveal="rise" data-reveal-delay="620" style="position:absolute;left:50%;bottom:-26px;transform:translateX(-50%);white-space:nowrap;" data-i18n="project02.zone.compromised">Périmètre compromis</span>
                </div>

                <div class="sc-in-right" data-scrub="0.05 0.7" data-scrub-ease="out" style="--amp:84px;display:flex;flex-direction:column;gap:var(--s-3);">
                    <span class="label" data-i18n="project02.extraction.label">Sortie risquée</span>
                    <span class="mark" data-reveal="line" style="width:100%;"></span>
                    <span class="t-body" style="font-size:13px;" data-i18n="project02.extraction.desc">Chaque sortie extérieure peut tourner court — et ce qui en revient n'est pas toujours ce qui en est parti.</span>
                </div>
            </div>

            <div style="max-width:60ch;margin:var(--s-8) auto 0;text-align:center;">
                <h2 class="t-h2" data-reveal="mask" data-reveal-delay="420"><span class="mask-line"><span data-i18n="project02.gameplay.title2">Rien sur le papier ne l'explique.</span></span></h2>
                <p class="t-body" data-reveal="blur" data-reveal-delay="680" style="margin:var(--s-4) auto 0;" data-i18n="project02.gameplay.watch">
                    Vous montez la garde.
                </p>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         ENVIRONMENT — cycle jour / nuit (motion.js, 2 états — pas de
         troisième variante disponible pour Novum, contrairement à Jurassic)
    =================================================================== --}}
    <section class="section-tight">
        <div class="wrap">
            <div style="max-width:60ch;">
                <span class="label" data-reveal="rise" data-i18n="project02.env.label">Environnement</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.env.title">Un monde qui attend</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.env.lead">
                    Dehors, le jour et la nuit se succèdent sur un monde redevenu respirable — mais jamais
                    tout à fait vide.
                </p>
            </div>
        </div>

        {{-- data-env-label / data-env-caption sont écrasés par pages/project.js
             au fil du scroll (jour/nuit) — 2 couches ici, pas 3 : le
             brouillard STATES[2] de la maquette d'origine n'est simplement
             jamais atteint (n=2 plafonne la segmentation à l'index 1). --}}
        <div data-env-stage style="position:relative;height:100vh;overflow:hidden;margin-top:var(--s-7);background:#05050a;">
            <img data-env-layer src="{{ $img('jour.png') }}" alt="Extérieur de jour" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:1;">
            <img data-env-layer src="{{ $img('nuit.png') }}" alt="Extérieur de nuit" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;">

            <div style="position:absolute;inset:0;background:linear-gradient(0deg, rgba(5,5,10,.85), transparent 40%);"></div>

            <div style="position:absolute;left:var(--gutter);bottom:var(--s-7);right:var(--gutter);display:flex;flex-direction:column;gap:var(--s-3);">
                <span data-env-label class="label label-accent" style="font-size:12px;letter-spacing:.28em;">JOUR</span>
                <p data-env-caption class="t-body" style="max-width:46ch;font-size:15px;">
                    L'air est pur, le sol stable — les instruments ne détectent plus d'activité radioactive.
                </p>
            </div>

            <div data-env-progress style="position:absolute;left:0;bottom:0;height:2px;width:100%;background:rgba(233,233,237,.10);">
                <span style="display:block;height:100%;width:100%;transform:scaleX(0);transform-origin:0 50%;background:linear-gradient(90deg,var(--accent-2),var(--accent));"></span>
            </div>
        </div>

        <div class="wrap" style="margin-top:var(--s-7);text-align:center;">
            <p class="t-lead" data-reveal="up" data-i18n="project02.env.tagline">Le silence dehors n'a jamais vraiment changé.</p>
        </div>
    </section>

    {{-- ==================================================================
         ATMOSPHERE
    =================================================================== --}}
    <section class="section" style="position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;">
            <img src="{{ $img('nuit.png') }}" alt="" style="width:100%;height:100%;object-fit:cover;opacity:.28;">
            <div style="position:absolute;inset:0;background:linear-gradient(180deg, var(--bg) 0%, rgba(5,5,10,.55) 30%, var(--bg) 100%);"></div>
        </div>
        <div class="wrap" style="position:relative;max-width:64ch;text-align:center;margin-inline:auto;">
            <span class="label" data-reveal="rise" data-i18n="project.atmo.kicker">Atmosphère</span>
            <h2 class="t-h1" data-reveal="mask" style="margin-top:var(--s-3);">
                <span class="mask-line"><span data-i18n="project02.atmo.title">Vous n'êtes plus seul</span></span>
            </h2>
            <p class="t-lead" data-reveal="blur" data-reveal-delay="160" style="margin-top:var(--s-5);" data-i18n="project02.atmo.p1">
                Une lumière pâle réapparaît la nuit, toujours plus proche. Personne ne veut en parler
                ouvertement — mais plusieurs l'ont vue.
            </p>
            <p class="t-body" data-reveal="blur" data-reveal-delay="240" style="margin-top:var(--s-3);" data-i18n="project02.atmo.p2">
                Novum construit sa tension par accumulation : des pannes sans explication, des marques dans le
                métal, une silhouette qui n'approche jamais tout à fait — mais qui ne s'éloigne pas non plus.
            </p>
        </div>
    </section>

    {{-- ==================================================================
         JOURNAL — extraits du lore fourni, en remplacement du corridor HQ
         de Jurassic (qui suppose cinq visuels d'intérieur distincts que
         Novum n'a pas encore) : ici, le texte porte le "monde" à la place
         des images.
    =================================================================== --}}
    <section class="section-tight" id="journal">
        <div class="wrap">
            <span class="label" data-reveal="rise" style="display:block;margin-bottom:var(--s-3);">Journal</span>
            <h2 class="t-h1" data-reveal="mask"><span class="mask-line"><span data-i18n="project02.journal.title">Sept ans, un bunker.</span></span></h2>
            <p class="t-body" data-reveal="blur" style="margin-top:var(--s-4);max-width:60ch;" data-i18n="project02.journal.lead">
                Le monde de Novum se raconte moins en captures qu'en notes de journal — celles tenues par
                l'équipe du bunker, jour après jour, jusqu'à la sortie.
            </p>

            <div style="display:flex;flex-direction:column;margin-top:var(--s-7);border-top:1px solid var(--line);" data-reveal="stagger">
                @foreach ($journal as $entry)
                    <div style="display:grid;grid-template-columns:minmax(90px,120px) minmax(0,1fr);gap:var(--s-5);padding:var(--s-5) 0;border-bottom:1px solid var(--line);align-items:baseline;">
                        <span class="label label-accent" style="font-family:var(--font-heading);font-size:13px;letter-spacing:.16em;">{{ $entry['n'] }}</span>
                        <p class="t-body" style="margin:0;font-size:14.5px;line-height:1.75;color:var(--text-2);" data-i18n="{{ $entry['i18n'] }}">{{ $entry['fr'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================================================================
         LE BUNKER — remplace "Prepare your team" (pas de captures d'intérieur
         type QG pour Novum : le bunker se décrit par ses systèmes, pas par
         une photo de local)
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div>
                <span class="label" data-reveal="rise" data-i18n="project02.prep.label">Le bunker</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.prep.title">Le foyer, c'est ce qui fonctionne encore</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.prep.p1">
                    Recyclage d'air, générateur, éclairage de secours : le bunker n'est pas un décor, c'est un
                    système qu'il faut entretenir. Une panne n'est jamais juste cosmétique.
                </p>
                <p class="t-body" data-reveal="blur" data-reveal-delay="80" style="margin-top:var(--s-3);" data-i18n="project02.prep.p2">
                    Le craft médical — herbes cultivées, cachets, vaccins — occupe une place centrale : soigner
                    l'équipe compte autant que l'armer pour une sortie.
                </p>
                <p class="t-lead" data-reveal="up" data-reveal-delay="160" style="margin-top:var(--s-5);" data-i18n="project02.prep.tagline">
                    Gardez les lumières allumées. Gardez l'équipe en vie.
                </p>
            </div>
            <div class="media media-4-3 media-hover" data-reveal="frame">
                <img src="{{ $img('Herb_GreenPot.png') }}" alt="Herbe cultivée en pot, ressource médicale" style="object-fit:contain;background:#000;padding:var(--s-6);">
            </div>
        </div>
    </section>

    {{-- ==================================================================
         ARSENAL
    =================================================================== --}}
    <section class="section" id="arsenal">
        <div class="wrap">
            <div style="max-width:60ch;margin-bottom:var(--s-7);">
                <span class="label" data-reveal="rise">Arsenal</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.arsenal.title">Équipez-vous pour l'extérieur</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.arsenal.lead">
                    Une arme, des modifications adaptées à la sortie prévue, et un kit médical — l'équipement se
                    pense pour revenir, pas seulement pour partir.
                </p>
            </div>

            @foreach ($arsenal as $group)
                <div style="margin-top:var(--s-7);">
                    <span class="label label-accent" data-reveal="rise" style="display:block;margin-bottom:var(--s-4);" data-i18n="{{ $group['i18n'] }}">{{ $group['cat'] }}</span>
                    <div class="grid-3" data-reveal="stagger">
                        @foreach ($group['items'] as $item)
                            <div class="media media-4-3" style="background:#000;">
                                <img src="{{ $item['src'] }}" alt="{{ $item['name'] }}" style="object-fit:contain;padding:var(--s-4);">
                                <span class="media-cap label">{{ $item['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ==================================================================
         SYSTÈMES DE SURVIE — remplace "Mission system"
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div class="media media-4-3 media-hover" data-reveal="frame" style="order:2;background:#000;">
                <img src="{{ $img('Vaccine_Blue.png') }}" alt="Vaccin de synthèse, ressource rare" style="object-fit:contain;padding:var(--s-6);">
            </div>
            <div style="order:1;">
                <span class="label" data-reveal="rise" data-i18n="project02.mission.kicker">Systèmes de survie</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.mission.title">Chaque entrée est une décision</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.mission.lead">
                    Le journal de bord n'est pas un simple récap narratif : il consigne l'état du bunker,
                    guide les priorités, et révèle progressivement ce qui se passe dehors.
                </p>
                <span class="t-body" style="display:block;margin-top:var(--s-4);font-size:13px;color:var(--text-3);" data-i18n="project02.mission.each">Chaque jour de jeu suit :</span>
                <div style="display:flex;flex-wrap:wrap;gap:var(--s-2);margin-top:var(--s-3);" data-reveal="stagger">
                    @foreach ($survieAttrs as $attr)
                        <span class="tag" data-i18n="{{ $attr['i18n'] }}">{{ $attr['fr'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         CE QUI RÔDE DEHORS — remplace "The dinosaurs" (2 visuels de menace
         disponibles, pas 3 : présentation simple plutôt que de forcer
         l'entrelacs à trois brins sur deux images)
    =================================================================== --}}
    <section class="section-tight" style="text-align:center;">
        <div class="wrap" style="max-width:56ch;margin-inline:auto;">
            <span class="label" data-reveal="rise" data-i18n="project02.threat.kicker">Ce qui rôde dehors</span>
            <h2 class="t-h1" data-reveal="mask" style="margin-top:var(--s-3);"><span class="mask-line"><span data-i18n="project02.threat.title">Ça observe. Ça n'approche pas.</span></span></h2>
            <p class="t-body" data-reveal="blur" data-reveal-delay="120" style="margin-top:var(--s-5);" data-i18n="project02.threat.lead">
                Une silhouette humanoïde, immobile à l'horizon — trop loin pour être identifiée, trop régulière
                pour être un hasard. Les drones envoyés en reconnaissance reviennent endommagés avant d'avoir
                franchi cinquante mètres.
            </p>
        </div>

        <div class="wrap" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:var(--s-6);margin-top:var(--s-8);">
            <div class="media media-4-3 media-hover" data-reveal="frame">
                <img src="{{ $img('invasion-zombie-1.png') }}" alt="Silhouette aperçue à l'horizon">
            </div>
            <div class="media media-4-3 media-hover" data-reveal="frame" data-reveal-delay="80">
                <img src="{{ $img('invasion-zombie-2.png') }}" alt="Menace observée de plus près">
            </div>
        </div>

        <div class="wrap" style="max-width:56ch;margin:var(--s-7) auto 0;">
            <p class="t-lead" data-reveal="up" data-i18n="project02.threat.tagline">
                Observer. Attendre. Ne pas détourner le regard.
            </p>
        </div>
    </section>

    {{-- ==================================================================
         FLORE MUTÉE — trois spécimens, chacun adapté à un extrême
    =================================================================== --}}
    <section class="section" id="flore">
        <div class="wrap">
            <div style="max-width:60ch;margin-bottom:var(--s-7);">
                <span class="label" data-reveal="rise" data-i18n="project02.flore.kicker">Flore mutée</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.flore.title">Trois extrêmes, trois adaptations</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.flore.lead">
                    Chaque spécimen documenté vit une propriété unique liée à un phénomène que rien n'explique
                    encore — une adaptation à des conditions que la vie ne devrait pas pouvoir supporter.
                </p>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:var(--s-6);" data-reveal="stagger">
                @foreach ($flore as $plante)
                    <div style="display:flex;flex-direction:column;gap:var(--s-4);border-radius:var(--radius-lg,14px);overflow:hidden;background:rgba(35,37,50,.6);box-shadow:var(--shadow-sm);">
                        <div class="media media-4-3">
                            <img src="{{ $plante['img'] }}" alt="{{ $plante['nom'] }}">
                        </div>
                        <div style="padding:0 var(--s-6) var(--s-6);">
                            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin-bottom:var(--s-3);">
                                <span style="font-size:20px;letter-spacing:-.01em;">{{ $plante['nom'] }}</span>
                                <span class="tag tag-accent" data-i18n="{{ $plante['i18n'] }}.element">{{ $plante['element']['fr'] }}</span>
                            </div>
                            <p class="t-body" style="margin:0;font-size:14px;line-height:1.65;color:var(--text-3);" data-i18n="{{ $plante['i18n'] }}.desc">{{ $plante['fr'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================================================================
         L'ÉQUIPE DU BUNKER — remplace "Co-op"
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div class="media media-4-3 media-hover" data-reveal="frame">
                <img src="{{ $img('jour.png') }}" alt="Vue extérieure depuis le périmètre du bunker">
            </div>
            <div>
                <span class="label" data-reveal="rise" data-i18n="project02.coop.kicker">L'équipe du bunker</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project02.coop.title"></h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project02.coop.p1">
                    Ingénieurs, scientifiques, section de sécurité : chacun tient son rôle dans le bunker, et
                    chaque décision extérieure se discute en équipe avant d'être prise.
                </p>
                <p class="t-body" data-reveal="blur" data-reveal-delay="80" style="margin-top:var(--s-3);" data-i18n="project02.coop.p2">
                    Le silence pèse différemment sur chacun. Certains cessent d'écrire. D'autres continuent —
                    parce que c'est ce qu'ils savent faire : observer, noter, comprendre.
                </p>
                <p class="t-lead" data-reveal="up" data-reveal-delay="160" style="margin-top:var(--s-5);" data-i18n="project02.coop.tagline">
                    Un bunker. Un journal. Sept ans.
                </p>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         BANDE-ANNONCE — vraie capture vidéo disponible pour Novum
    =================================================================== --}}
    <section class="section">
        <div class="wrap">
            <div style="display:flex;align-items:center;gap:var(--s-4);margin-bottom:var(--s-5);">
                <span class="label" data-reveal="rise" data-i18n="project02.trailer.label">Bande-annonce</span>
                <span class="mark" data-reveal="line" style="flex:1;"></span>
            </div>
            <div data-reveal="frame" style="position:relative;aspect-ratio:16/9;border-radius:var(--radius-lg,14px);overflow:hidden;background:#000;box-shadow:var(--shadow-sm);">
                <video src="{{ $img('Novum.mp4') }}" controls preload="metadata" playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         SCREENSHOT GALLERY — carousel
    =================================================================== --}}
    <section class="section" id="galerie">
        <div class="wrap">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:var(--s-4);margin-bottom:var(--s-5);">
                <div data-reveal="mask">
                    <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);" data-i18n="project.gallery.kicker">Rapports de terrain</span></span>
                    <span class="mask-line"><h2 class="t-h2" data-i18n="project.gallery.title">Galerie</h2></span>
                </div>
                <span class="label" data-reveal="rise" data-i18n="project.gallery.hint">Glisser · flèches · ← →</span>
            </div>

            <div class="carousel" data-carousel data-mode="perspective" data-autoplay="6500" aria-roledescription="carousel">
                <div class="carousel-viewport" data-carousel-viewport aria-label="Galerie de Novum : la terre d'après">
                    @foreach ($gallery as $n => $slide)
                        <figure class="carousel-slide">
                            <div class="media media-16-9"><img src="{{ $slide['src'] }}" alt="{{ $slide['cap'] }}"></div>
                            <figcaption class="slide-caption label" data-i18n="project02.gallery.slide.{{ $n }}">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }} — {{ $slide['cat'] }} · {{ $slide['cap'] }}</figcaption>
                        </figure>
                    @endforeach
                </div>

                <div class="carousel-ui">
                    <div class="carousel-dots" role="tablist" aria-label="Plans">
                        @foreach ($gallery as $n => $slide)
                            <button class="carousel-dot" role="tab" aria-selected="{{ $n === 0 ? 'true' : 'false' }}" aria-label="Capture {{ $n + 1 }}"></button>
                        @endforeach
                    </div>
                    <div style="display:flex;align-items:center;gap:var(--s-4);">
                        <span class="carousel-index num" data-carousel-index>01 / {{ str_pad(count($gallery), 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="carousel-arrows">
                            <button class="carousel-arrow" data-carousel-prev aria-label="Capture précédente">←</button>
                            <button class="carousel-arrow" data-carousel-next aria-label="Capture suivante">→</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         DEVELOPMENT
    =================================================================== --}}
    <section class="section-tight">
        <div class="wrap">
            <span class="label" data-reveal="rise" data-i18n="project.dev.kicker">Développement</span>
            <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);max-width:40ch;" data-i18n="project02.dev.title">Actuellement en pré-production</h2>
            <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);max-width:56ch;" data-i18n="project02.dev.lead">
                Novum : la terre d'après est en phase de cadrage. Le projet avance autour de plusieurs axes :
            </p>
            <div class="grid-2" style="margin-top:var(--s-6);" data-reveal="stagger">
                @foreach ($devAxes as $axe)
                    <div class="card">
                        <span class="card-kicker">{{ $axe['n'] }}</span>
                        <span class="card-title">{{ $axe['titre'] }}</span>
                        <span class="card-body" data-i18n="{{ $axe['i18n'] }}">{{ $axe['fr'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================================================================
         CTA
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:flex;flex-direction:column;align-items:flex-start;gap:var(--s-5);">
            <hr class="rule" data-reveal="line" style="width:100%;">
            <div data-reveal="mask">
                <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);" data-i18n="project02.cta.kicker">Le monde est calme. Ça ne veut rien dire.</span></span>
                <span class="mask-line"><h2 class="t-h1" data-i18n="project02.cta.title">La porte est sur le point de s'ouvrir</h2></span>
            </div>
            <p class="t-body" data-reveal="blur" data-reveal-delay="120" data-i18n="project02.cta.status">
                Novum : la terre d'après est actuellement en pré-production.
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:var(--s-3);" data-reveal="stagger">
                <a class="btn" href="{{ route('studio.works') }}"><span data-i18n="project.cta.allworks">Tous les travaux</span> <span class="arrow" aria-hidden="true">→</span></a>
                <a class="btn btn-ghost" href="{{ route('studio.about') }}#contact" data-i18n="nav.contact">Contact</a>
            </div>
        </div>
    </section>
@endsection
