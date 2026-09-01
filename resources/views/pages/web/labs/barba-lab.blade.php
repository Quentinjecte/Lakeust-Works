<!-- Dix scènes, chacune une démonstration autonome (mock de navigateur, pas
     de vraie navigation) : ce laboratoire documente le système de transitions
     Barba réellement branché sur about/works/project (voir
     barba-transitions.js), et prolonge la réflexion vers des techniques que
     le site n'utilise pas encore (05, 08, 09, 10 — marquées comme telles). -->

@extends('layouts.site')
@section('title', 'Barba Lab — Lakeust Works')
@section('content')
<head>
    @vite(['resources/css/lab.css', 'resources/js/labs/barba/barba-lab.js'])

    @php
        /* Catalogue — même gabarit de fiche que Scroll Lab (tag/statut, titre,
           description, cinq champs, CTA), adapté au vocabulaire de ce lab :
           « Déclenche » remplace « Scroll » puisque rien ici n'est piloté par
           le défilement — chaque scène se déclenche au survol/clic sur un
           lien simulé. Les dix scènes sont déjà prototypées, donc chaque
           fiche a son bouton Ouvrir (contrairement à Scroll Lab, qui liste
           aussi des idées non construites). */
        $sections = [
            ['tag' => 'X', 'name' => 'Catalogue',],
            ['tag' => '01', 'name' => 'Voile', 'open' => 1, 'i18n' => 'barba.cat.1',
                'desc' => "Le blanc monte à 80 %, jamais à 100 % — le plafond de la traversée nuageuse du cinématique. Le conteneur est échangé au sommet du voile, page sortante déjà floue.",
                'declenche' => "Clic sur un lien interne ; Barba intercepte et attend la promesse de leave avant le swap.",
                'vu' => "Flou + voile blanc montant, contenu échangé au sommet, puis le voile redescend sur la nouvelle page.",
                'tech' => "gsap.to sur filter/opacity, deux tweens chaînés (leave 520 ms, enter 680 ms).",
                'cout' => "Faible — c'est la règle par défaut du site, déjà en place partout où aucune paire plus spécifique ne matche.",
                'portfolio' => "Déjà en production — c'est le filet de sécurité de toute navigation."],
            ['tag' => '02', 'name' => 'Horizon', 'open' => 2, 'i18n' => 'barba.cat.2',
                'desc' => "La ligne se trace d'abord, les deux panneaux se referment sur elle, puis rouvrent sur la page suivante.",
                'declenche' => "Navigation à propos ↔ travaux.",
                'vu' => "Une ligne lumineuse qui se dessine, deux panneaux qui se referment dessus puis rouvrent sur le contenu suivant.",
                'tech' => "leave() dessine la ligne puis ferme les panneaux ; Barba attend la promesse — pas de raccourci possible.",
                'cout' => "Faible — deux tweens séquencés, aucun état à réconcilier.",
                'portfolio' => "Déjà en production entre à-propos et travaux."],
            ['tag' => '03', 'name' => 'Signal', 'open' => 3, 'i18n' => 'barba.cat.3',
                'desc' => "Perte de signal : la page s'assombrit, un balayage passe deux fois, le relevé de route s'affiche, puis le contenu revient par paliers.",
                'declenche' => "Navigation travaux ↔ projet.",
                'vu' => "Assombrissement, double balayage lumineux, relevé de route (namespaces), retour par paliers steps().",
                'tech' => "Relevé composé depuis les namespaces des deux vues, disponibles dans leave().",
                'cout' => "Moyen — la seule transition du jeu qui affiche la navigation elle-même, donc la seule à lire le contexte des deux pages.",
                'portfolio' => "Déjà en production entre travaux et projet."],
            ['tag' => '04', 'name' => 'Éclipse', 'open' => 4, 'i18n' => 'barba.cat.4',
                'desc' => "L'ouverture se referme jusqu'au noir complet, l'anneau tient 200 ms sur la bascule, puis rouvre sur la page suivante.",
                'declenche' => "Navigation à propos ↔ projet.",
                'vu' => "Un disque qui se referme jusqu'au noir, un anneau lumineux qui tient la bascule, puis une ouverture progressive.",
                'tech' => "La couverture vient d'un box-shadow à écartement constant : on anime la taille de l'ouverture, jamais un scale.",
                'cout' => "Moyen — le piège du scale sur l'ombre est le genre d'erreur qui ne se voit qu'à l'écran.",
                'portfolio' => "Déjà en production entre à-propos et projet."],
            ['tag' => '05', 'name' => 'Synchrone', 'open' => 5, 'i18n' => 'barba.cat.5',
                'desc' => "Avec sync: true, les deux conteneurs coexistent dans le document : le sortant part à gauche pendant que l'entrant arrive de la droite.",
                'declenche' => "Piste non branchée sur le site.",
                'vu' => "Les deux pages glissent en même temps, côte à côte, au lieu de se succéder.",
                'tech' => "Sans ce drapeau, leave doit finir avant qu'enter commence — un raccord immédiat est alors impossible.",
                'cout' => "Élevé — gérer deux conteneurs vivants en même temps complique tout ce qui suit (scroll, focus, mesures).",
                'portfolio' => "Faible aujourd'hui : aucune paire de routes du site n'en a besoin."],
            ['tag' => '06', 'name' => 'Règles de route', 'open' => 6, 'i18n' => 'barba.cat.6',
                'desc' => "Une transition par paire de vues. Barba retient la première règle qui correspond : les paires spécifiques d'abord, la règle sans condition en dernier recours.",
                'declenche' => "Choix d'un départ et d'une arrivée dans le sélecteur.",
                'vu' => "La règle appliquée s'allume dans la liste selon la paire choisie.",
                'tech' => "TRANSITIONS dans barba-transitions.js ; les namespaces viennent du nom de route Blade (data-barba-namespace).",
                'cout' => "Faible — c'est une lecture, pas une animation.",
                'portfolio' => "C'est la table réellement chargée sur le site — documentation vivante plus qu'effet."],
            ['tag' => '07', 'name' => 'Vues', 'open' => 7, 'i18n' => 'barba.cat.7',
                'desc' => "Ce qui doit vivre et mourir avec une page : reveals, scrub, parallax, et les sections épinglées ScrollTrigger de /projet.",
                'declenche' => "Entrée/sortie du conteneur Barba (afterEnter / beforeLeave).",
                'vu' => "Un journal des montages/démontages au fil des navigations simulées.",
                'tech' => "bootPageSystems() et project.dispose() — sans démontage, ScrollTrigger garde les bornes de la page précédente.",
                'cout' => "Moyen — l'oubli est silencieux : la fuite ne se voit qu'après plusieurs navigations.",
                'portfolio' => "C'est le cycle réel du site — pas un effet, un garde-fou."],
            ['tag' => '08', 'name' => 'Élément partagé', 'open' => 8, 'i18n' => 'barba.cat.8',
                'desc' => "La vignette cliquée devient l'image de tête de la fiche. Mesure avant, mesure après, une seule interpolation : le lien visuel remplace la transition.",
                'declenche' => "Piste non branchée sur le site.",
                'vu' => "Une vignette qui grandit et se déplace jusqu'à devenir l'image d'en-tête de la page suivante.",
                'tech' => "FLIP : les deux conteneurs coexistent le temps de la mesure (sync: true), un couple d'attributs apparie source et cible.",
                'cout' => "Élevé — /travaux n'a pas encore de vignettes de projet à apparier.",
                'portfolio' => "Fort si des vignettes de projet existent un jour — le lien visuel est plus lisible qu'un fondu."],
            ['tag' => '09', 'name' => 'Préchargement', 'open' => 9, 'i18n' => 'barba.cat.9',
                'desc' => "Le greffon @barba/prefetch demande la page au survol du lien. Au clic, il ne reste que la peinture : la transition démarre sur un cache chaud.",
                'declenche' => "Survol d'un lien interne.",
                'vu' => "Comparaison de l'attente réseau observée entre un lien préchargé et un lien qui ne l'est pas.",
                'tech' => "@barba/prefetch, greffon non installé sur le site — cette scène simule seulement l'écart qu'il supprimerait.",
                'cout' => "Faible à installer, mais dépense du réseau sur des liens jamais cliqués.",
                'portfolio' => "Moyen — gain surtout perceptible sur mobile ou réseau lent."],
            ['tag' => '10', 'name' => 'Toile persistante', 'open' => 10, 'i18n' => 'barba.cat.10',
                'desc' => "Un canvas Three.js pourrait vivre hors du conteneur Barba : jamais démonté, seule l'interface serait échangée pendant que la scène continue de tourner.",
                'declenche' => "Piste non branchée sur le site.",
                'vu' => "Simulation de ce qu'il faudrait pour enchaîner les cinématiques sans réinitialiser Three.js.",
                'tech' => "Sur ce site, chaque cinématique est une page à part hors du wrapper Barba — un vrai chargement, délibéré.",
                'cout' => "Élevé — sortir un canvas du cycle Barba complique la synchronisation avec le reste de la page.",
                'portfolio' => "Non appliqué : le choix actuel (page à part) est plus simple et suffisant."],
        ];
    @endphp
</head>

<div data-blab-root style="position:relative;min-height:100vh;background:#05050a">

    <nav style="position:fixed;left:clamp(10px,2.4vw,26px);top:50%;transform:translateY(-50%);z-index:50;display:flex;flex-direction:column;padding-left:12px">
        <span style="position:absolute;left:0;top:8px;bottom:8px;width:1px;background:rgba(233,233,237,.12)"></span>
        <span data-lab-sec-dot style="position:absolute;left:0;top:8px;width:1px;height:10px;background:#9184d9;box-shadow:0 0 10px rgba(145,132,217,.7);transition:transform .45s cubic-bezier(.16,1,.3,1)"></span>
        @foreach ($sections as $r)
            <button type="button" data-lab-sec title="{{ $r['name'] }}" class="lab-sec-btn" style="appearance:none;background:none;border:0;cursor:pointer;display:flex;align-items:center;height:26px;padding:0;color:rgba(226,221,209,.40);font-family:inherit;font-size:10px;letter-spacing:.20em;text-transform:uppercase;transition:color .3s">
                <span>{{ $r['tag'] }}</span>
            </button>
        @endforeach
    </nav>

    <div style="position:fixed;left:0;bottom:0;height:1px;width:100%;background:rgba(233,233,237,.10);z-index:50">
        <span data-blab-progress style="display:block;height:100%;width:100%;transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.25),#9184d9);transition:transform .5s cubic-bezier(.16,1,.3,1);transform:scaleX(0.1)"></span>
    </div>

    <main style="position:relative;z-index:1">

        {{-- ==================================================== X · Catalogue --- --}}
        <section class="lab-sec blab-catalogue" data-blab-scr="10" data-screen-label="Lab 11 Catalogue" style="position:relative;min-height:100vh">
            <div style="min-height:52vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:0 clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98" data-i18n="lab.title.catalogue">Catalogue</h1>
                <p style="margin:0;max-width:56ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="barba.intro">Dix scènes du système de transitions Barba, chacune une démonstration autonome sur un mock de navigateur. Quatre sont réellement chargées sur le site (01, 02, 03, 04) ; les six autres prolongent la réflexion vers des techniques non encore branchées.</p>
            </div>

            <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));padding:0 clamp(56px,9vw,180px) clamp(60px,8vw,120px)">
                @foreach (array_slice($sections, 1, count($sections) - 1, true) as $k => $c)
                    <article style="display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55)">
                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                            <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ $c['tag'] }}</span>
                            <span style="font-size:10px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)" data-i18n="lab.status">Prototypé</span>
                        </div>
                        <h3 style="margin:0;font-size:clamp(20px,2.2vw,28px);font-weight:200;letter-spacing:-.02em;line-height:1.1">{{ $c['name'] }}</h3>
                        <p style="margin:0;font-size:13.5px;line-height:1.7;color:rgba(226,221,209,.62)" data-i18n="{{ $c['i18n'] }}.desc">{{ $c['desc'] }}</p>
                        <div style="display:flex;flex-direction:column;gap:9px;padding-top:14px;border-top:1px solid rgba(233,233,237,.08)">
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.trigger">Déclenche</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.trigger">{{ $c['declenche'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.seen">Vu</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.seen">{{ $c['vu'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.tech">Tech</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.tech">{{ $c['tech'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.cost">Coût</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.cost">{{ $c['cout'] }}</span></span>
                            <span style="display:grid;grid-template-columns:80px 1fr;gap:14px;font-size:11.5px;line-height:1.55"><span style="letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.30)" data-i18n="lab.field.portfolio">Portfolio</span><span style="color:rgba(226,221,209,.72)" data-i18n="{{ $c['i18n'] }}.portfolio">{{ $c['portfolio'] }}</span></span>
                        </div>
                        <button type="button" class="lab-cta" data-blab-cta="{{ $c['open'] }}"><span data-i18n="lab.open">Ouvrir</span> {{ $c['tag'] }}</button>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ==================================================== 01 · Voile --- --}}
        <section class="lab-sec is-active" data-blab-scr="0" data-demo="voile" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">01</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Voile</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le blanc monte à 80&nbsp;%, jamais à 100&nbsp;% — le plafond de la traversée nuageuse du cinématique. Le conteneur est échangé au sommet du voile, quand la page sortante est déjà floue.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">La forme la plus neutre du jeu : elle convient à toutes les paires de routes et sert de règle par défaut sur le site.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>{ name: 'voile',</span>
                    <span style="padding-left:12px">leave: ({ current }) =&gt; veil.in(520),</span>
                    <span style="padding-left:12px">enter: ({ next }) =&gt; veil.out(680) }</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02)">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div data-page="a" style="position:absolute;inset:0;opacity:1;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#12131d,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Index</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">TRAVAUX</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">12 projets — 2024 / 2026</span>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;opacity:0;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#191a33,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Fiche</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">Three.js — Theatre.js — 46 s</span>
                        </div>
                        <div data-el="veil" style="position:absolute;inset:0;opacity:0;pointer-events:none;background:radial-gradient(130% 100% at 50% 116%,#e4e7f5 0%,#c3c8dd 46%,#8f94ae 100%)"></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">520 + 680 ms</span>
                    <button type="button" class="btn btn-primary" data-blab-play="voile">Naviguer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 02 · Horizon --- --}}
        <section class="lab-sec" data-blab-scr="1" data-demo="horizon" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">02</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Horizon</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">La ligne se trace d'abord, les deux panneaux se referment sur elle, puis rouvrent sur la page suivante. La ligne reste une fraction de seconde seule à l'écran.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">En place entre à-propos et travaux sur le site. Barba attend la promesse renvoyée par <code style="color:#b3a9e6">leave</code> : la fermeture ne peut pas être court-circuitée par une requête rapide.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>leave: () =&gt; line.draw(260)</span>
                    <span style="padding-left:12px">.then(() =&gt; panels.close(460))</span>
                    <span>enter: () =&gt; panels.open(620)</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02)">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div data-page="a" style="position:absolute;inset:0;opacity:1;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#12131d,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Index</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">TRAVAUX</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">12 projets — 2024 / 2026</span>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;opacity:0;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#191a33,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Fiche</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">Three.js — Theatre.js — 46 s</span>
                        </div>
                        <div data-el="panelTop" style="position:absolute;left:0;right:0;top:0;height:50.2%;transform:translateY(-101%);pointer-events:none;background:linear-gradient(180deg,#05050a,#12131d)"></div>
                        <div data-el="panelBottom" style="position:absolute;left:0;right:0;bottom:0;height:50.2%;transform:translateY(101%);pointer-events:none;background:linear-gradient(0deg,#05050a,#12131d)"></div>
                        <div data-el="line" style="position:absolute;left:0;right:0;top:50%;height:1px;transform:scaleX(0);opacity:0;pointer-events:none;background:linear-gradient(90deg,transparent,#9184d9 18%,#9184d9 82%,transparent);box-shadow:0 0 18px rgba(145,132,217,.6)"></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">260 + 460 + 620 ms</span>
                    <button type="button" class="btn btn-primary" data-blab-play="horizon">Naviguer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 03 · Signal --- --}}
        <section class="lab-sec" data-blab-scr="2" data-demo="signal" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">03</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Signal</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Perte de signal&nbsp;: la page s'assombrit, un balayage passe deux fois, le relevé de route s'affiche, puis le contenu revient par paliers.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">En place entre travaux et projet sur le site — la seule transition du jeu qui montre la navigation elle-même. Le relevé est composé depuis les namespaces des deux vues, disponibles dans <code style="color:#b3a9e6">leave</code>.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>leave({ current, next }) {</span>
                    <span style="padding-left:12px">route.textContent =</span>
                    <span style="padding-left:24px">`${current.namespace} → ${next.namespace}`</span>
                    <span>}</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02)">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div data-page="a" style="position:absolute;inset:0;opacity:1;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#12131d,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Index</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">TRAVAUX</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">12 projets — 2024 / 2026</span>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;opacity:0;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#191a33,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Fiche</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">Three.js — Theatre.js — 46 s</span>
                        </div>
                        <div data-el="dim" style="position:absolute;inset:0;opacity:0;pointer-events:none;background:rgba(5,5,10,.88)"></div>
                        <div data-el="scan" style="position:absolute;left:0;right:0;top:0;height:26%;opacity:0;pointer-events:none;background:linear-gradient(180deg,transparent,rgba(145,132,217,.16) 72%,#9184d9)"></div>
                        <div data-el="readout" style="position:absolute;left:26px;bottom:26px;opacity:0;pointer-events:none;display:flex;align-items:center;gap:10px;font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.72)">
                            <span style="display:block;width:20px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            <span data-el="route">works → project</span>
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">620 + 540 ms</span>
                    <button type="button" class="btn btn-primary" data-blab-play="signal">Naviguer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 04 · Éclipse --- --}}
        <section class="lab-sec" data-blab-scr="3" data-demo="eclipse" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">04</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Éclipse</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">L'ouverture se referme jusqu'au noir complet, l'anneau tient 200&nbsp;ms sur la bascule, puis rouvre sur la page suivante qui se détend d'un pour cent.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">En place entre à-propos et projet sur le site. La couverture vient d'une ombre à écartement constant&nbsp;: c'est la taille de l'ouverture qui est animée, jamais un <code style="color:#b3a9e6">scale</code> — sinon l'ombre rétrécit avec elle.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>beforeEnter: () =&gt; window.scrollTo(0, 0)</span>
                    <span>enter: () =&gt; iris.open(720)</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02)">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div data-page="a" style="position:absolute;inset:0;opacity:1;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#12131d,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Index</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">TRAVAUX</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">12 projets — 2024 / 2026</span>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;opacity:0;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#191a33,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Fiche</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">Three.js — Theatre.js — 46 s</span>
                        </div>
                        <div data-el="hole" style="position:absolute;left:50%;top:50%;width:132%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;pointer-events:none;box-shadow:0 0 0 1400px #05050a"></div>
                        <div data-el="ring" style="position:absolute;left:50%;top:50%;width:132%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;opacity:0;pointer-events:none;border:1px solid #9184d9;box-shadow:0 0 26px rgba(145,132,217,.55)"></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">560 + 200 + 720 ms</span>
                    <button type="button" class="btn btn-primary" data-blab-play="eclipse">Naviguer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 05 · Synchrone (concept) --- --}}
        <section class="lab-sec" data-blab-scr="4" data-demo="sync" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">05</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Synchrone</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Avec <code style="color:#b3a9e6">sync: true</code>, les deux conteneurs coexistent dans le document&nbsp;: le sortant part à gauche pendant que l'entrant arrive de la droite, dans la même course.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Piste non branchée sur le site — aucune paire de routes n'en a besoin aujourd'hui. Sans ce drapeau, <code style="color:#b3a9e6">leave</code> doit finir avant qu'<code style="color:#b3a9e6">enter</code> commence — un raccord est alors impossible.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>barba.init({ sync: true, transitions: [{</span>
                    <span style="padding-left:12px">leave: ({ current }) =&gt; slide(current, -100),</span>
                    <span style="padding-left:12px">enter: ({ next }) =&gt; slide(next, 100, 0) }] })</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02)">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div data-page="a" style="position:absolute;inset:0;opacity:1;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#12131d,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Index</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">TRAVAUX</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">12 projets — 2024 / 2026</span>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;opacity:0;padding:26px;display:flex;flex-direction:column;justify-content:space-between;background:linear-gradient(180deg,#191a33,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Fiche</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">Three.js — Theatre.js — 46 s</span>
                        </div>
                        <span data-el="seam" style="position:absolute;top:0;bottom:0;width:1px;left:50%;opacity:0;pointer-events:none;background:linear-gradient(180deg,transparent,#9184d9,transparent)"></span>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums">720 ms, une seule course</span>
                    <button type="button" class="btn btn-primary" data-blab-play="sync">Naviguer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 06 · Règles de route --- --}}
        <section class="lab-sec" data-blab-scr="5" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">06</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Règles de route</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Une transition par paire de vues. Barba retient la première règle qui correspond&nbsp;: les paires spécifiques d'abord, la règle sans condition en dernier recours.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Ce sont les règles réellement chargées sur le site — voir <code style="color:#b3a9e6">TRANSITIONS</code> dans <code style="color:#b3a9e6">barba-transitions.js</code>. Les namespaces viennent du nom de route Blade&nbsp;: <code style="color:#b3a9e6">data-barba-namespace="works"</code>. Choisis un départ et une arrivée — la règle appliquée s'allume.</p>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div style="display:flex;flex-direction:column;gap:7px">
                        <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.34)">Depuis</span>
                        <div data-blab-ns="from" style="display:flex;flex-wrap:wrap;gap:8px">
                            @foreach (['about' => 'à propos', 'works' => 'travaux', 'project' => 'projet'] as $ns => $label)
                                <button type="button" data-blab-ns-pick="from" data-blab-ns-val="{{ $ns }}" style="appearance:none;cursor:pointer;font-family:inherit;font-size:10px;letter-spacing:.2em;text-transform:uppercase;padding:7px 13px;border-radius:8px;border:1px solid rgba(233,233,237,.14);background:transparent;color:rgba(226,221,209,.52);transition:border-color .25s,background .25s,color .25s">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:7px">
                        <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.34)">Vers</span>
                        <div data-blab-ns="to" style="display:flex;flex-wrap:wrap;gap:8px">
                            @foreach (['about' => 'à propos', 'works' => 'travaux', 'project' => 'projet'] as $ns => $label)
                                <button type="button" data-blab-ns-pick="to" data-blab-ns-val="{{ $ns }}" style="appearance:none;cursor:pointer;font-family:inherit;font-size:10px;letter-spacing:.2em;text-transform:uppercase;padding:7px 13px;border-radius:8px;border:1px solid rgba(233,233,237,.14);background:transparent;color:rgba(226,221,209,.52);transition:border-color .25s,background .25s,color .25s">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <div data-blab-rules style="display:flex;flex-direction:column;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14"></div>
                <div style="display:flex;align-items:baseline;gap:14px;padding:18px 20px;border:1px solid rgba(145,132,217,.28);border-radius:10px;background:rgba(145,132,217,.06)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.44)">Appliquée</span>
                    <span data-blab-matched style="font-size:clamp(18px,2vw,26px);font-weight:200;letter-spacing:.14em;text-transform:uppercase;color:#e2ddd1">voile</span>
                    <span data-blab-matched-pair style="margin-left:auto;font-family:ui-monospace,monospace;font-size:11px;color:rgba(226,221,209,.42)">works → project</span>
                </div>
            </div>
        </section>

        {{-- ==================================================== 07 · Vues --- --}}
        <section class="lab-sec" data-blab-scr="6" data-demo="views" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">07</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Vues</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Ce qui doit vivre et mourir avec une page&nbsp;: reveals, scrub, parallax, et les sections épinglées ScrollTrigger de /projet. Montés dans <code style="color:#b3a9e6">afterEnter</code>, détruits dans <code style="color:#b3a9e6">beforeLeave</code>.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Le cycle réel du site — voir <code style="color:#b3a9e6">bootPageSystems()</code> et <code style="color:#b3a9e6">project.dispose()</code>. Sans démontage, chaque visite laisse un abonné derrière elle, et <code style="color:#b3a9e6">ScrollTrigger</code> garde les bornes de la page précédente.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>hooks.beforeLeave(({ current }) =&gt;</span>
                    <span style="padding-left:12px">current.namespace === 'web.project' &amp;&amp; project.dispose())</span>
                    <span>hooks.afterEnter(({ next }) =&gt; bootPageSystems(next))</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="display:flex;flex-direction:column;gap:12px;padding:20px;border:1px solid rgba(233,233,237,.10);border-radius:10px;background:#0b0c14">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                        <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.34)">Cycle de vie</span>
                        <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.44)">Vue active — <span data-blab-view-ns style="color:#b3a9e6">works</span></span>
                    </div>
                    <div data-blab-log style="display:flex;flex-direction:column;gap:0;min-height:232px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.6"></div>
                    <div style="display:flex;align-items:center;gap:14px;padding-top:6px">
                        <span data-el="scene" style="display:block;width:28px;height:28px;border:1px solid rgba(145,132,217,.5);border-top-color:#9184d9;border-radius:50%;opacity:.25"></span>
                        <span data-blab-scene-state style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.38)">Scène montée — 1 abonné</span>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span data-blab-view-count style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34)">0 navigation</span>
                    <button type="button" class="btn btn-primary" data-blab-play="views">Naviguer</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 08 · Élément partagé (concept) --- --}}
        <section class="lab-sec" data-blab-scr="7" data-demo="flip" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">08</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Élément partagé</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">La vignette cliquée devient l'image de tête de la fiche. Mesure avant, mesure après, une seule interpolation&nbsp;: le lien visuel remplace la transition.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Piste non branchée sur le site — /travaux n'a pas encore de vignettes de projet. Les deux conteneurs doivent coexister le temps de la mesure — donc <code style="color:#b3a9e6">sync: true</code>, et un couple d'attributs pour apparier la source et la cible.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>const a = clicked.getBoundingClientRect()</span>
                    <span>const b = hero.getBoundingClientRect()</span>
                    <span>hero.animate(invert(a, b), 760)</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#0b0c14;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02)">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div data-page="a" style="position:absolute;inset:0;opacity:1;padding:22px;display:flex;flex-direction:column;gap:16px;background:linear-gradient(180deg,#12131d,#05050a)">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.38)">Sélection</span>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;flex:1">
                                <button type="button" data-el="thumb" style="appearance:none;padding:0;cursor:pointer;border:1px solid rgba(233,233,237,.12);border-radius:6px;overflow:hidden;background:linear-gradient(150deg,#191a33,#0b0c14);display:flex;flex-direction:column;justify-content:flex-end;transition:border-color .25s">
                                    <span style="padding:8px 10px;font-size:9px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.62);text-align:left">Orbital</span>
                                </button>
                                <div style="border:1px solid rgba(233,233,237,.08);border-radius:6px;background:linear-gradient(150deg,#12131d,#0b0c14);display:flex;flex-direction:column;justify-content:flex-end">
                                    <span style="padding:8px 10px;font-size:9px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.28)">Forêt</span>
                                </div>
                                <div style="border:1px solid rgba(233,233,237,.08);border-radius:6px;background:linear-gradient(150deg,#12131d,#0b0c14);display:flex;flex-direction:column;justify-content:flex-end">
                                    <span style="padding:8px 10px;font-size:9px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.28)">Trou noir</span>
                                </div>
                            </div>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;opacity:0;display:flex;flex-direction:column;background:linear-gradient(180deg,#191a33,#05050a)">
                            <div data-el="hero" style="flex:1;border-bottom:1px solid rgba(233,233,237,.10);background:linear-gradient(150deg,#191a33,#0b0c14);display:flex;align-items:flex-end;padding:16px">
                                <span style="font-size:9px;letter-spacing:.24em;text-transform:uppercase;color:rgba(226,221,209,.44)">Image de tête</span>
                            </div>
                            <div data-el="heroText" style="flex:none;padding:18px 22px;display:flex;flex-direction:column;gap:8px;opacity:0">
                                <div style="font-size:clamp(18px,2.2vw,26px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.34)">Three.js — Theatre.js — 46 s</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34)">Clique la vignette Orbital</span>
                    <button type="button" class="btn btn-secondary" data-blab-reset-flip>Retour</button>
                </div>
            </div>
        </section>

        {{-- ==================================================== 09 · Préchargement (concept) --- --}}
        <section class="lab-sec" data-blab-scr="8" data-demo="prefetch" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">09</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Préchargement</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Le greffon <code style="color:#b3a9e6">@barba/prefetch</code> demande la page au survol du lien. Au clic, il ne reste que la peinture&nbsp;: la transition démarre sur un cache chaud.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Greffon non installé sur le site — cette scène simule seulement l'écart d'attente qu'il supprimerait. Survole les deux liens, puis clique&nbsp;: le relevé compare l'attente réseau observée.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span>import prefetch from '@barba/prefetch'</span>
                    <span>barba.use(prefetch)</span>
                    <span style="color:rgba(226,221,209,.34)">// data-barba-prefetch="false" pour exclure</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <div style="display:flex;flex-direction:column;gap:14px;padding:22px;border:1px solid rgba(233,233,237,.10);border-radius:10px;background:#0b0c14">
                    <div data-blab-prefetch="cold" style="display:flex;flex-direction:column;gap:10px;padding:16px;border:1px solid rgba(233,233,237,.08);border-radius:8px;background:rgba(233,233,237,.015)">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                            <button type="button" data-blab-link="cold" style="appearance:none;cursor:pointer;background:none;border:0;padding:0;font-family:inherit;font-size:13px;letter-spacing:.06em;color:#b3a9e6;text-align:left">/projet</button>
                            <span data-blab-link-state style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">inactif</span>
                        </div>
                        <div style="position:relative;height:1px;background:rgba(233,233,237,.10)">
                            <span data-blab-link-fill style="position:absolute;left:0;top:0;height:100%;width:100%;transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.3),#9184d9);transition:transform .3s linear;transform:scaleX(0)"></span>
                        </div>
                        <span data-blab-link-note style="font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.34)">sans greffon — requête au clic</span>
                    </div>
                    <div data-blab-prefetch="warm" style="display:flex;flex-direction:column;gap:10px;padding:16px;border:1px solid rgba(233,233,237,.08);border-radius:8px;background:rgba(233,233,237,.015)">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                            <button type="button" data-blab-link="warm" style="appearance:none;cursor:pointer;background:none;border:0;padding:0;font-family:inherit;font-size:13px;letter-spacing:.06em;color:#b3a9e6;text-align:left">/projet · prefetch</button>
                            <span data-blab-link-state style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.30)">inactif</span>
                        </div>
                        <div style="position:relative;height:1px;background:rgba(233,233,237,.10)">
                            <span data-blab-link-fill style="position:absolute;left:0;top:0;height:100%;width:100%;transform-origin:0 50%;background:linear-gradient(90deg,rgba(145,132,217,.3),#9184d9);transition:transform .3s linear;transform:scaleX(0)"></span>
                        </div>
                        <span data-blab-link-note style="font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:rgba(226,221,209,.34)">prefetch au survol</span>
                    </div>
                </div>
                <div style="display:flex;align-items:baseline;gap:16px;padding:18px 20px;border:1px solid rgba(145,132,217,.28);border-radius:10px;background:rgba(145,132,217,.06)">
                    <span style="font-size:9px;letter-spacing:.28em;text-transform:uppercase;color:rgba(226,221,209,.44)">Attente réseau</span>
                    <span data-blab-wait style="font-size:clamp(20px,2.4vw,30px);font-weight:200;letter-spacing:.06em;color:#e2ddd1;font-variant-numeric:tabular-nums">—</span>
                    <span data-blab-wait-from style="margin-left:auto;font-family:ui-monospace,monospace;font-size:11px;color:rgba(226,221,209,.42)">aucune navigation</span>
                </div>
            </div>
        </section>

        {{-- ==================================================== 10 · Toile persistante (concept) --- --}}
        <section class="lab-sec" data-blab-scr="9" data-demo="canvas" style="position:relative;min-height:100vh;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:clamp(30px,4vw,72px);align-items:center;padding:clamp(84px,10vh,120px) clamp(52px,7vw,120px) clamp(56px,8vh,96px) clamp(64px,8vw,140px)">
            <span style="position:absolute;right:clamp(20px,6vw,120px);top:14vh;font-size:clamp(120px,20vw,300px);font-weight:200;line-height:1;letter-spacing:-.05em;color:rgba(233,233,237,.04);pointer-events:none">10</span>
            <div style="display:flex;flex-direction:column;gap:20px;max-width:52ch">
                <span style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                <h1 style="margin:0;font-size:clamp(32px,5vw,68px);font-weight:200;letter-spacing:-.03em;line-height:1">Toile persistante</h1>
                <p style="margin:0;font-size:clamp(14px,1.05vw,16px);line-height:1.75;color:rgba(226,221,209,.62)">Un canvas Three.js pourrait vivre hors du conteneur Barba&nbsp;: jamais démonté, seule l'interface serait échangée pendant que la scène continue de tourner.</p>
                <p style="margin:0;font-size:13px;line-height:1.7;color:rgba(226,221,209,.40)">Non appliqué&nbsp;: sur ce site, chaque cinématique (<code style="color:#b3a9e6">home-cinematic</code>, <code style="color:#b3a9e6">forest-cinematic</code>, <code style="color:#b3a9e6">blackhole-cinematic</code>) est une page à part, hors du wrapper Barba — un vrai chargement, délibérément. Cette scène illustre ce qu'il faudrait pour les enchaîner sans réinitialiser Three.js.</p>
                <div style="display:flex;flex-direction:column;gap:5px;padding:14px 16px;border:1px solid rgba(233,233,237,.09);border-radius:8px;font-family:ui-monospace,monospace;font-size:11px;line-height:1.7;color:rgba(226,221,209,.52)">
                    <span style="color:rgba(226,221,209,.34)">&lt;!-- hors du conteneur --&gt;</span>
                    <span>&lt;orbital-stage&gt;&lt;/orbital-stage&gt;</span>
                    <span>&lt;main data-barba="container"&gt;…&lt;/main&gt;</span>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="position:relative;aspect-ratio:16/10;border:1px solid rgba(233,233,237,.10);border-radius:10px;overflow:hidden;background:#05050a;display:flex;flex-direction:column">
                    <div style="flex:none;display:flex;align-items:center;gap:12px;padding:11px 14px;border-bottom:1px solid rgba(233,233,237,.08);background:rgba(233,233,237,.02);position:relative;z-index:3">
                        <span style="display:flex;gap:5px"><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i><i style="display:block;width:5px;height:5px;border-radius:50%;background:rgba(233,233,237,.20)"></i></span>
                        <span style="font-size:10px;letter-spacing:.14em;color:rgba(226,221,209,.42)">lakeust.fr<span data-el="url" style="color:rgba(226,221,209,.72)">/travaux</span></span>
                    </div>
                    <div style="position:relative;flex:1;overflow:hidden">
                        <div style="position:absolute;inset:0;z-index:0;background:radial-gradient(70% 60% at 30% 30%,rgba(145,132,217,.22),transparent 70%),#05050a"></div>
                        <div style="position:absolute;inset:-40%;z-index:0;background-image:radial-gradient(1px 1px at 20px 30px,rgba(233,233,237,.5),transparent),radial-gradient(1px 1px at 120px 90px,rgba(233,233,237,.35),transparent),radial-gradient(1px 1px at 220px 40px,rgba(233,233,237,.45),transparent);background-size:320px 200px;animation:lab-drift 14s linear infinite"></div>
                        <span style="position:absolute;left:50%;top:50%;z-index:1;width:52%;aspect-ratio:1;transform:translate(-50%,-50%);border:1px solid rgba(145,132,217,.34);border-radius:50%"></span>
                        <div data-page="a" style="position:absolute;inset:0;z-index:2;opacity:1;padding:26px;display:flex;flex-direction:column;justify-content:space-between">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.44)">Index</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">TRAVAUX</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.34)">12 projets — 2024 / 2026</span>
                        </div>
                        <div data-page="b" style="position:absolute;inset:0;z-index:2;opacity:0;padding:26px;display:flex;flex-direction:column;justify-content:space-between">
                            <span style="font-size:9px;letter-spacing:.30em;text-transform:uppercase;color:rgba(226,221,209,.44)">Fiche</span>
                            <div style="display:flex;flex-direction:column;gap:10px">
                                <div style="font-size:clamp(22px,2.6vw,34px);font-weight:200;letter-spacing:.18em;color:#e2ddd1">ORBITAL</div>
                                <span style="width:120px;height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
                            </div>
                            <span style="font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:rgba(226,221,209,.34)">Three.js — Theatre.js — 46 s</span>
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <span style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(226,221,209,.34)">La scène ne redémarre pas</span>
                    <button type="button" class="btn btn-primary" data-blab-play="canvas">Naviguer</button>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection

