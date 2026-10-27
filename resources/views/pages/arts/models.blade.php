{{-- Lakeust Arts · liste des modèles 3D — grille de cartes avec filtre par outil.
     Chaque carte monte un mini-visualiseur qui tourne au survol (et se libère
     hors écran). Données : App\Support\ModelCatalog. --}}
@extends('pages.arts.subsite')
@section('title', 'Modèles 3D — Lakeust Arts')
@section('description', "Lakeust Arts : modèles 3D (props, personnages, décors) créés avec Blender et Blockbench, à faire tourner directement dans le navigateur.")

@section('sub-content')
    @use('App\Support\ModelCatalog')
    @php
        $models = ModelCatalog::all();
        $usedTools = collect($models)->pluck('tools')->flatten()->unique()->values();
    @endphp

    <header class="ar-hero">
        <div class="ar-eyebrow" data-hero="1"><span class="i18n-fr">Lakeust Arts · Atelier 3D</span><span class="i18n-en">Lakeust Arts · 3D workshop</span></div>
        <h1 class="textured gradient2 ar-title" data-hero="1"><span class="i18n-fr">Modèles 3D</span><span class="i18n-en">3D Models</span></h1>
        <p class="ar-lead" data-hero="1">
            <span class="i18n-fr">Props, personnages et décors modélisés sous Blender et Blockbench — à faire tourner directement dans le navigateur.</span>
            <span class="i18n-en">Props, characters and environments modelled in Blender and Blockbench — spin them right in your browser.</span>
        </p>

        <div class="ar-filters" data-hero="1">
            <button type="button" class="ar-chip is-on" data-ar-filter="all"><span class="i18n-fr">Tous</span><span class="i18n-en">All</span></button>
            @foreach ($usedTools as $t)
                <button type="button" class="ar-chip" data-ar-filter="{{ $t }}">{{ ModelCatalog::TOOLS[$t]['label'] ?? $t }}</button>
            @endforeach
        </div>
        <div class="ar-rule"></div>
    </header>

    <div class="ar-wrap">
        <div class="ar-grid">
            @foreach ($models as $m)
                <a class="ar-card" data-ar-card data-tool="{{ implode(' ', $m['tools']) }}" data-reveal="rise" data-barba-prevent
                   href="{{ $m['url'] }}" style="--c:{{ $m['accent'] }}">
                    <lw-model-viewer demo="{{ $m['demo'] }}" accent="{{ $m['accent'] }}" load="{{ $m['load'] }}" hover-rotate
                                     @if ($m['src']) src="{{ asset($m['src']) }}" @endif></lw-model-viewer>
                    <div class="ar-card-top">
                        <span class="ar-badges">{!! ModelCatalog::badges($m['tools']) !!}</span>
                        <span class="ar-pill">{{ $m['tris'] }} tris</span>
                    </div>
                    <div class="ar-card-body">
                        <h3 class="ar-card-name">{{ $m['name'] }}</h3>
                        <div class="ar-card-meta"><span class="i18n-fr">{{ $m['category']['fr'] }}</span><span class="i18n-en">{{ $m['category']['en'] }}</span> · {{ $m['year'] }}</div>
                        <div class="ar-tags">
                            @foreach ($m['tags'] as $tag)<span class="ar-pill">{{ $tag }}</span>@endforeach
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
