{{-- Gabarit des pages secondaires : rideau de transition, nav, footer. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lakeust Works -- Web')</title>
    <link rel="icon" href="{{ asset('images/Icon_Lakeust_nobg.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    @include('partials.analytics')

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @php
            /* Base commune à toutes les pages, plus l'entrée propre à la page
               (déclarée via @section('entry', ...) / @section('entry-css', ...))
               quand elle en a une — pas de GSAP/Lenis chargés hors du Lab :
               chaque page du laboratoire déclare son propre entry JS/CSS
               (voir le <head> inline dans @section('content') de chacune),
               rien de spécifique à un lab ne vit dans cette base commune. */
            $vite = ['resources/css/app.css', 'resources/js/core/app.js'];
            if ($__env->hasSection('entry-css')) $vite[] = trim($__env->yieldContent('entry-css'));
            if ($__env->hasSection('entry')) $vite[] = trim($__env->yieldContent('entry'));
        @endphp
        @vite($vite)
    @endif
    @stack('head')
</head>
<body>

<div class="nav-progress" data-progress></div>

{{-- Recouvrement persistant des quatre transitions Barba (voir
     barba-transitions.js) : vit hors du wrapper, jamais remplacé par un
     swap de page, chaque transition s'occupe elle-même de remettre ses
     propres pièces à l'état neutre en fin de course. --}}
<div class="pxo" data-pxo aria-hidden="true">
    <div class="pxo-veil" data-pxo="veil"></div>
    <div class="pxo-panel pxo-panel-top" data-pxo="panelTop"></div>
    <div class="pxo-panel pxo-panel-bottom" data-pxo="panelBottom"></div>
    <div class="pxo-line" data-pxo="line"></div>
    <div class="pxo-dim" data-pxo="dim"></div>
    <div class="pxo-scan" data-pxo="scan"></div>
    <div class="pxo-readout" data-pxo="readout"><span data-pxo="route"></span></div>
    <div class="pxo-hole" data-pxo="hole"></div>
    <div class="pxo-ring" data-pxo="ring"></div>
</div>

@php
    /* 'external' : page hors gabarit Barba (pas de data-barba="container" —
       welcome.blade.php est un document autonome, et les pages labs/* logent
       leur @vite dans @section('content'), donc jamais exécuté si Barba se
       contente d'injecter le HTML fetché). data-barba-prevent force Barba à
       laisser passer une vraie navigation pour ces liens-là plutôt que de
       tenter un swap SPA voué à rester bloqué (voir barba-transitions.js).

       Personnalisable par page comme 'title' (@yield('title', défaut) /
       @section('title', ...)), mais @section/@yield ne transportent que du
       texte rendu, pas un tableau PHP : une page qui veut son propre menu
       définit simplement $nav dans son propre bloc @php avant @extends —
       Blade transmet automatiquement les variables du enfant à la mise en
       page (voir compileExtends). Ici on ne pose que la valeur par défaut,
       si rien n'a été fourni. */
    $nav = $nav ?? [
        ['route' => 'web.about',   'i18n' => 'nav.about', 'keep' => true],
        ['route' => 'web.works',   'i18n' => 'nav.works',  'keep' => true],
        ['route' => 'web.lab',     'i18n' => 'nav.lab', 'keep' => false, 'external' => true],
    ];
@endphp

<header class="" data-nav>
    <nav>
        {{-- .nav-links traînait ici en trop (copié depuis welcome.blade.php) :
             core/responsive.css cache tout .nav-links .nav-link:not([data-keep])
             sous 620px — pensé pour l'ancienne barre à une seule rangée, mais
             ça s'appliquait aussi via cet ancêtre au menu déroulant du dessous,
             qui a pourtant toute la place pour les 4 liens (d'où "Laboratoire"
             et "Contact" invisibles malgré grid-cols-4). --}}
        <div class="sm:hidden">
            <div class="nav">
                <div class="w-screen ">
                    <div class="flex justify-around ">
                        <button type="button" command="--toggle" commandfor="mobile-menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-500">
                            <span class="absolute -inset-0.5"></span>
                            <span class="sr-only">Open main menu</span>

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
                                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <a class="nav-brand text-[9px]" href="{{ route('welcome') }}" data-barba-prevent>Lakeust Works<span>- @yield('cat', 'Web')</span></a>

                        <div class="wl-lang" data-lang-switch></div>
                    </div>
                </div>
                {{-- grid grid-cols-4 directement sur le conteneur des liens : la
                     grille n'agit que sur ses enfants DIRECTS — avec un
                     <div class="nav-links"> imbriqué entre les deux, la grille
                     n'avait qu'un seul enfant (ce div), qui prenait toute la
                     1ère colonne pendant que .nav-links (display:flex, voir
                     nav.css) essayait de faire tenir les 4 liens dans ce quart
                     d'espace — d'où l'affichage cassé. --}}
                <el-disclosure id="mobile-menu" hidden class="absolute inset-x-0 top-full border-t border-[var(--line)] backdrop-blur-[14px]" style="background:color-mix(in srgb, var(--bg-1) 92%, transparent);">
                    <div class="grid grid-cols-4 text-[8px] md:text-[11px]" style="padding:var(--s-5);gap:2px;">
                        @foreach ($nav as $item)
                            <a class="nav-link"
                               href="{{ route($item['route']) }}"
                               data-route="{{ $item['route'] }}"
                               data-i18n="{{ $item['i18n'] }}"
                               @if (request()->routeIs($item['route'])) aria-current="page" @endif
                               @if ($item['keep']) data-keep @endif
                               @if (!empty($item['external'])) data-barba-prevent @endif></a>
                        @endforeach 
                        <a class="nav-link" style="padding:0 10px;" href="{{ route('web.about') }}#contact" data-i18n="nav.contact"></a>
                    </div>
                </el-disclosure>
            </div>
        </div>

        <div class="hidden sm:block">
            <div class="nav">
                <a class="nav-brand" href="{{ route('welcome') }}" data-barba-prevent><span class="dot"></span>Lakeust Works<span>- @yield('cat', 'Web')</span></a>
                <div class="nav-links text-[11px]">
                    @foreach ($nav as $item)
                        <a class="nav-link"
                           href="{{ route($item['route']) }}"
                           data-route="{{ $item['route'] }}"
                           data-i18n="{{ $item['i18n'] }}"
                           @if (request()->routeIs($item['route'])) aria-current="page" @endif
                           @if ($item['keep']) data-keep @endif
                           @if (!empty($item['external'])) data-barba-prevent @endif></a>
                    @endforeach
                    <a class="nav-link" href="{{ route('web.about') }}#contact" data-i18n="nav.contact"></a>
                    <div class="nav-lang" data-lang-switch></div>
                </div>
            </div>
        </div>
    </nav>
</header>
<!--    
-->
{{-- Le nav et le footer vivent hors du wrapper : chrome persistant, jamais
     remplacé par Barba. Seul <main> — le conteneur — est échangé d'une
     navigation à l'autre. Le namespace vient directement du nom de route :
     about / works / project, exactement ce qu'attendent les règles de
     TRANSITIONS dans barba-transitions.js. --}}
<div data-barba="wrapper">
    <main class="shell page-in" data-barba="container" data-barba-namespace="{{ request()->route()->getName() }}">
        @yield('content')
    </main>
</div>

<footer class="footer">
    <div class="wrap footer-row">
        <div>
            <span class="label" style="display:block;margin-bottom:var(--s-2);" data-i18n="footer.brand"></span>
            <span class="t-body" style="font-size:13px;" data-i18n="footer.tagline"></span>
        </div>
        <div style="display:flex;gap:var(--s-5);">
            @foreach ($nav as $item)
                @unless (request()->routeIs($item['route']))
                    <a class="link-inline" href="{{ route($item['route']) }}" data-i18n="{{ $item['i18n'] }}" @if (!empty($item['external'])) data-barba-prevent @endif></a>
                @endunless
            @endforeach
            @unless (request()->routeIs('legal'))
                <a class="link-inline" href="{{ route('legal') }}" data-i18n="footer.legal"></a>
            @endunless
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
