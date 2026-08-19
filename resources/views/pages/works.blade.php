@extends('layouts.site')

@section('title', 'Travaux — Lakeust Works')

@php
    /* Placeholder : à remplacer par une source de données réelle. */
    $selection = [
        ['titre' => 'Projet 01', 'annee' => '2026'],
        ['titre' => 'Projet 02', 'annee' => '2025'],
        ['titre' => 'Projet 03', 'annee' => '2025'],
        ['titre' => 'Projet 04', 'annee' => '2024'],
        ['titre' => 'Projet 05', 'annee' => '2023'],
    ];
    $archive = [
        ['titre' => 'Projet archivé 06', 'annee' => '2022'],
        ['titre' => 'Projet archivé 07', 'annee' => '2022'],
        ['titre' => 'Projet archivé 08', 'annee' => '2021'],
        ['titre' => 'Projet archivé 09', 'annee' => '2020'],
    ];
@endphp

@section('content')
    <section class="section" style="padding:clamp(120px,18vh,200px) 0 var(--s-6);">
        <div class="wrap">
            <div data-reveal="mask" style="w-lg">
                <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-4);">Index — 2020 / 2026</span></span>
                <span class="mask-line"><h1 class="t-display ">Travaux</h1></span>
            </div>
            <p class="t-lead" data-reveal="blur" data-reveal-delay="200" style="margin-top:var(--s-5);">
                Texte de remplacement. La bande ci-dessous se déplace horizontalement au fil du scroll vertical.
            </p>
        </div>
    </section>

    {{-- dérive horizontale : le scroll vertical translate la piste --}}
    <section class="drift" data-drift>
        <div class="drift-sticky">
            <div class="drift-head">
                <span class="label">Sélection</span>
                <span class="drift-count label" data-drift-count>01 / {{ str_pad(count($selection), 2, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="drift-track">
                @foreach ($selection as $i => $projet)
                    <a class="drift-item" href="{{ route('project') }}">
                        <div class="media media-4-3 media-hover" @if ($i === 0) data-reveal="frame" @endif><div class="media-fill"></div></div>
                        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-top:var(--s-3);">
                            <span class="t-h3">{{ $projet['titre'] }}</span><span class="label">{{ $projet['annee'] }}</span>
                        </div>
                        <span class="t-body" style="font-size:13px;">Placeholder — une ligne de description.</span>
                    </a>
                @endforeach
            </div>

            <div class="drift-bar"><span></span></div>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            <span class="label" data-reveal="rise">Archive</span>
            <hr class="rule" data-reveal="line" style="margin:var(--s-3) 0 var(--s-5);">
            <div class="grid-2" data-reveal="converge">
                @foreach ($archive as $projet)
                    <a class="card" href="{{ route('project') }}">
                        <span class="card-kicker">{{ $projet['annee'] }}</span>
                        <span class="card-title">{{ $projet['titre'] }}</span>
                        <span class="card-body">Placeholder.</span>
                        <span class="card-more">Ouvrir <span aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
