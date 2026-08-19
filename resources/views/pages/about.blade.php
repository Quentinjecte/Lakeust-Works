@extends('layouts.site')

@section('title', 'À propos — Lakeust Works')

@section('content')
    <section class="section" style="padding-top:clamp(120px,18vh,200px);">
        <div class="wrap split">

            <aside class="split-aside">
                <div data-reveal="mask">
                    <span class="mask-line"><span class="label" style="display:block;margin-bottom:var(--s-4);">Dossier — 001</span></span>
                    <span class="mask-line"><h1 class="t-h1">Concevoir</h1></span>
                    <span class="mask-line"><h1 class="t-h1" style="color:var(--text-3);">des systèmes</h1></span>
                </div>
                <span class="mark" style="margin:var(--s-5) 0;" data-reveal="wipe" data-reveal-delay="240"></span>
                <p class="t-body" data-reveal="blur" data-reveal-delay="180" style="font-size:14px;">
                    Texte de remplacement. Cette colonne reste fixe pendant que le flux de droite défile ;
                    le sommaire indique la section lue.
                </p>
                <nav style="display:flex;flex-direction:column;gap:var(--s-2);margin-top:var(--s-6);" data-reveal="stagger" data-reveal-delay="260">
                    <a class="label" data-section-link href="#s-01" style="text-decoration:none;">01 — Approche</a>
                    <a class="label" data-section-link href="#s-02" style="text-decoration:none;">02 — Méthode</a>
                    <a class="label" data-section-link href="#s-03" style="text-decoration:none;">03 — Chiffres</a>
                    <a class="label" data-section-link href="#contact" style="text-decoration:none;">04 — Contact</a>
                </nav>
            </aside>

            <div class="split-flow">

                <article id="s-01">
                    <span class="label" data-reveal="rise">01 — Approche</span>
                    <div class="media media-16-9 media-hover parallax" data-parallax="30" style="margin:var(--s-4) 0 var(--s-5);" data-reveal="wipe">
                        <div class="media-fill"></div>
                        <span class="media-cap label" style="color:var(--text-2);">Placeholder — image 16:9</span>
                    </div>
                    <h2 class="t-h2" data-reveal="rise" style="margin-bottom:var(--s-3);">Un vocabulaire, pas un décor</h2>
                    <p class="t-body" data-reveal="blur">
                        Contenu de remplacement. Les paragraphes apparaissent en fondu-flou lorsqu'ils entrent
                        dans le champ, une seule fois, sans rejouer au retour en arrière.
                    </p>
                    <p class="t-body" data-reveal="blur" data-reveal-delay="80">
                        Deuxième paragraphe de remplacement, destiné à vérifier le rythme vertical et la
                        largeur de lecture maximale.
                    </p>
                </article>

                <article id="s-02">
                    <span class="label" data-reveal="rise">02 — Méthode</span>
                    <h2 class="t-h2" data-reveal="rise" data-reveal-delay="60" style="margin:var(--s-3) 0 var(--s-5);">Trois temps</h2>
                    <div class="grid-2" data-reveal="stagger">
                        @foreach (['Cadrage', 'Prototype', 'Livraison'] as $i => $etape)
                            <a class="card" href="{{ route('project') }}">
                                <span class="card-kicker">Étape 0{{ $i + 1 }}</span>
                                <span class="card-title">{{ $etape }}</span>
                                <span class="card-body">Texte de remplacement décrivant l'étape.</span>
                                <span class="card-more">Voir <span aria-hidden="true">→</span></span>
                            </a>
                        @endforeach
                    </div>
                </article>

                <article id="s-03">
                    <span class="label" data-reveal="rise">03 — Chiffres</span>
                    <div class="grid-3" style="margin-top:var(--s-4);">
                        @foreach ([['12', '', 'Projets livrés'], ['6', ' ans', 'Pratique'], ['4', '', 'Moteurs']] as $i => $stat)
                            <div class="stat" data-reveal="rise" data-reveal-delay="{{ $i * 90 }}"
                                 data-count="{{ $stat[0] }}" @if ($stat[1]) data-count-suffix="{{ $stat[1] }}" @endif>
                                <span class="stat-value num" data-count-value>0</span>
                                <span class="stat-label">{{ $stat[2] }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article id="contact">
                    <hr class="rule" data-reveal="line" style="margin-bottom:var(--s-6);">
                    <h2 class="t-h2" data-reveal="rise">Parlons du prochain</h2>
                    <p class="t-lead" data-reveal="blur" style="margin:var(--s-3) 0 var(--s-5);">
                        Une idée de projet, une collaboration, une question ? Écrivez-moi.
                    </p>

                    <div class="grid-3" data-reveal="stagger" style="margin-bottom:var(--s-6);">
                        <div>
                            <span class="label" style="display:block;margin-bottom:4px;">Email</span>
                            <a class="link-inline" href="mailto:quentrenaud13@gmail.com">quentrenaud13@gmail.com</a>
                        </div>
                        <div>
                            <span class="label" style="display:block;margin-bottom:4px;">Email pro</span>
                            <a class="link-inline" href="mailto:lakeustworks@gmail.com">lakeustworks@gmail.com</a>
                        </div>
                        <div>
                            <span class="label" style="display:block;margin-bottom:4px;">Téléphone</span>
                            <a class="link-inline" href="tel:+33652603188">06 52 60 31 88</a>
                        </div>
                    </div>

                    <div style="margin-bottom:var(--s-6);">
                        <span class="label" style="display:block;margin-bottom:var(--s-3);">Suivre</span>
                        <div style="display:flex;flex-wrap:wrap;gap:var(--s-4);" data-reveal="stagger">
                            <a class="link-inline" href="https://www.twitch.tv/quentinjecte" target="_blank" rel="noopener">Twitch</a>
                            <a class="link-inline" href="https://www.youtube.com/@quentinjecte" target="_blank" rel="noopener">YouTube</a>
                        </div>
                    </div>

                    <div style="margin-bottom:var(--s-6);">
                        <span class="label" style="display:block;margin-bottom:var(--s-3);">Partenaires</span>
                        <div style="display:flex;flex-wrap:wrap;gap:var(--s-4);" data-reveal="stagger">
                            <a class="link-inline" href="https://www.youtube.com/@ConfusedSlimeStudio" target="_blank" rel="noopener">Confused Slime Studio</a>
                            <a class="link-inline" href="https://www.youtube.com/@Rexignis" target="_blank" rel="noopener">Rexignis</a>
                        </div>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:var(--s-3);" data-reveal="stagger">
                        <a class="btn" href="{{ route('works') }}">Voir les travaux <span class="arrow" aria-hidden="true">→</span></a>
                        <a class="btn btn-ghost" href="{{ route('project') }}">Étude de cas</a>
                    </div>
                </article>

            </div>
        </div>
    </section>
@endsection
