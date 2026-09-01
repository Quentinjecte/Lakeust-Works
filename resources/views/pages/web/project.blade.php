@extends('layouts.site')

@section('title', 'Jurassic Containment — Lakeust Works')

@section('content')
<head>
    {{-- resources/js/pages/project.js n'est plus une entrée Vite statique :
         Barba l'importe à la demande en entrant dans le namespace "project",
         quel que soit le point d'entrée de la session (voir
         resources/js/core/barba-transitions.js). --}}

    @vite(['resources/css/app.css', 'resources/css/web.css', 'resources/js/ui/carousel.js'])

    {{-- i18n : les accroches courtes déjà en anglais ("Hunt. Contain. Survive.",
         "The mission doesn't go according to plan.", etc.) sont la signature
         de cette page — laissées identiques dans les deux langues, jamais
         wrapées en data-i18n. Seuls les paragraphes explicatifs français et
         les libellés d'interface sont traduits. Les légendes de la galerie et
         les libellés jour/nuit/brouillard (pilotés par pages/project.js)
         restent en français pour l'instant — pas encore convertis. --}}

    @php
        $img = fn ($name) => asset('images/jurassic-containment/' . $name);

        $missionAttrs = [
            ['fr' => 'Spécimens', 'i18n' => 'project.mission.specimens'],
            ['fr' => 'Objectifs', 'i18n' => 'project.mission.objectives'],
            ['fr' => 'Niveaux de danger', 'i18n' => 'project.mission.danger'],
            ['fr' => 'Récompenses', 'i18n' => 'project.mission.rewards'],
            ['fr' => "Conditions d'opération", 'i18n' => 'project.mission.conditions'],
        ];

        $devAxes = [
            ['n' => '01', 'titre' => 'Gameplay', 'i18n' => 'project.dev.gameplay', 'fr' => 'Développer les mécaniques de chasse, de capture et de coopération.'],
            ['n' => '02', 'titre' => 'World', 'i18n' => 'project.dev.world', 'fr' => "Construire différents environnements et zones d'opération."],
            ['n' => '03', 'titre' => 'Creatures', 'i18n' => 'project.dev.creatures', 'fr' => 'Développer des spécimens avec des comportements et des situations de chasse variés.'],
            ['n' => '04', 'titre' => 'Systems', 'i18n' => 'project.dev.systems', 'fr' => "Construire les systèmes de progression, d'équipement, de missions et de coopération."],
        ];

        $arsenal = [
            ['cat' => 'Armes', 'i18n' => 'project.arsenal.weapons', 'items' => [
                ['src' => $img('item-arme-remington.webp'), 'name' => 'Remington 11-87'],
                ['src' => $img('item-arme-srs-a2.webp'), 'name' => 'Desert Tech SRS A2'],
                ['src' => $img('item-arme-tr15.webp'), 'name' => 'TR-15'],
                ['src' => $img('item-arme-mk18.webp'), 'name' => 'Mk18'],
                ['src' => $img('item-arme-mk23.webp'), 'name' => 'Mk23'],
                ['src' => $img('item-arme-wk11.webp'), 'name' => 'WK-11'],
            ]],
            ['cat' => 'Munitions', 'i18n' => 'project.arsenal.ammo', 'items' => [
                ['src' => $img('item-munition-calibre12.webp'), 'name' => 'Calibre 12'],
                ['src' => $img('item-munition-flechette.webp'), 'name' => 'Fléchette tranquillisante'],
                ['src' => $img('item-munition-556.webp'), 'name' => '5.56×45mm'],
                ['src' => $img('item-munition-338.webp'), 'name' => '.338 Lapua Magnum'],
                ['src' => $img('item-munition-9x19.webp'), 'name' => '9×19mm'],
            ]],
            ['cat' => 'Gadgets', 'i18n' => 'project.arsenal.gadgets', 'items' => [
                ['src' => $img('item-gadget-camera.webp'), 'name' => 'Caméra de surveillance'],
                ['src' => $img('item-gadget-detecteur.webp'), 'name' => 'Détecteur de mouvement'],
                ['src' => $img('item-gadget-drone.webp'), 'name' => 'Drone de reconnaissance'],
                ['src' => $img('item-gadget-piege.webp'), 'name' => 'Piège à filet'],
            ]],
            ['cat' => 'Consommables', 'i18n' => 'project.arsenal.consumables', 'items' => [
                ['src' => $img('item-conso-bandage.webp'), 'name' => 'Rouleaux de bandage'],
                ['src' => $img('item-conso-reparation.webp'), 'name' => 'Kit de réparation'],
                ['src' => $img('item-conso-eau.webp'), 'name' => "Bouteille d'eau"],
                ['src' => $img('item-conso-mre.webp'), 'name' => 'Ration MRE'],
                ['src' => $img('item-conso-soin.webp'), 'name' => 'Trousse de soin militaire'],
                ['src' => $img('item-conso-adrenaline.webp'), 'name' => "Seringue d'adrénaline"],
            ]],
        ];

        $gallery = [
            ['src' => $img('env-day-02.webp'), 'cap' => 'Environnement — jour', 'cat' => 'ENVIRONMENT'],
            ['src' => $img('hq-briefing.webp'), 'cap' => 'Opérations — briefing de mission', 'cat' => 'OPERATIONS'],
            ['src' => $img('env-night-03.webp'), 'cap' => 'Environnement — nuit', 'cat' => 'ENVIRONMENT'],
            ['src' => $img('hq-shop.webp'), 'cap' => 'Quartier général — équipement', 'cat' => 'HEADQUARTERS'],
            ['src' => $img('env-fog-04.webp'), 'cap' => 'Environnement — brouillard', 'cat' => 'ENVIRONMENT'],
            ['src' => $img('hq-range.webp'), 'cap' => 'Quartier général — centre de tir', 'cat' => 'HEADQUARTERS'],
            ['src' => $img('env-sunset-03.webp'), 'cap' => 'Terrain — coucher de soleil', 'cat' => 'FIELD'],
            ['src' => $img('hq-hangar-02.webp'), 'cap' => 'Quartier général — hangar', 'cat' => 'HEADQUARTERS'],
            ['src' => $img('env-day-04.webp'), 'cap' => 'Terrain — exploration', 'cat' => 'FIELD'],
        ];

        $nav = [
            ['route' => 'web.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'web.works',   'i18n' => 'nav.works',  'keep' => true],
            ['route' => 'web.lab',     'i18n' => 'nav.lab', 'keep' => false, 'external' => true],
        ];
    @endphp
</head>

    {{-- ==================================================================
         HERO
    =================================================================== --}}
    <section style="position:relative;height:92vh;min-height:600px;overflow:hidden;">
        <div class="parallax" data-parallax="50" style="position:absolute;inset:0;">
            <img src="{{ $img('hero-attack.webp') }}" alt="Un contractant fait face à un spécimen dans la brume"
                 class="sc-unzoom" data-scrub="0 1"
                 style="position:absolute;inset:-8%;width:calc(100% + 16%);height:calc(100% + 16%);object-fit:cover;">
        </div>
        <div style="position:absolute;inset:0;background:linear-gradient(180deg, rgba(5,5,10,.35), rgba(5,5,10,.25) 40%, rgba(5,5,10,.97));"></div>
        <div class="wrap" style="position:relative;height:100%;display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:var(--s-8);">
            <div data-reveal="mask">
                <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);" data-i18n="project.hero.kicker">En développement — PC</span></span>
                <span class="mask-line"><h1 class="t-display">Jurassic Containment</h1></span>
            </div>
            <p class="t-lead" data-reveal="blur" data-reveal-delay="180" style="margin-top:var(--s-4);max-width:46ch;">
                Hunt. Contain. Survive. <span data-i18n="project.hero.lead">Un jeu de chasse coopératif en première personne, où chaque mission vous confronte à des créatures préhistoriques imprévisibles.</span>
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:var(--s-4);align-items:center;margin-top:var(--s-6);" data-reveal="stagger" data-reveal-delay="320">
                <a class="btn" href="#projet"><span aria-hidden="true">▷</span> <span data-i18n="project.hero.cta.discover">Découvrir le projet</span> <span class="arrow" aria-hidden="true">→</span></a>
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
                        <span class="t-body" style="font-size:14px;" data-i18n="project.meta.status.value">En développement</span>
                    </div>
                    <span class="mark"></span>
                    <a class="btn btn-ghost" href="#galerie" style="align-self:flex-start;"><span data-i18n="project.meta.captures">Voir les captures</span> <span class="arrow" aria-hidden="true">→</span></a>
                </div>
            </aside>
            <div class="split-flow">
                <div>
                    <span class="label" data-reveal="rise">The hunt has begun</span>
                    <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);" data-i18n="project.hunt.title">Traquer. Observer. Préparer. Contenir.</h2>
                    <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.hunt.p1">
                        Jurassic Containment est un jeu coopératif de chasse en première personne. Rejoignez une
                        équipe de contractants envoyés sur des zones isolées pour localiser, traquer et contenir
                        des spécimens dangereux.
                    </p>
                    <p class="t-body" data-reveal="blur" data-reveal-delay="80" data-i18n="project.hunt.p2">
                        Chaque mission vous demande de vous adapter au terrain, à votre équipement et surtout à
                        votre cible.
                    </p>
                </div>

                <div class="grid-3" data-reveal="stagger">
                    <div class="stat" data-count="4">
                        <span class="stat-value num" data-count-value>0</span>
                        <span class="stat-label" data-i18n="project.stat.env">États environnement</span>
                    </div>
                    <div class="stat" data-count="4">
                        <span class="stat-value num" data-count-value>0</span>
                        <span class="stat-label" data-i18n="project.stat.coop">Contractants co-op</span>
                    </div>
                    <div class="stat" data-count="5">
                        <span class="stat-value num" data-count-value>0</span>
                        <span class="stat-label" data-i18n="project.stat.attrs">Attributs par mission</span>
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
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">Every hunt is different</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.gameplay.lead">
                    Votre objectif principal est simple : retrouver et neutraliser ou capturer le spécimen ciblé.
                    Mais une mission ne se résume jamais à une simple chasse.
                </p>
            </div>

            <div style="display:grid;grid-template-columns:1fr minmax(240px,42%) 1fr;gap:clamp(16px,3vw,40px);align-items:center;">
                <div class="sc-in-left" data-scrub="0.05 0.7" data-scrub-ease="out" style="--amp:-84px;display:flex;flex-direction:column;gap:var(--s-3);align-items:flex-end;text-align:right;">
                    <span class="label" data-i18n="project.relay.label">Relais coupé</span>
                    <span class="mark" data-reveal="line" style="width:100%;"></span>
                    <span class="t-body" style="font-size:13px;" data-i18n="project.relay.desc">Un relais détruit peut couper vos communications.</span>
                </div>

                <div style="position:relative;aspect-ratio:1/1;">
                    <div class="sc-sharp" data-scrub="0.1 0.62" data-reveal="hex" style="--amp:9px;position:absolute;inset:0;">
                        <img src="{{ $img('env-fog-01.webp') }}" alt="Brouillard sur la zone d'opération" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <span class="label label-accent" data-reveal="rise" data-reveal-delay="620" style="position:absolute;left:50%;bottom:-26px;transform:translateX(-50%);white-space:nowrap;" data-i18n="project.zone.compromised">Zone compromise</span>
                </div>

                <div class="sc-in-right" data-scrub="0.05 0.7" data-scrub-ease="out" style="--amp:84px;display:flex;flex-direction:column;gap:var(--s-3);">
                    <span class="label" data-i18n="project.extraction.label">Extraction dangereuse</span>
                    <span class="mark" data-reveal="line" style="width:100%;"></span>
                    <span class="t-body" style="font-size:13px;" data-i18n="project.extraction.desc">Un spécimen agressif peut transformer une opération contrôlée en véritable situation de survie.</span>
                </div>
            </div>

            <div style="max-width:60ch;margin:var(--s-8) auto 0;text-align:center;">
                <h2 class="t-h2" data-reveal="mask" data-reveal-delay="420"><span class="mask-line"><span>The mission doesn't go according to plan.</span></span></h2>
                <p class="t-body" data-reveal="blur" data-reveal-delay="680" style="margin:var(--s-4) auto 0;">
                    You adapt.
                </p>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         ENVIRONMENT — séquence jour / nuit / brouillard (motion.js)
    =================================================================== --}}
    <section class="section-tight">
        <div class="wrap">
            <div style="max-width:60ch;">
                <span class="label" data-reveal="rise" data-i18n="project.env.label">Environnement</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">A world that doesn't wait</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.env.lead">
                    Les opérations prennent place dans des environnements sauvages où les conditions peuvent
                    radicalement changer l'expérience de jeu.
                </p>
            </div>
        </div>

        {{-- data-env-label / data-env-caption sont écrasés par pages/project.js
             au fil du scroll (jour/nuit/brouillard) — pas encore relié au
             système i18n, reste en français pour l'instant. --}}
        <div data-env-stage style="position:relative;height:100vh;overflow:hidden;margin-top:var(--s-7);background:#05050a;">
            <img data-env-layer src="{{ $img('env-day-03.webp') }}" alt="Zone d'opération de jour" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:1;">
            <img data-env-layer src="{{ $img('env-night-02.webp') }}" alt="Zone d'opération de nuit" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;">
            <img data-env-layer src="{{ $img('env-fog-02.webp') }}" alt="Zone d'opération sous le brouillard" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;">

            <div style="position:absolute;inset:0;background:linear-gradient(0deg, rgba(5,5,10,.85), transparent 40%);"></div>

            <div style="position:absolute;left:var(--gutter);bottom:var(--s-7);right:var(--gutter);display:flex;flex-direction:column;gap:var(--s-3);">
                <span data-env-label class="label label-accent" style="font-size:12px;letter-spacing:.28em;">JOUR</span>
                <p data-env-caption class="t-body" style="max-width:46ch;font-size:15px;">
                    La visibilité permet d'explorer plus facilement le terrain, mais ne garantit pas votre sécurité.
                </p>
            </div>

            <div data-env-progress style="position:absolute;left:0;bottom:0;height:2px;width:100%;background:rgba(233,233,237,.10);">
                <span style="display:block;height:100%;width:100%;transform:scaleX(0);transform-origin:0 50%;background:linear-gradient(90deg,var(--accent-2),var(--accent));"></span>
            </div>
        </div>

        <div class="wrap" style="margin-top:var(--s-7);text-align:center;">
            <p class="t-lead" data-reveal="up">The environment is part of the hunt.</p>
        </div>
    </section>

    {{-- ==================================================================
         ATMOSPHERE
    =================================================================== --}}
    <section class="section" style="position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;">
            <img src="{{ $img('env-night-04.webp') }}" alt="" style="width:100%;height:100%;object-fit:cover;opacity:.28;">
            <div style="position:absolute;inset:0;background:linear-gradient(180deg, var(--bg) 0%, rgba(5,5,10,.55) 30%, var(--bg) 100%);"></div>
        </div>
        <div class="wrap" style="position:relative;max-width:64ch;text-align:center;margin-inline:auto;">
            <span class="label" data-reveal="rise">Atmosphere</span>
            <h2 class="t-h1" data-reveal="mask" style="margin-top:var(--s-3);">
                <span class="mask-line"><span>You are not alone</span></span>
            </h2>
            <p class="t-lead" data-reveal="blur" data-reveal-delay="160" style="margin-top:var(--s-5);" data-i18n="project.atmo.p1">
                La forêt est silencieuse. Trop silencieuse.
            </p>
            <p class="t-body" data-reveal="blur" data-reveal-delay="240" style="margin-top:var(--s-3);" data-i18n="project.atmo.p2">
                Entre les arbres, quelque chose bouge. Vous ne savez pas encore quoi. Vous savez seulement que
                vous n'avez pas beaucoup de temps. Jurassic Containment cherche à construire une tension
                constante autour de la chasse, de l'exploration et de l'inconnu.
            </p>
        </div>
    </section>

    {{-- ==================================================================
         HQ — traversée en corridor (motion.js)
    =================================================================== --}}
    <section class="section-tight" data-lab="corridor">
        <div class="wrap">
            <span class="label" data-reveal="rise" style="display:block;margin-bottom:var(--s-3);">HQ</span>
            <h2 class="t-h1" data-reveal="mask"><span class="mask-line"><span>Your operations. Your preparation.</span></span></h2>
        </div>

        <div data-corridor-stage style="position:relative;height:100vh;overflow:hidden;margin-top:var(--s-7);background:linear-gradient(180deg,var(--bg),var(--bg-1) 55%,var(--bg));perspective:900px;perspective-origin:50% 50%;">
            <div data-corridor style="position:absolute;inset:0;transform-style:preserve-3d;will-change:transform;">
                <div data-plane="-6400" data-plane-x="-280" data-plane-y="-40" data-plane-rot="-2" style="position:absolute;left:50%;top:50%;width:min(30vw,420px);aspect-ratio:4/3;border:1px solid var(--line-2);background:linear-gradient(140deg,var(--bg-2),var(--bg) 65%);overflow:hidden;">
                    <img src="{{ $img('hq-hangar-01.webp') }}" alt="Hangar du quartier général" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div data-plane="-4800" data-plane-x="260" data-plane-y="60" data-plane-rot="3" style="position:absolute;left:50%;top:50%;width:min(26vw,360px);aspect-ratio:4/3;border:1px solid var(--line-2);background:linear-gradient(140deg,var(--bg-2),var(--bg) 65%);overflow:hidden;">
                    <img src="{{ $img('hq-briefing.webp') }}" alt="Écran de briefing de mission" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div data-plane="-3200" data-plane-x="-200" data-plane-y="140" style="position:absolute;left:50%;top:50%;width:min(28vw,400px);aspect-ratio:4/3;border:1px solid var(--line-2);background:linear-gradient(140deg,var(--bg-2),var(--bg) 65%);overflow:hidden;">
                    <img src="{{ $img('hq-shop.webp') }}" alt="Interface d'équipement du quartier général" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div data-plane="-1600" data-plane-x="280" data-plane-y="-140" data-plane-rot="-3" style="position:absolute;left:50%;top:50%;width:min(26vw,360px);aspect-ratio:4/3;border:1px solid var(--line-2);background:linear-gradient(140deg,var(--bg-2),var(--bg) 65%);overflow:hidden;">
                    <img src="{{ $img('hq-range.webp') }}" alt="Centre de tir du quartier général" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div data-plane="-200" data-plane-x="0" data-plane-y="0" style="position:absolute;left:50%;top:50%;width:min(34vw,480px);aspect-ratio:16/9;border:1px solid var(--accent-dim);background:linear-gradient(140deg,var(--bg-2),var(--bg) 65%);overflow:hidden;">
                    <img src="{{ $img('hq-hangar-02.webp') }}" alt="Espace d'équipe du quartier général" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>

            <div style="position:absolute;left:var(--gutter);bottom:var(--s-6);display:flex;flex-direction:column;gap:var(--s-2);pointer-events:none;">
                <span class="label">Caméra · Z</span>
                <span data-corridor-z class="num" style="font-size:clamp(24px,3vw,38px);font-weight:200;">0</span>
            </div>
            <div style="position:absolute;inset:0;pointer-events:none;background:radial-gradient(120% 90% at 50% 50%,transparent 42%,rgba(5,5,10,.9));"></div>
        </div>
    </section>

    {{-- ==================================================================
         PREPARE YOUR TEAM
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div>
                <span class="label" data-reveal="rise" data-i18n="project.prep.label">Préparation</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">Prepare your team</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.prep.p1">
                    Avant chaque mission, les joueurs retrouvent leur quartier général. C'est ici que vous
                    préparez votre prochaine opération, sélectionnez votre équipement, consultez les
                    informations disponibles et choisissez votre prochaine destination.
                </p>
                <p class="t-body" data-reveal="blur" data-reveal-delay="80" style="margin-top:var(--s-3);" data-i18n="project.prep.p2">
                    Armes, munitions, gadgets et consommables peuvent être sélectionnés en fonction de la
                    mission et de votre rôle au sein de l'équipe. Le QG n'est pas simplement un menu — c'est
                    votre point de départ.
                </p>
                <p class="t-lead" data-reveal="up" data-reveal-delay="160" style="margin-top:var(--s-5);">
                    Know your target. Prepare your equipment. Deploy.
                </p>
            </div>
            <div class="media media-4-3 media-hover" data-reveal="frame">
                <img src="{{ $img('hq-shop.webp') }}" alt="Interface d'équipement du quartier général">
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
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">Equip for the mission ahead</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.arsenal.lead">
                    Chaque contractant compose son propre chargement avant le déploiement : arme principale,
                    munitions adaptées à la cible, gadgets de reconnaissance et consommables de survie.
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
         MISSION SYSTEM
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div class="media media-4-3 media-hover" data-reveal="frame" style="order:2;">
                <img src="{{ $img('hq-briefing.webp') }}" alt="Écran de briefing de mission">
            </div>
            <div style="order:1;">
                <span class="label" data-reveal="rise">Mission system</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">Choose your next target</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.mission.lead">
                    Les missions sont sélectionnées depuis le centre des opérations. Explorez les différentes
                    régions disponibles, identifiez les zones actives et consultez les informations de chaque
                    opération avant de vous déployer.
                </p>
                <span class="t-body" style="display:block;margin-top:var(--s-4);font-size:13px;color:var(--text-3);" data-i18n="project.mission.each">Chaque mission présente ses propres :</span>
                <div style="display:flex;flex-wrap:wrap;gap:var(--s-2);margin-top:var(--s-3);" data-reveal="stagger">
                    @foreach ($missionAttrs as $attr)
                        <span class="tag" data-i18n="{{ $attr['i18n'] }}">{{ $attr['fr'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         THE DINOSAURS
    =================================================================== --}}
    <section class="section-tight" style="text-align:center;">
        <div class="wrap" style="max-width:56ch;margin-inline:auto;">
            <span class="label" data-reveal="rise">The dinosaurs</span>
            <h2 class="t-h1" data-reveal="mask" style="margin-top:var(--s-3);"><span class="mask-line"><span>They are not just targets</span></span></h2>
            <p class="t-body" data-reveal="blur" data-reveal-delay="120" style="margin-top:var(--s-5);" data-i18n="project.dinos.lead">
                Les spécimens rencontrés durant les opérations ne sont pas de simples ennemis. Ils peuvent se
                déplacer, fuir, attaquer et réagir à votre présence. Votre approche doit donc évoluer en
                fonction de votre cible.
            </p>
        </div>

        {{-- entrelacs (motion.js) : les trois brins du comportement à adopter --}}
        <div data-braid style="position:relative;height:100vh;overflow:hidden;display:grid;place-items:center;margin-top:var(--s-7);background:radial-gradient(70% 60% at 50% 45%,var(--bg-1),var(--bg) 76%);">
            <div style="position:relative;width:min(1080px,88vw);height:min(66vh,560px);">
                <article data-strand style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:clamp(190px,21vw,290px);display:flex;flex-direction:column;gap:var(--s-3);will-change:transform,filter;text-align:left;">
                    <span style="display:block;aspect-ratio:3/4;border:1px solid var(--line-2);overflow:hidden;">
                        <img src="{{ $img('dino-stegosaurus.webp') }}" alt="Stégosaure" style="width:100%;height:100%;object-fit:cover;object-position:47% 43%;">
                    </span>
                    <span class="label" data-i18n="project.dino.stegosaurus">Stégosaure</span>
                    <span class="t-h3" data-strand-name style="font-weight:200;">Observe</span>
                </article>
                <article data-strand style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:clamp(190px,21vw,290px);display:flex;flex-direction:column;gap:var(--s-3);will-change:transform,filter;text-align:left;">
                    <span style="display:block;aspect-ratio:3/4;border:1px solid var(--line-2);overflow:hidden;">
                        <img src="{{ $img('dino-raptor.webp') }}" alt="Raptor" style="width:100%;height:100%;object-fit:cover;object-position:52% 54%;">
                    </span>
                    <span class="label">Raptor</span>
                    <span class="t-h3" data-strand-name style="font-weight:200;">Track</span>
                </article>
                <article data-strand style="position:absolute;left:50%;top:50%;translate:-50% -50%;width:clamp(190px,21vw,290px);display:flex;flex-direction:column;gap:var(--s-3);will-change:transform,filter;text-align:left;">
                    <span style="display:block;aspect-ratio:3/4;border:1px solid var(--line-2);overflow:hidden;">
                        <img src="{{ $img('dino-pachy.webp') }}" alt="Pachycéphalosaure" style="width:100%;height:100%;object-fit:cover;object-position:49% 47%;">
                    </span>
                    <span class="label" data-i18n="project.dino.pachy">Pachycéphalosaure</span>
                    <span class="t-h3" data-strand-name style="font-weight:200;">Adapt</span>
                </article>
            </div>

            <div style="position:absolute;left:var(--gutter);bottom:var(--s-6);display:flex;gap:var(--s-4);align-items:baseline;pointer-events:none;">
                <span class="label" data-i18n="project.dino.foreground">Au premier plan</span>
                <span data-braid-front class="label label-accent">Observe</span>
            </div>
        </div>

        <div class="wrap" style="max-width:56ch;margin:var(--s-7) auto 0;">
            <p class="t-lead" data-reveal="up">
                Observe. Track. Adapt.
            </p>
        </div>
    </section>

    {{-- ==================================================================
         CO-OP
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div class="media media-4-3 media-hover" data-reveal="frame">
                <img src="{{ $img('hq-hangar-02.webp') }}" alt="Espace d'équipe du quartier général">
            </div>
            <div>
                <span class="label" data-reveal="rise">Co-op</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">Hunt together</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.coop.p1">
                    Jouez en équipe et coordonnez vos actions pour accomplir votre mission. Chaque joueur peut
                    contribuer différemment à l'opération : exploration, soutien, combat, capture ou gestion de
                    l'équipement.
                </p>
                <p class="t-body" data-reveal="blur" data-reveal-delay="80" style="margin-top:var(--s-3);" data-i18n="project.coop.p2">
                    Une bonne préparation peut faire la différence entre une mission réussie et une extraction
                    catastrophique.
                </p>
                <p class="t-lead" data-reveal="up" data-reveal-delay="160" style="margin-top:var(--s-5);">
                    4 contractors. One mission. One extraction.
                </p>
            </div>
        </div>
    </section>

    {{-- ==================================================================
         PROGRESSION
    =================================================================== --}}
    <section class="section" style="text-align:center;">
        <div class="wrap" style="max-width:56ch;margin-inline:auto;">
            <span class="label" data-reveal="rise">Progression</span>
            <h2 class="t-h1" data-reveal="mask" style="margin-top:var(--s-3);"><span class="mask-line"><span>From contractor to legend</span></span></h2>
            <p class="t-body" data-reveal="blur" data-reveal-delay="120" style="margin-top:var(--s-5);" data-i18n="project.progression.p1">
                Chaque opération contribue à votre progression. Gagnez de l'expérience, développez votre
                réputation et débloquez progressivement de nouvelles possibilités pour votre équipement et vos
                missions.
            </p>
            <p class="t-body" data-reveal="blur" data-reveal-delay="200" style="margin-top:var(--s-3);" data-i18n="project.progression.p2">
                Votre réputation ouvre l'accès à des opérations plus dangereuses — et à des récompenses plus
                importantes.
            </p>
        </div>
    </section>

    {{-- ==================================================================
         UI / IMMERSION
    =================================================================== --}}
    <section class="section">
        <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center;">
            <div class="media media-4-3 media-hover" data-reveal="frame" style="order:2;">
                <img src="{{ $img('hq-range.webp') }}" alt="Centre de tir du quartier général">
            </div>
            <div style="order:1;">
                <span class="label" data-reveal="rise">UI / Immersion</span>
                <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);">The interface is part of the world</h2>
                <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);" data-i18n="project.ui.lead">
                    Dans Jurassic Containment, l'interface ne se contente pas d'afficher des informations. Elle
                    fait partie de l'univers. Du quartier général aux briefings de mission, chaque écran est
                    conçu comme un outil utilisé par les contractants de Lakeust BioGen.
                </p>
                <p class="t-lead" data-reveal="up" data-reveal-delay="120" style="margin-top:var(--s-5);">
                    No ordinary menus. Operational systems.
                </p>
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
                    <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);">Field reports</span></span>
                    <span class="mask-line"><h2 class="t-h2" data-i18n="project.gallery.title">Galerie</h2></span>
                </div>
                <span class="label" data-reveal="rise" data-i18n="project.gallery.hint">Glisser · flèches · ← →</span>
            </div>

            {{-- Légendes/alt de la galerie : pas encore traduites (restent en
                 français quelle que soit la langue choisie). --}}
            <div class="carousel" data-carousel data-mode="perspective" data-autoplay="6500" aria-roledescription="carousel">
                <div class="carousel-viewport" data-carousel-viewport aria-label="Galerie de Jurassic Containment">
                    @foreach ($gallery as $n => $slide)
                        <figure class="carousel-slide">
                            <div class="media media-16-9"><img src="{{ $slide['src'] }}" alt="{{ $slide['cap'] }}"></div>
                            <figcaption class="slide-caption label">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }} — {{ $slide['cat'] }} · {{ $slide['cap'] }}</figcaption>
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
            <span class="label" data-reveal="rise">Development</span>
            <h2 class="t-h2" data-reveal="rise" style="margin-top:var(--s-2);max-width:40ch;">Currently in development</h2>
            <p class="t-body" data-reveal="blur" style="margin-top:var(--s-3);max-width:56ch;" data-i18n="project.dev.lead">
                Jurassic Containment est actuellement en développement. Le projet évolue progressivement autour
                de plusieurs axes :
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
                <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);" data-i18n="project.cta.kicker">Le monde n'est pas prêt. Votre équipe non plus.</span></span>
                <span class="mask-line"><h2 class="t-h1">The next contract is waiting</h2></span>
            </div>
            <p class="t-body" data-reveal="blur" data-reveal-delay="120">
                Jurassic Containment is currently in development.
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:var(--s-3);" data-reveal="stagger">
                <a class="btn" href="{{ route('web.works') }}"><span data-i18n="project.cta.allworks">Tous les travaux</span> <span class="arrow" aria-hidden="true">→</span></a>
                <a class="btn btn-ghost" href="{{ route('web.about') }}#contact" data-i18n="nav.contact">Contact</a>
            </div>
        </div>
    </section>
@endsection
