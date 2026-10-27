{{-- Sous-gabarit des pages "Lakeust Web" : s'insère ENTRE layouts/site.blade.php
     et la page. Chaîne : page -> pages.web.subsite -> layouts.site.

     layouts.site fournit déjà nav, footer, rideau Barba et le
     <main data-barba="container"> autour de @yield('content') : ce fichier
     ne doit donc PAS redéfinir de wrapper/main/footer (deux conteneurs
     Barba imbriqués casseraient les transitions, et le footer serait
     rendu deux fois). Il ne porte que ce qui est propre au pôle Web :
       - la liste de liens $nav (nav ET footer la lisent) ;
       - le CSS du pôle Web (entry-css, rendu dans le <head> par le layout) ;
       - l'enveloppe .theme-web (css/pages/web-theme.css) qui redéfinit les
         jetons de couleur pour tout le contenu.

     Une page Web étend 'pages.web.subsite', déclare ses sections title et
     description, et place son contenu dans la section 'sub-content'. Pour un
     menu différent sur une page précise, elle définit $nav dans son propre
     bloc php (variable transmise de gabarit en gabarit, voir layouts.site).

     Attention : ne jamais écrire de directive Blade (arobase + nom) dans un
     commentaire. Blade extrait les blocs php AVANT de retirer les commentaires :
     le mot-clé php dans ce commentaire avalerait tout jusqu'au premier
     endphp réel et produirait une erreur de syntaxe. --}}
@extends('layouts.site')

@section('cat', 'Web')
@section('entry-css', 'resources/css/web.css')

@section('content')
    @php
        $nav = $nav ?? [
            ['route' => 'web.about', 'i18n' => 'nav.about', 'keep' => true],
            /* ['route' => 'web.works', 'i18n' => 'nav.works', 'keep' => true], */
            ['route' => 'web.lab',   'i18n' => 'nav.lab',   'keep' => false, 'external' => true],
        ];
    @endphp

    <div class="theme-web">
        @yield('sub-content')
    </div>
@endsection
