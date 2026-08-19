{{-- Gabarit des pages secondaires : rideau de transition, nav, footer. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lakeust Works')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @php
            /* Base commune à toutes les pages, plus l'entrée propre à la page
               (déclarée via @section('entry', ...) / @section('entry-css', ...))
               quand elle en a une — pas de GSAP/Lenis chargés hors du Lab. */
            $vite = ['resources/css/app.css', 'resources/js/app.js', 'resources/js/lab.js'];
            if ($__env->hasSection('entry-css')) $vite[] = trim($__env->yieldContent('entry-css'));
            if ($__env->hasSection('entry')) $vite[] = trim($__env->yieldContent('entry'));
        @endphp
        @vite($vite)
    @endif
    @stack('head')
</head>
<body>

<div class="curtain" data-curtain><i></i><i></i><i></i></div>
<div class="nav-progress" data-progress></div>

@php
    $nav = [
        ['route' => 'about',   'label' => 'À propos', 'keep' => true],
        ['route' => 'works',   'label' => 'Travaux',  'keep' => true],
        ['route' => 'project', 'label' => 'Projet',   'keep' => false],
        ['route' => 'lab',     'label' => 'Scroll Lab', 'keep' => false],
    ];
@endphp

<header class="nav" data-nav>
    <a class="nav-brand" href="{{ route('welcome') }}"><span class="dot"></span>Lakeust Works</a>
    <nav class="nav-links">
        @foreach ($nav as $item)
            <a class="nav-link"
               href="{{ route($item['route']) }}"
               @if (request()->routeIs($item['route'])) aria-current="page" @endif
               @if ($item['keep']) data-keep @endif>{{ $item['label'] }}</a>
        @endforeach
        <a class="nav-link" href="{{ route('about') }}#contact">Contact</a>
    </nav>
</header>

<main class="shell page-in">
    @yield('content')

    <footer class="footer">
        <div class="wrap footer-row">
            <div>
                <span class="label" style="display:block;margin-bottom:var(--s-2);">Lakeust Works</span>
                <span class="t-body" style="font-size:13px;">Placeholder — mentions, année, localisation.</span>
            </div>
            <div style="display:flex;gap:var(--s-5);">
                @foreach ($nav as $item)
                    @unless (request()->routeIs($item['route']))
                        <a class="link-inline" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                    @endunless
                @endforeach
            </div>
        </div>
    </footer>
</main>

@stack('scripts')
</body>
</html>
