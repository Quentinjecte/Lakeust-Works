{{-- Lakeust Arts · fiche d'un modèle 3D — visualiseur large avec onglets
     (rendu / fil de fer / rotation), fiche technique, étapes de production,
     emplacements pour les rendus, modèles liés.
     $model vient de la route arts.model ({slug} -> ModelCatalog::find). --}}
@extends('pages.arts.subsite')
@section('title', $model['name'] . ' — Modèle 3D — Lakeust Arts')
@section('description', \Illuminate\Support\Str::limit($model['desc']['fr'], 155))

@section('sub-content')
    @use('App\Support\ModelCatalog')
    @php
        $m = $model;
        $related = array_slice(array_values(array_filter(ModelCatalog::all(), fn ($x) => $x['slug'] !== $m['slug'])), 0, 3);
        $steps = ModelCatalog::steps();   // partagées avec la méthode de arts.about
    @endphp

    <div class="ar-wrap">
        <div class="ar-fiche-top">
            <div class="ar-crumb" data-hero="1">
                <a href="{{ route('arts.models') }}" data-barba-prevent><span class="i18n-fr">Modèles 3D</span><span class="i18n-en">3D Models</span></a>
                / <span style="color:var(--text-2)">{{ $m['name'] }}</span>
            </div>
            <span class="ar-badges">{!! ModelCatalog::badges($m['tools']) !!}</span>
        </div>

        <div class="ar-fiche">
            <div class="ar-fiche-main">
                <div class="ar-stage" style="--c:{{ $m['accent'] }}">
                    <lw-model-viewer data-ar-viewer controls hud autorotate
                                     demo="{{ $m['demo'] }}" accent="{{ $m['accent'] }}" load="{{ $m['load'] }}"
                                     @if ($m['src']) src="{{ asset($m['src']) }}" @endif style="position:absolute;inset:0"></lw-model-viewer>
                </div>
                <div class="ar-tabs" role="tablist">
                    <button type="button" class="ar-tab" data-ar-view="render" role="tab"><span class="i18n-fr">Rendu</span><span class="i18n-en">Render</span></button>
                    <button type="button" class="ar-tab" data-ar-view="wire" role="tab"><span class="i18n-fr">Maillage</span><span class="i18n-en">Wireframe</span></button>
                    <button type="button" class="ar-tab is-on" data-ar-view="spin" role="tab"><span class="i18n-fr">Rotation auto</span><span class="i18n-en">Auto-rotate</span></button>
                </div>
            </div>

            <aside class="ar-side">
                <div>
                    <div class="ar-eyebrow" style="margin-bottom:var(--space-4)"><span class="i18n-fr">{{ $m['category']['fr'] }}</span><span class="i18n-en">{{ $m['category']['en'] }}</span> · {{ $m['year'] }}</div>
                    <h1>{{ $m['name'] }}</h1>
                </div>
                <p class="ar-prose"><span class="i18n-fr">{{ $m['desc']['fr'] }}</span><span class="i18n-en">{{ $m['desc']['en'] }}</span></p>

                <div class="ar-spec">
                    <div><span class="k"><span class="i18n-fr">Outils</span><span class="i18n-en">Tools</span></span><span class="v">{!! ModelCatalog::badges($m['tools']) !!}</span></div>
                    <div><span class="k">Triangles</span><span class="v">{{ $m['tris'] }}</span></div>
                    <div><span class="k">Textures</span><span class="v">{{ $m['textures'] }}</span></div>
                    <div><span class="k"><span class="i18n-fr">Rig / animation</span><span class="i18n-en">Rig / animation</span></span><span class="v"><span class="i18n-fr">{{ $m['rig']['fr'] }}</span><span class="i18n-en">{{ $m['rig']['en'] }}</span></span></div>
                    <div><span class="k">Format</span><span class="v">glTF 2.0</span></div>
                    <div><span class="k"><span class="i18n-fr">Licence</span><span class="i18n-en">License</span></span><span class="v"><span class="i18n-fr">Tous droits réservés</span><span class="i18n-en">All rights reserved</span></span></div>
                </div>
                <div class="ar-tags">@foreach ($m['tags'] as $tag)<span class="ar-pill">{{ $tag }}</span>@endforeach</div>
                <p class="ar-rights">
                    <span class="i18n-fr">Ce modèle est ma création et n'est pas téléchargeable. Toute copie, extraction ou réutilisation sans mon accord est interdite. Certains modèles sont disponibles à l'achat sur des plateformes tierces.</span>
                    <span class="i18n-en">This model is my own work and is not available for download. Any copying, extraction or reuse without my permission is prohibited. Some models can be purchased on third-party platforms.</span>
                </p>
                @if (! empty($m['buy']))
                    <a class="ar-btn" href="{{ $m['buy']['url'] }}" target="_blank" rel="noopener"><span class="i18n-fr">Acheter sur {{ $m['buy']['label'] }}</span><span class="i18n-en">Buy on {{ $m['buy']['label'] }}</span></a>
                @endif
            </aside>
        </div>
@endsection
