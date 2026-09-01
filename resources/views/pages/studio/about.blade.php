@extends('layouts.site')

@section('title', 'À propos — Lakeust Works')

@section('content')
<head>
    @vite(['resources/css/app.css', 'resources/css/web.css'])
    @section('cat', 'Studio')

    @php
        /* 'external' : page hors gabarit Barba (pas de data-barba="container" —
           welcome.blade.php est un document autonome, et les pages labs/* logent
           leur @vite dans @section('content'), donc jamais exécuté si Barba se
           contente d'injecter le HTML fetché). data-barba-prevent force Barba à
           laisser passer une vraie navigation pour ces liens-là plutôt que de
           tenter un swap SPA voué à rester bloqué (voir barba-transitions.js). */

        $nav = [
            ['route' => 'studio.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'studio.works',   'i18n' => 'nav.games',  'keep' => true],
            ['route' => 'studio.lab',   'i18n' => 'nav.lab',  'keep' => true],
        ];
    @endphp
 </head>

   <section id="top" style="position:relative;min-height:640px;overflow:hidden;background:#07080e">
        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;text-align:center;padding:19vh var(--space-8) 0;pointer-events:none">
            <h1 class="textured gradient2" data-hero="1" style="margin:0;font-size:clamp(44px,8.4vw,132px);line-height:.94;letter-spacing:-.03em;font-weight:500;text-shadow:0 0 90px rgba(232,222,198,.28)">LAKEUST STUDIO</h1>
            <div data-hero="1" style="width:min(560px,80vw);height:1px;margin:var(--space-8) 0;background:linear-gradient(90deg,transparent,var(--accent-700) 48px,var(--accent-700) calc(100% - 48px),transparent)"></div>
        </div>
    </section>

    <div style="border-top:1px solid var(--divider);border-bottom:1px solid var(--divider);background:rgba(35,37,50,.4);overflow:hidden;padding:var(--space-4) 0">
        <div style="display:flex;width:max-content;animation:lw-marquee 34s linear infinite;font-family:var(--font-heading);font-size:12px;letter-spacing:.28em;text-transform:uppercase;color:var(--text-3)">
            @for ($r = 0; $r < 2; $r++)
                <div style="display:flex;gap:var(--space-8);padding-right:var(--space-8)" @if($r === 1) aria-hidden="true" @endif>
                    @foreach (['Unity','Shader Graph','HLSL','C#','VFX','ECS','DOTS','UXML','USS','Optimisation','Environement'] as $t)
                        <span>{{ $t }}</span><span style="color:var(--accent-600)">·</span>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    <section id="studio" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8) calc(var(--space-8)*2)">
        <div style="margin-top:calc(var(--space-8)*3);display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1px;background:var(--divider);border-top:1px solid var(--divider);border-bottom:1px solid var(--divider)">
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div data-count="2021" data-plain="1" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">2020</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Depuis</span><span class="i18n-en">Since</span></div>
            </div>
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div data-count="5" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">5</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Ans de pratique</span><span class="i18n-en">Years of practice</span></div>
            </div>
            <div style="background:var(--bg);padding:calc(var(--space-8)*1.5) var(--space-8)">
                <div data-count="4" style="font-family:var(--font-heading);font-size:clamp(36px,4.4vw,64px);line-height:1;letter-spacing:-.03em;color:var(--text-hi)">4</div>
                <div style="margin-top:var(--space-4);font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)"><span class="i18n-fr">Jeu Créer</span><span class="i18n-en">Games Created</span></div>
            </div>
        </div>
    </section>

    <section id="services" style="border-top:1px solid var(--divider);background:linear-gradient(180deg,rgba(35,37,50,.34),transparent 60%)">
        <div style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
                03 — <span class="i18n-fr">Nos services</span><span class="i18n-en">What we do</span>
            </div>
            <h2 data-reveal="rise" style="margin:0 0 calc(var(--space-8)*2.5);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:22ch">
                <span class="i18n-fr">Deux pôles, la même exigence.</span><span class="i18n-en">Two divisions, one standard.</span>
            </h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:var(--space-8)">
                <div data-reveal="rise" data-magnet="1" style="padding:calc(var(--space-8)*1.6);border-radius:var(--radius-lg);background:rgba(35,37,50,.7);box-shadow:var(--shadow-sm);transition:box-shadow .35s ease">
                    <div style="display:flex;align-items:baseline;gap:var(--space-4);margin-bottom:var(--space-8)">
                        <span style="font-family:var(--font-heading);font-size:clamp(26px,3vw,38px);letter-spacing:-.02em;color:var(--text-hi)">Games</span>
                        <span style="width:28px;height:1px;background:var(--accent);display:inline-block"></span>
                        <span style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--text-3)">Unity</span>
                    </div>
                    <p style="margin:0 0 calc(var(--space-8)*1.5);font-size:16px;line-height:1.65;color:var(--text-2);text-wrap:pretty">
                        <span class="i18n-fr">Jeu vidéo Unity : gameplay, effets et shaders. De la mécanique jouable au rendu final.</span>
                        <span class="i18n-en">Unity games: gameplay, effects and shaders. From playable mechanics to final render.</span>
                    </p>
                    <ul class="multi">
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Gameplay Unity, C#</span><span class="i18n-en">Unity gameplay, C#</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Shaders — Shader Graph, HLSL</span><span class="i18n-en">Shaders — Shader Graph, HLSL</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Effets et VFX</span><span class="i18n-en">Effects and VFX</span></li>
                        <li style="display:flex;gap:var(--space-4);font-size:14px;color:var(--text-2)"><span style="color:var(--accent-300)">—</span><span class="i18n-fr">Optimisation et build</span><span class="i18n-en">Optimisation and build</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="demos" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
        <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-6)">
            04 — <span class="i18n-fr">Démonstrations</span><span class="i18n-en">Demos</span>
        </div>
        <h2 data-reveal="rise" style="margin:0 0 calc(var(--space-8)*2.5);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:26ch">
            <span class="i18n-fr">Quatre systèmes, du premier pas à la maîtrise.</span><span class="i18n-en">Four systems, from first step to mastery.</span>
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(420px,1fr));gap:calc(var(--space-8)*1.5)">
            @foreach ([
                ['day-night-cycle', 'Cycle jour / nuit', 'Day / night cycle', 'Débutant', 'Beginner',
                    'Shader Graph · Matériau galaxie',
                    "Cycle continu qui interpole position du soleil, couleur du ciel et intensité lumineuse — matériau galaxie et shader d'aurore boréale pour la nuit, étoiles animées.",
                    "A continuous cycle interpolating sun position, sky colour and light intensity — a galaxy material and an aurora borealis shader by night, with animated stars."],
                ['weather-cycle', 'Météo dynamique', 'Dynamic weather', 'Intermédiaire', 'Intermediate',
                    'Volume Profile · Post-processing',
                    "Bascule dynamiquement entre plusieurs profils météo (paramètre cloud) en interpolant exposition, éclairage et brouillard dans le temps, sans perte de performance.",
                    "Dynamically blends between several weather volume profiles (cloud parameter), interpolating exposure, lighting and fog over time without a performance cost."],
                ['crafting', 'Crafting', 'Crafting', 'Avancé', 'Advanced',
                    'UI Toolkit · Inventaire runtime',
                    "Dépose de ressources sur l'établi en temps réel, vérification de recette et assemblage — création d'objets en tout genre à partir de l'inventaire du joueur.",
                    "Resources dropped onto the workbench in real time, recipe matching and assembly — crafting all kinds of objects straight from the player's inventory."],
                ['weapon-attachments', "Modification d'arme", 'Weapon modification', 'Expert', 'Expert',
                    'Sockets 3D · Manipulation runtime de mesh',
                    "Attache dynamique de composants (poignée, viseur...) directement sur le mesh de l'arme, vue et manipulée en 3D en temps réel pendant la modification.",
                    "Attachments (grip, sight...) dynamically bound to the weapon's mesh, viewed and manipulated live in 3D during modification."],
            ] as $i => $v)
                <div data-reveal="rise" style="border-radius:var(--radius-lg);overflow:hidden;background:rgba(35,37,50,.6);box-shadow:var(--shadow-sm)">
                    <div style="position:relative;aspect-ratio:16/9;background:#000">
                        <video src="{{ asset('videos/studio/' . $v[0] . '.mp4') }}" controls preload="metadata" playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
                    </div>
                    <div style="padding:var(--space-6) var(--space-8) calc(var(--space-8)*1.2)">
                        <div style="display:flex;align-items:baseline;justify-content:space-between;gap:var(--space-4);margin-bottom:var(--space-3)">
                            <span style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300)">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="tag tag-accent"><span class="i18n-fr">{{ $v[3] }}</span><span class="i18n-en">{{ $v[4] }}</span></span>
                        </div>
                        <div style="font-family:var(--font-heading);font-size:20px;letter-spacing:-.01em;margin-bottom:var(--space-3)"><span class="i18n-fr">{{ $v[1] }}</span><span class="i18n-en">{{ $v[2] }}</span></div>
                        <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-4);margin-bottom:var(--space-4)">{{ $v[5] }}</div>
                        <p style="margin:0;font-size:14px;line-height:1.65;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $v[6] }}</span><span class="i18n-en">{{ $v[7] }}</span></p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- dérive horizontale : le scroll vertical translate la piste (setupDrift, core/page-systems.js) --}}
    <section id="methode" class="drift" data-drift>
        <div class="drift-sticky" style="min-height:600px;background:var(--bg);border-top:1px solid var(--divider)">
            <div class="drift-head" style="max-width:1240px;margin:0 auto;width:100%;padding:0 var(--space-8) calc(var(--space-8)*2)">
                <div style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300)">
                    05 — <span class="i18n-fr">Notre méthode</span><span class="i18n-en">Our process</span>
                </div>
            </div>
            <div class="drift-track" style="padding:0 var(--space-8)">
                @foreach ([
                    ['01', 'Cadrage', 'Scoping', "On définit le besoin, les contraintes techniques et ce qui reste hors périmètre. Écrit, chiffré.", 'We set the need, the technical constraints and what stays out of scope. Written down, priced.'],
                    ['02', 'Prototype', 'Prototype', "Une version jouable ou cliquable avant la production. On corrige la direction tant que c'est peu coûteux.", 'A playable or clickable version before production. Direction gets corrected while it is still cheap.'],
                    ['03', 'Production', 'Build', 'Le code est écrit ici, versionné, relu. Points d\'avancement réguliers, rien de caché.', 'The code is written here, versioned, reviewed. Regular check-ins, nothing hidden.'],
                    ['04', 'Livraison', 'Delivery', 'Mise en ligne ou build final, documentation, et le suivi qui va avec.', 'Deployment or final build, documentation, and the follow-up that goes with it.'],
                ] as $step)
                    <div class="drift-item" style="width:min(78vw,520px);padding:calc(var(--space-8)*1.6);border-radius:var(--radius-lg);background:rgba(35,37,50,.62);box-shadow:var(--shadow-sm);display:flex;flex-direction:column;justify-content:space-between;min-height:330px">
                        <div style="font-family:var(--font-heading);font-size:56px;line-height:1;color:var(--accent-700)">{{ $step[0] }}</div>
                        <div>
                            <h3 style="margin:0 0 var(--space-6);font-size:26px;letter-spacing:-.02em"><span class="i18n-fr">{{ $step[1] }}</span><span class="i18n-en">{{ $step[2] }}</span></h3>
                            <p style="margin:0;font-size:15px;line-height:1.7;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $step[3] }}</span><span class="i18n-en">{{ $step[4] }}</span></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="travaux" style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
        <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
            06 — <span class="i18n-fr">Réalisations</span><span class="i18n-en">Selected work</span>
        </div>
        <h2 data-reveal="rise" style="margin:0 0 calc(var(--space-8)*2.5);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:24ch">
            <span class="i18n-fr">Ce qui tourne, pas ce qui est promis.</span><span class="i18n-en">What runs, not what is promised.</span>
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:var(--space-8)">
            @foreach ([
                ['Web · WebGL', 'Portail orbital', 'Orbital portal', 'Navigation en temps réel : Three.js, shaders de lentille gravitationnelle, GSAP.', 'Real-time navigation: Three.js, gravitational-lensing shaders, GSAP.', 'Capture — portail orbital WebGL'],
                ['Web · Laravel', 'Transitions de page', 'Page transitions', 'Navigation sans rechargement sur une base Laravel et Vite, transitions Barba.', 'No-reload navigation on a Laravel and Vite base, Barba transitions.', 'Capture — transitions de page'],
                ['Games · Unity', 'Projet Unity', 'Unity project', 'Effets et shaders sous Unity — titre et détails à documenter.', 'Effects and shaders in Unity — title and details to be documented.', 'Capture — projet Unity (shader / VFX)'],
            ] as $w)
                <a data-reveal="rise" data-magnet="1" href="#contact" data-jump="contact" style="display:block;color:inherit;border-radius:var(--radius-lg);overflow:hidden;background:rgba(35,37,50,.6);box-shadow:var(--shadow-sm);transition:box-shadow .35s ease,transform .35s ease">
                    <div style="position:relative;aspect-ratio:4/3">
                        <div class="lw-ph"><span>{{ $w[5] }}</span></div>
                    </div>
                    <div style="padding:var(--space-8)">
                        <div style="font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-3)">{{ $w[0] }}</div>
                        <div style="font-family:var(--font-heading);font-size:20px;letter-spacing:-.01em;margin-bottom:var(--space-3)"><span class="i18n-fr">{{ $w[1] }}</span><span class="i18n-en">{{ $w[2] }}</span></div>
                        <p style="margin:0;font-size:14px;line-height:1.6;color:var(--text-3);text-wrap:pretty"><span class="i18n-fr">{{ $w[3] }}</span><span class="i18n-en">{{ $w[4] }}</span></p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section id="stack" style="border-top:1px solid var(--divider);background:linear-gradient(180deg,rgba(35,37,50,.3),transparent 70%)">
        <div style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:calc(var(--space-8)*2)">
                07 — <span class="i18n-fr">Technologies</span><span class="i18n-en">Technologies</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:calc(var(--space-8)*3)">
                <div>
                    <div data-reveal="rise" style="font-family:var(--font-heading);font-size:15px;letter-spacing:.16em;text-transform:uppercase;color:var(--text-2);padding-bottom:var(--space-6);margin-bottom:var(--space-8);border-bottom:1px solid var(--divider)">Games</div>
                    <div data-reveal="rise" style="display:flex;flex-wrap:wrap;gap:var(--space-3)">
                        @foreach (['Unity','C#','Shader Graph','HLSL','VFX Graph','URP','Blender'] as $t)
                            <span class="tag tag-outline">{{ $t }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <div data-reveal="rise" style="font-family:var(--font-heading);font-size:15px;letter-spacing:.16em;text-transform:uppercase;color:var(--text-2);padding-bottom:var(--space-6);margin-bottom:var(--space-8);border-bottom:1px solid var(--divider)">Web</div>
                    <div data-reveal="rise" style="display:flex;flex-wrap:wrap;gap:var(--space-3)">
                        @foreach (['JavaScript','CSS','Three.js','GLSL','GSAP','Barba','Laravel','Blade','Vite'] as $t)
                            <span class="tag tag-outline">{{ $t }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" style="border-top:1px solid var(--divider)">
        <div style="max-width:1240px;margin:0 auto;padding:calc(var(--space-8)*4) var(--space-8)">
            <div data-reveal="rise" style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--accent-300);margin-bottom:var(--space-8)">
                08 — <span class="i18n-fr">Contact &amp; devis</span><span class="i18n-en">Contact &amp; quotes</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:calc(var(--space-8)*3);align-items:start">
                <div>
                    <h2 data-reveal="rise" style="margin:0 0 var(--space-8);font-size:clamp(28px,3.6vw,54px);line-height:1.06;letter-spacing:-.025em;max-width:18ch">
                        <span class="i18n-fr">Parlez-nous du projet.</span><span class="i18n-en">Tell us about the project.</span>
                    </h2>
                    <p data-reveal="rise" style="margin:0 0 calc(var(--space-8)*1.5);font-size:16px;line-height:1.65;color:var(--text-3);max-width:44ch;text-wrap:pretty">
                        <span class="i18n-fr">Jeu, site, effet précis à produire : décrivez le besoin, on répond avec un périmètre et un prix.</span>
                        <span class="i18n-en">A game, a site, one specific effect: describe the need and we answer with a scope and a price.</span>
                    </p>
                    <div data-reveal="rise" style="font-size:20px;font-family:var(--font-heading);color:var(--accent-300)">contact@lakeust.works</div>
                    <div style="margin-top:var(--space-3);font-size:12px;color:var(--text-4)"><span class="i18n-fr">Adresse à confirmer</span><span class="i18n-en">Address to confirm</span></div>
                </div>
                <form data-reveal="rise" data-lw-form style="display:flex;flex-direction:column;gap:var(--space-6);padding:calc(var(--space-8)*1.5);border-radius:var(--radius-lg);background:rgba(35,37,50,.6);box-shadow:var(--shadow-sm)">
                    <div class="field">
                        <label for="lw-name"><span class="i18n-fr">Nom</span><span class="i18n-en">Name</span></label>
                        <input class="input" id="lw-name" type="text" required>
                    </div>
                    <div class="field">
                        <label for="lw-mail">Email</label>
                        <input class="input" id="lw-mail" type="email" required>
                    </div>
                    <div class="field">
                        <label for="lw-msg"><span class="i18n-fr">Le projet</span><span class="i18n-en">The project</span></label>
                        <textarea class="input" id="lw-msg" rows="5" style="resize:vertical"></textarea>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit" data-magnet="1"><span class="i18n-fr">Envoyer</span><span class="i18n-en">Send</span></button>
                    <div data-lw-form-note style="font-size:12px;color:var(--text-4);text-align:center">Démonstration — le formulaire n'envoie rien pour l'instant.</div>
                </form>
            </div>
        </div>
    </section>
@endsection
