{{-- Sous-gabarit des pages « Lakeust Arts » (troisième branche du site, à côté
     de Lakeust Web et Lakeust Studio). Même principe que pages/web/subsite :
     chaîne page -> pages.arts.subsite -> layouts.site, qui fournit déjà nav,
     footer, rideau Barba et le conteneur Barba — on n'en redéfinit aucun.

     Ce fichier ne porte que ce qui est propre à Arts :
       - la marque « Arts » dans la barre de navigation (section cat) ;
       - la liste de liens $nav (nav ET footer la lisent), surchargeable par
         une page qui définit son propre $nav ;
       - $contactRoute : le lien « Contact » du menu vise la section #contact de
         arts.about plutôt que celle de la page À propos du Web ;
       - le CSS et le JS de la branche (sections entry-css et entry, rendues
         dans le head par le layout) ;
       - l'enveloppe .ar sur laquelle s'accrochent les styles de arts.css.

     Une page Arts étend 'pages.arts.subsite', déclare title et description, et
     place son contenu dans la section 'sub-content'. Les liens vers ces pages
     portent data-barba-prevent : leur JS (visualiseur 3D) ne serait jamais
     exécuté par un simple échange de conteneur Barba.

     Attention : ne jamais écrire de directive Blade (arobase + nom) dans un
     commentaire, Blade extrait certains blocs avant de retirer les commentaires. --}}
@extends('layouts.site')

@section('cat', 'Arts')
@section('entry-css', 'resources/css/arts.css')
@section('entry', 'resources/js/pages/arts-models.js')

@section('content')
    @php
        $nav = $nav ?? \App\Support\ModelCatalog::artsNav();
        $contactRoute = $contactRoute ?? 'arts.about';
    @endphp

    <div class="ar">
        @yield('sub-content')
    </div>
@endsection
