{{-- Le carrefour Lakeust : point d'entrée du site, deux branches en orbite
     (Lakeust Web / Lakeust Studio). Page autonome, pas de layout partagé —
     voir resources/js/pages/welcome-lakeust.js pour l'orbite, la sélection
     de branche et le rideau de sortie. --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store" />
    <title>Lakeust Works</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/web.css', 'resources/js/pages/welcome-lakeust.js', 'resources/js/labs/chevron/chevron-lab.js'
    , 'resources/js/three/blackhole.js', 'resources/js/pages/home.js'])

    @php
        $branches = [
            [
                'label' => 'Lakeust Web',
                'tag'   => 'three.js · GSAP · WebGL',
                'fr'    => "Sites, expériences temps réel et laboratoires d'interaction.",
                'en'    => 'Sites, real-time experiences and interaction labs.',
                'href'  => route('web.about'),
                'open'  => true,
            ],
            [
                'label' => 'Lakeust Studio',
                'tag'   => 'Unity · C# · shaders',
                'fr'    => 'Jeux, moteurs maison et outils internes.',
                'en'    => 'Games, in-house engines and internal tools.',
                'href'  => route('studio.about'),
                'open'  => false,
            ],
        ];
        $img = [
            'QR' => asset('images/team/QR.jpg'),
            'AL' => asset('images/team/AL.jfif'),
            'LC' => asset('images/team/LC.jpg'),
            'CS' => asset('images/team/CS.png'),
        ];
    @endphp
</head>
<body class="pageshow page-welcome nocursor">

<div class="wl-page " data-wl-root data-lang="fr" style="background:var(--bg-1);color:var(--text);font-family:var(--font);min-height:100vh;position:relative;">

    <nav class="wl-nav">
        <a href="#top" style="display:flex;align-items:center;gap:var(--s-3);flex:none;color:inherit;text-decoration:none;">
            <span style="display:block;width:18px;height:1px;background:linear-gradient(90deg,transparent,var(--line-2));"></span>
            <span style="font-size:12px;letter-spacing:.26em;text-transform:uppercase;color:var(--text-2);">Lakeust Works</span>
        </a>
        <div style="flex:1"></div>
        <div class="wl-lang" data-lang-switch></div>
    </nav>

    <section id="top" style="position:relative;height:100vh;min-height:640px;overflow:hidden;background:#07080e">
        <black-hole-stage drive="external" quality="auto" disk-palette="ember" duration="1" style="position:absolute;left:-8%;top:-8%;width:116%;height:152%"></black-hole-stage>
        <div style="position:absolute;inset:0;background:radial-gradient(circle at 50% 46%, transparent 22%, color-mix(in srgb, #07080e 78%, transparent) 78%);pointer-events:none"></div>
        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;text-align:center;padding:19vh var(--space-8) 0;pointer-events:none">
            <div data-hero="1" style="font-size:11px;letter-spacing:.36em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
                <span class="i18n-fr">Studio de développement · Unity &amp; Web</span><span class="i18n-en">Development studio · Unity &amp; Web</span>
            </div>
            <h1 class="textured" data-hero="1" style="margin:0;font-size:clamp(44px,8.4vw,132px);line-height:.94;letter-spacing:-.03em;font-weight:500;text-shadow:0 0 90px rgba(232,222,198,.28)">LAKEUST WORKS</h1>
            <div data-hero="1" style="width:min(560px,80vw);height:1px;margin:var(--space-8) 0;background:linear-gradient(90deg,transparent,var(--accent-700) 48px,var(--accent-700) calc(100% - 48px),transparent)"></div>
            <p data-hero="1" style="margin:0;max-width:640px;font-size:clamp(15px,1.5vw,19px);line-height:1.6;color:var(--text-2);text-wrap:pretty">
                <span class="i18n-fr">Deux pôles, un même atelier. Jeu vidéo Unity — effets, shaders. Web — CSS, JS.</span>
                <span class="i18n-en">Two divisions, one workshop. Unity games — effects, shaders. Web — CSS, JS.</span>
            </p>
        </div>
        <div style="position:absolute;left:50%;bottom:var(--space-8);transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:var(--space-2);font-size:10px;letter-spacing:.3em;text-transform:uppercase;color:var(--text-3);animation:lw-cue 2.4s ease-in-out infinite">
            <span class="i18n-fr">Défiler</span><span class="i18n-en">Scroll</span>
            <span style="width:1px;height:34px;background:linear-gradient(180deg,rgba(226,221,209,.4),transparent)"></span>
        </div>
    </section>

    <div style="border-top:1px solid var(--divider);border-bottom:1px solid var(--divider);background:rgba(35,37,50,.4);overflow:hidden;padding:var(--space-4) 0">
        <div style="display:flex;width:max-content;animation:lw-marquee 34s linear infinite;font-family:var(--font-heading);font-size:12px;letter-spacing:.28em;text-transform:uppercase;color:var(--text-3)">
            @for ($r = 0; $r < 2; $r++)
                <div style="display:flex;gap:var(--space-8);padding-right:var(--space-8)" @if($r === 1) aria-hidden="true" @endif>
                    @foreach (['Unity','Shader Graph','HLSL','C#','VFX','Three.js','GSAP','JavaScript','CSS','Laravel','Vite','Barba', 'Blender'] as $t)
                        <span>{{ $t }}</span><span style="color:var(--accent-600)">·</span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    <section data-cvm-root style="background:#0b0c14;color:#e2ddd1;font-family:Inter,system-ui,sans-serif;min-height:150vh;display:flex;flex-direction:column">

        <div data-stage data-screen-label="Chevron" style="position:relative;flex:1;min-height:640px;overflow:hidden;background-color:#0b0c14;background-image:radial-gradient(46% 38% at 50% 12%, rgba(145,132,217,.16) 0%, transparent 72%)">

            {{-- Bande 0 — Lakeust Web, seule branche déjà construite --}}
            <a data-band data-tone="#5d5294" href="{{ route('web.about') }}" style="position:absolute;inset:0;display:block;color:inherit;transition:opacity .4s ease">
                <div data-inner style="position:absolute;transform-origin:0 0;transition:transform .72s cubic-bezier(.22,1,.36,1);will-change:transform;background:linear-gradient(90deg, #423a6a 0%, #5d5294 72%, #796cbf 100%)">
                    <div class="media-fill" style="position:absolute;inset:0"></div>
                    <div data-tiles style="position:absolute;inset:0;display:flex"></div>
                </div>
                <div data-label style="position:absolute;display:flex;align-items:center;gap:17px;white-space:nowrap;pointer-events:none">
                    <span style="font-weight:300;font-size:clamp(26px,2.7vw,40px);letter-spacing:.02em;color:#f3f5fe">Lakeust Web</span>
                    <span style="font-size:12px;letter-spacing:.26em;text-transform:uppercase;color:#cfd3e5">three.js · GSAP · WebGL</span>
                    <svg width="44" height="12" viewBox="0 0 44 12" fill="none" style="flex:none"><path d="M1 6 H41" stroke="#e7e5fe" stroke-width="1.4" stroke-linecap="round" opacity="0.55"></path><path d="M35.5 1.5 L42.5 6 L35.5 10.5" stroke="#e7e5fe" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </div>
            </a>

            {{-- Bande 1 — Lakeust Studio, pas encore construite --}}
            <a data-band data-tone="#595d6c" href="{{ route('studio.about') }}" style="position:absolute;inset:0;display:block;color:inherit;transition:opacity .4s ease">
                <div data-inner style="position:absolute;transform-origin:0 0;transition:transform .72s cubic-bezier(.22,1,.36,1);will-change:transform;background:linear-gradient(90deg, #75798c 10%, #595d6c 62%, #3f424d 100%)">
                    <div class="media-fill" style="position:absolute;inset:0"></div>
                    <div data-tiles style="position:absolute;inset:0;display:flex"></div>
                </div>
                <div data-label style="position:absolute;display:flex;align-items:center;gap:17px;white-space:nowrap;pointer-events:none">
                    <svg width="44" height="12" viewBox="0 0 44 12" fill="none" style="flex:none"><path d="M8.5 1.5 L1.5 6 L8.5 10.5" stroke="#e7e5fe" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path><path d="M3 6 H43" stroke="#e7e5fe" stroke-width="1.4" stroke-linecap="round" opacity="0.55"></path></svg>
                    <span style="font-weight:300;font-size:clamp(26px,2.7vw,40px);letter-spacing:.02em;color:#f3f5fe">Lakeust Studio</span>
                    <span style="font-size:12px;letter-spacing:.26em;text-transform:uppercase;color:#cfd3e5">Unity · C# · shaders</span>
                </div>
            </a>

            {{-- Bande 2 — troisième versant, à venir --}}
            <a data-band data-tone="#2b2741" href="#" data-soon style="position:absolute;inset:0;display:block;color:inherit;transition:opacity .4s ease">
                <div data-inner style="position:absolute;transform-origin:0 0;transition:transform .72s cubic-bezier(.22,1,.36,1);will-change:transform;background:linear-gradient(90deg, #423a6a 0%, #2b2741 100%)">
                    <div data-tiles style="position:absolute;inset:0;display:flex"></div>
                </div>
                <div data-label style="position:absolute;display:flex;align-items:center;gap:11px;white-space:nowrap;pointer-events:none">
                    <span style="font-weight:300;font-size:clamp(19px,1.8vw,26px);letter-spacing:.02em;color:#e4e7f5">Troisième versant</span>
                    <span class="tag tag-outline" style="flex:none;font-size:10px;letter-spacing:.2em;text-transform:uppercase">à venir</span>
                </div>
            </a>

            {{-- Bande 3 — quatrième versant, à venir --}}
            <a data-band data-tone="#292b31" href="#" data-soon style="position:absolute;inset:0;display:block;color:inherit;transition:opacity .4s ease">
                <div data-inner style="position:absolute;transform-origin:0 0;transition:transform .72s cubic-bezier(.22,1,.36,1);will-change:transform;background:linear-gradient(90deg, #3f424d 0%, #292b31 100%)">
                    <div data-tiles style="position:absolute;inset:0;display:flex"></div>
                </div>
                <div data-label style="position:absolute;display:flex;align-items:center;gap:11px;white-space:nowrap;pointer-events:none">
                    <span class="tag tag-outline" style="flex:none;font-size:10px;letter-spacing:.2em;text-transform:uppercase">à venir</span>
                    <span style="font-weight:300;font-size:clamp(19px,1.8vw,26px);letter-spacing:.02em;color:#cfd3e5">Quatrième versant</span>
                </div>
            </a>
        </div>
    </section>


    <section id="studio" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8) calc(var(--space-8)*2)">
        <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
            01 — <span class="i18n-fr">Qui sommes-nous</span><span class="i18n-en">Who we are</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:calc(var(--space-8)*3);align-items:start">
            <div>
                <h2 data-reveal="rise" style="margin:0 0 var(--space-8);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em">
                    <span class="i18n-fr">Un atelier technique, deux terrains.</span><span class="i18n-en">One technical workshop, two grounds.</span>
                </h2>
            </div>
            <div style="display:flex;flex-direction:column;gap:var(--space-6);max-width:52ch">
                <p data-reveal="rise" style="margin:0;font-size:17px;line-height:1.65;color:var(--text-2);text-wrap:pretty">
                    <span class="i18n-fr">Lakeust Works développe des jeux vidéo sous Unity et des interfaces web. Le travail est le même dans les deux cas : comprendre le besoin, écrire le code, livrer quelque chose qui tient.</span>
                    <span class="i18n-en">Lakeust Works builds Unity games and web interfaces. The work is the same in both cases: understand the need, write the code, ship something that holds.</span>
                </p>
                <p data-reveal="rise" style="margin:0;font-size:15px;line-height:1.7;color:var(--text-3);text-wrap:pretty">
                    <span class="i18n-fr">Pas de sous-traitance en cascade, pas d'intermédiaire. Le code est écrit ici, du shader à l'intégration.</span>
                    <span class="i18n-en">No cascading subcontracting, no middleman. The code is written here, from the shader to the integration.</span>
                </p>
            </div>
        </div>

        <div style="margin-top:calc(var(--space-8)*3);display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1px;background:var(--divider);border-top:1px solid var(--divider);border-bottom:1px solid var(--divider)">
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div data-count="2020" data-plain="1" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">2020</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Depuis</span><span class="i18n-en">Since</span></div>
            </div>
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div data-count="6" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">6</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Ans de pratique</span><span class="i18n-en">Years of practice</span></div>
            </div>
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div data-count="2" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">2</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Pôles — Games &amp; Web</span><span class="i18n-en">Divisions — Games &amp; Web</span></div>
            </div>
        </div>
    </section>

    <section id="fondateur" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
        <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:calc(var(--space-8)*2)">
            02 — <span class="i18n-fr">Le fondateur</span><span class="i18n-en">The founder</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:calc(var(--space-8)*3);align-items:center">
            <div data-reveal="rise" style="position:relative;aspect-ratio:4/5;border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-md)">
                <img src="{{ $img['QR'] }}" alt="Quentin Renaud" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
            </div>
            <div>
                <h2 data-reveal="rise" style="margin:0;font-size:clamp(34px,4.6vw,68px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">Quentin Renaud</h2>
                <div data-reveal="rise" style="margin-top:var(--space-6);font-size:13px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300)">
                    <span class="i18n-fr">Fondateur de Lakeust Works</span><span class="i18n-en">Founder of Lakeust Works</span>
                </div>
                <div data-reveal="rise" style="margin:calc(var(--space-8)*1.5) 0;height:1px;background:linear-gradient(90deg,transparent,var(--divider) 48px,var(--divider) calc(100% - 48px),transparent)"></div>
                <div style="display:flex;flex-direction:column;gap:var(--space-6);max-width:54ch">
                    <p data-reveal="rise" style="margin:0;font-size:19px;line-height:1.55;color:var(--text);text-wrap:pretty">
                        <span class="i18n-fr">Assistant technique d'ingénieurs. <br> Développeur depuis 2020.</span>
                        <span class="i18n-en">Engineers' technical assistant. <br> Developer since 2020.</span>
                    </p>
                    <p data-reveal="rise" style="margin:0;font-size:15px;line-height:1.7;color:var(--text-3);text-wrap:pretty">
                        <span class="i18n-fr">Il dirige les deux pôles du studio et reste sur le code : gameplay et shaders sous Unity, intégration et animation sur le web.</span>
                        <span class="i18n-en">He runs both divisions of the studio and stays on the code: gameplay and shaders in Unity, integration and animation on the web.</span>
                    </p>
                </div>
                <div data-reveal="rise" style="display:flex;flex-wrap:wrap;gap:calc(var(--space-6)*1.4);margin-top:calc(var(--space-8)*1.4)">
                    <a class="link-inline" href="https://www.youtube.com/@quentinjecte" target="_blank" rel="noopener">YouTube</a>
                    <a class="link-inline" href="https://www.twitch.tv/quentinjecte" target="_blank" rel="noopener">Twitch</a>
                    <a class="link-inline" href="https://www.linkedin.com/in/quentin-renaud-562773252/" target="_blank" rel="noopener">LinkedIn</a>
                </div>
            </div>
        </div>

        <div style="margin-top:calc(var(--space-8)*4)">
            <div data-reveal="rise" style="display:flex;align-items:center;gap:var(--space-5);margin-bottom:var(--space-8)">
                <span style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Collaborateurs</span><span class="i18n-en">Collaborators</span></span>
                <span class="tag"><span class="i18n-fr">Entreprises indépendantes</span><span class="i18n-en">Independent businesses</span></span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:var(--space-8)">
                <div data-reveal="rise" class="card" style="padding:var(--space-8);background:var(--surface)">
                    <div style="aspect-ratio:1;border-radius:var(--radius-sm);overflow:hidden;position:relative;margin-bottom:var(--space-6)">
                        <img src="{{ $img['AL'] }}" alt="Arno Labourdette" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                    </div>
                    <div class="card-title">Arno Labourdette</div>
                    <div class="card-meta" style="color:var(--text-3)"><span class="i18n-fr">Collaborateur — Dev Unity, Web, Blender</span><span class="i18n-en">Collaborator — Unity, Web &amp; Blender dev</span></div>
                    <div style="display:flex;flex-wrap:wrap;gap:calc(var(--space-6)*1.4);margin-top:var(--space-6)">
                        <a class="link-inline" href="https://arnolabourdette-rexignis.fr/" target="_blank" rel="noopener"><span class="i18n-fr">Site</span><span class="i18n-en">Website</span></a>
                        <a class="link-inline" href="https://www.youtube.com/@Rexignis" target="_blank" rel="noopener">YouTube</a>
                        <a class="link-inline" href="https://www.linkedin.com/in/arno-labourdette-3a3116252/" target="_blank" rel="noopener">LinkedIn</a>
                    </div>
                </div>
                <div data-reveal="rise" class="card" style="padding:var(--space-8);background:var(--surface)">
                    <div style="aspect-ratio:1;border-radius:var(--radius-sm);overflow:hidden;position:relative;margin-bottom:var(--space-6)">
                        <img src="{{ $img['LC'] }}" alt="Leonardo Catalano" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                    </div>
                    <div class="card-title">Leonardo Catalano</div>
                    <div class="card-meta" style="color:var(--text-3)"><span class="i18n-fr">Collaborateur — Monteur vidéo</span><span class="i18n-en">Collaborator — Video editor</span></div>
                    <div style="display:flex;flex-wrap:wrap;gap:calc(var(--space-6)*1.4);margin-top:var(--space-6)">
                        <a class="link-inline" href="https://www.youtube.com/channel/UCKWEB-1-SBq49SzpYVjVJHA" target="_blank" rel="noopener">YouTube</a>
                        <a class="link-inline" href="https://lepfiremontage.fr/" target="_blank" rel="noopener"><span class="i18n-fr">Portfolio</span><span class="i18n-en">Portfolio</span></a>
                        <a class="link-inline" href="https://www.linkedin.com/in/leonardo-catalano-b25949298/" target="_blank" rel="noopener">LinkedIn</a>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:calc(var(--space-8)*4)">
            <div data-reveal="rise" style="display:flex;align-items:center;gap:var(--space-5);margin-bottom:var(--space-8)">
                <span style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Co-fondateur</span><span class="i18n-en">Co-founder</span></span>
                <span class="tag-accent"><span class="i18n-fr">Studio partenaire</span><span class="i18n-en">Partner studio</span></span>
            </div>
            <div data-reveal="rise" class="card" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:var(--space-8)">
                <div style="aspect-ratio:1;border-radius:var(--radius-sm);overflow:hidden;position:relative;margin-bottom:var(--space-6)">
                    <img src="{{ $img['CS'] }}" alt="Confused Slime" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">
                </div>
                <div  style="display:flex;flex-direction:column;gap:var(--space-6); justify-content:center;">
                    <div class="card-title" style="font-size:22px">Confused Slime</div>
                    <div class="card-meta" style="color:var(--text-3);margin-top:var(--space-3)"><span class="i18n-fr">Studio de jeu vidéo — co-fondé avec Lakeust Works</span><span class="i18n-en">Game studio — co-founded with Lakeust Works</span></div>
                    <p style="margin:var(--space-5) 0 0;font-size:14px;line-height:1.65;color:var(--text-3);max-width:52ch">
                        <span class="i18n-fr">Aide les développeurs indépendants (jeu, web) et les monteurs vidéo à concrétiser leurs projets.</span>
                        <span class="i18n-en">Helps indie developers (games, web) and video editors bring their projects to life.</span>
                    </p>
                    <div style="display:flex;flex-wrap:wrap;gap:calc(var(--space-6)*1.4);margin-top:var(--space-6)">
                        <a class="link-inline" href="https://www.linkedin.com/company/confused-slime/posts/?feedView=all" target="_blank" rel="noopener">LinkedIn</a>
                        <a class="link-inline" href="https://www.youtube.com/@ConfusedSlimeStudio" target="_blank" rel="noopener">YouTube</a>
                        <a class="link-inline" href="https://confusedslime.arnolabourdette-rexignis.fr/" target="_blank" rel="noopener"><span class="i18n-fr">Site</span><span class="i18n-en">Website</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--<section style="border-top:1px solid var(--line);">
        <div class="wrap section">
            <div class="label label-accent" style="margin-bottom:var(--s-8);" data-reveal="rise">
                01 — <span class="i18n-fr">Le pont</span><span class="i18n-en">The bridge</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:calc(var(--s-8) * 2);align-items:start;">
                <h2 class="t-h1" data-reveal="rise">
                    <span class="i18n-fr">Deux versants, une même équipe et un seul socle technique.</span>
                    <span class="i18n-en">Two sides, one team, one technical base.</span>
                </h2>
                <div style="display:flex;flex-direction:column;gap:var(--s-6);">
                    <p class="t-body" data-reveal="rise" data-reveal-delay="80">
                        <span class="i18n-fr">Le web et le jeu se nourrissent l'un de l'autre&nbsp;: les shaders écrits pour un moteur finissent dans un lab WebGL, et les outils faits pour le navigateur servent à prototyper une mécanique.</span>
                        <span class="i18n-en">Web and games feed each other: shaders written for an engine end up in a WebGL lab, and browser tooling becomes the fastest way to prototype a mechanic.</span>
                    </p>
                    <p class="t-body" data-reveal="rise" data-reveal-delay="140">
                        <span class="i18n-fr">Ce carrefour n'est qu'un aiguillage. D'autres branches viendront s'y accrocher.</span>
                        <span class="i18n-en">This crossroads is only a switch. More branches will hook onto it.</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" style="border-top:1px solid var(--line);">
        <div class="wrap section">
            <div class="label label-accent" style="margin-bottom:var(--s-8);" data-reveal="rise">
                02 — <span class="i18n-fr">Contact</span><span class="i18n-en">Contact</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:calc(var(--s-8) * 2);align-items:end;">
                <div style="display:flex;flex-direction:column;gap:var(--s-6);" data-reveal="rise">
                    <p class="t-body" style="max-width:44ch;">
                        <span class="i18n-fr">Un jeu, un site, un effet précis&nbsp;: décris le besoin, la réponse arrive avec un périmètre et un prix.</span>
                        <span class="i18n-en">A game, a site, one specific effect: describe the need and the answer comes with a scope and a price.</span>
                    </p>
                    <div class="t-h3" style="color:var(--accent);">contact@lakeust.works</div>
                </div>
                <div style="display:flex;gap:var(--s-4);justify-content:flex-start;" data-reveal="rise" data-reveal-delay="80">
                    <a class="btn btn-primary" href="mailto:contact@lakeust.works">
                        <span class="i18n-fr">Écrire</span><span class="i18n-en">Write</span>
                    </a>
                </div>
            </div>
        </div>
    </section>-->

    <footer style="border-top:1px solid var(--line);">
        <div class="wrap" style="padding:var(--s-6) var(--gutter);display:flex;flex-wrap:wrap;gap:var(--s-6);justify-content:space-between;font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:var(--text-3);">
            <span>Lakeust Works — {{ date('Y') }}</span>
            <span><span class="i18n-fr">Aiguillage</span><span class="i18n-en">Switchboard</span> · {{ str_pad(count($branches), 2, '0', STR_PAD_LEFT) }}</span>
        </div>
    </footer>
</div>
</body>
</html>
