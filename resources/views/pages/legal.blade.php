@extends('layouts.site')

@section('title', 'Mentions légales — Lakeust Works')
@section('cat', 'Legals')

@section('content')
<head>
    @vite(['resources/css/app.css', 'resources/css/web.css'])

    {{-- Page purement française : la CNIL/le RGPD référencés ici sont des
         entités françaises, et une "traduction" de mentions légales
         introduirait un risque de divergence avec le texte qui fait foi.
         Le lien du footer reste bilingue (data-i18n="footer.legal"), la
         page elle-même ne l'est pas — voir aussi web/about.blade.php pour
         le nav par défaut réutilisé ici (pas de $nav dédié). --}}
</head>

<section class="section-tight">
    <div class="wrap" style="max-width:74ch;">
        <div data-reveal="mask">
            <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-3);">Lakeust Works</span></span>
            <span class="mask-line"><h1 class="t-h1">Mentions légales</h1></span>
        </div>
    </div>
</section>

<section class="section-tight">
    <div class="wrap" style="max-width:74ch;display:flex;flex-direction:column;gap:var(--s-8);">

        <div data-reveal="rise">
            <h2 class="t-h3">Éditeur du site</h2>
            <p class="t-body" style="margin-top:var(--s-3);">
                Le présent site, accessible à l'adresse <strong>lakeust.works</strong>, est édité par :
            </p>
            <p class="t-body" style="margin-top:var(--s-3);line-height:1.9;">
                Renaud Quentin<br>
                Nom commercial : Lakeust Works<br>
                Statut : particulier (passage en auto-entrepreneur prévu)<br>
                Adresse : 13720, France<br>
                Email : <a class="link-inline" href="mailto:lakeustworks@gmail.com">lakeustworks@gmail.com</a>
            </p>
            <p class="t-body" style="margin-top:var(--s-3);">
                Directeur de la publication : Renaud Quentin
            </p>
        </div>

        <div data-reveal="rise">
            <h2 class="t-h3">Hébergement</h2>
            <p class="t-body" style="margin-top:var(--s-3);">Le site est hébergé par :</p>
            <p class="t-body" style="margin-top:var(--s-3);line-height:1.9;">
                GitHub, Inc.<br>
                88 Colin P Kelly Jr St<br>
                San Francisco, CA 94107<br>
                États-Unis<br>
                Site : <a class="link-inline" href="https://github.com" target="_blank" rel="noopener">github.com</a>
            </p>
        </div>

        <div data-reveal="rise">
            <h2 class="t-h3">Activité</h2>
            <p class="t-body" style="margin-top:var(--s-3);">Lakeust Works est un studio indépendant proposant :</p>
            <ul class="t-body" style="margin:var(--s-3) 0 0;padding-left:1.2em;display:flex;flex-direction:column;gap:6px;">
                <li>développement de jeux vidéo</li>
                <li>création de sites web</li>
                <li>projets personnels</li>
                <li>prestations sur demande</li>
            </ul>
        </div>

        <div data-reveal="rise">
            <h2 class="t-h3">Propriété intellectuelle</h2>
            <p class="t-body" style="margin-top:var(--s-3);">
                Sauf mention contraire, l'ensemble du contenu présent sur le site — code source, textes,
                graphismes, illustrations, vidéos, modèles 3D, logos, animations, scènes WebGL, et autres
                éléments originaux — est la propriété exclusive de Lakeust Works.
            </p>
            <p class="t-body" style="margin-top:var(--s-3);">
                Toute reproduction, représentation, modification ou exploitation commerciale, totale ou
                partielle, est interdite sans autorisation préalable.
            </p>
            <p class="t-body" style="margin-top:var(--s-3);">
                Les noms, marques et contenus appartenant à des tiers restent la propriété de leurs
                titulaires respectifs.
            </p>
        </div>

        <div data-reveal="rise">
            <h2 class="t-h3">Responsabilité</h2>
            <p class="t-body" style="margin-top:var(--s-3);">
                L'éditeur s'efforce de fournir des informations exactes et à jour, sans garantir l'absence
                d'erreurs ou d'omissions. Il ne peut être tenu responsable :
            </p>
            <ul class="t-body" style="margin:var(--s-3) 0 0;padding-left:1.2em;display:flex;flex-direction:column;gap:6px;">
                <li>d'une indisponibilité temporaire du site,</li>
                <li>d'une mauvaise utilisation du contenu,</li>
                <li>des pratiques des sites tiers accessibles via des liens externes.</li>
            </ul>
        </div>

        <div data-reveal="rise">
            <h2 class="t-h3">Données personnelles &amp; Cookies</h2>
            <p class="t-body" style="margin-top:var(--s-3);">
                Le site n'utilise aucun cookie publicitaire, ni outil de mesure d'audience.
            </p>
            <p class="t-body" style="margin-top:var(--s-3);">Le site peut utiliser :</p>
            <ul class="t-body" style="margin:var(--s-3) 0 0;padding-left:1.2em;display:flex;flex-direction:column;gap:6px;">
                <li>un cookie technique de session généré automatiquement par Laravel (sécurité, gestion CSRF)</li>
                <li>un stockage localStorage pour mémoriser la préférence de langue (FR/EN)</li>
            </ul>
            <p class="t-body" style="margin-top:var(--s-3);">
                Ces éléments sont strictement nécessaires au fonctionnement du site et exemptés de
                consentement selon le RGPD.
            </p>
        </div>

        <div data-reveal="rise">
            <h2 class="t-h3">Contact par email</h2>
            <p class="t-body" style="margin-top:var(--s-3);">
                Lorsque vous contactez Lakeust Works par email, les informations transmises sont utilisées
                uniquement pour répondre à votre demande et gérer une éventuelle relation professionnelle.
                Elles ne sont jamais revendues à des tiers.
            </p>
            <p class="t-body" style="margin-top:var(--s-3);">Conformément au RGPD, vous pouvez demander :</p>
            <ul class="t-body" style="margin:var(--s-3) 0 0;padding-left:1.2em;display:flex;flex-direction:column;gap:6px;">
                <li>l'accès à vos données,</li>
                <li>leur rectification,</li>
                <li>leur effacement,</li>
                <li>la limitation ou la portabilité,</li>
                <li>ou vous opposer à leur traitement lorsque ce droit s'applique.</li>
            </ul>
            <p class="t-body" style="margin-top:var(--s-3);">
                Contact : <a class="link-inline" href="mailto:lakeustworks@gmail.com">lakeustworks@gmail.com</a>
            </p>
            <p class="t-body" style="margin-top:var(--s-3);">
                Vous pouvez également déposer une réclamation auprès de la
                <a class="link-inline" href="https://www.cnil.fr" target="_blank" rel="noopener">CNIL</a>.
            </p>
        </div>

        <div data-reveal="rise" style="border-top:1px solid var(--divider);padding-top:var(--s-6);">
            <p class="t-body" style="color:var(--text-3);font-size:13px;">
                © 2024–{{ date('Y') }} Lakeust Works
            </p>
            <p class="t-body" style="color:var(--text-4);font-size:12px;margin-top:4px;">
                Dernière mise à jour : 02 septembre 2026
            </p>
        </div>

    </div>
</section>
@endsection
