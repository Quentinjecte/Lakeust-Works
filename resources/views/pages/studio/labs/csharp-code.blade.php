<!-- C# Code Lab — trois systèmes plus proches de l'algorithme que du visuel
     (détection/ciblage, placement en grille, ECS/DOTS) : même showcase que
     Visual Effect Lab, mais chaque fiche ajoute un extrait de code
     représentatif du principe filmé, pas un copier-coller du script réel. -->

@extends('layouts.site')
@section('title', 'C# Code Lab — Lakeust Works')
@section('cat', 'Studio')

@section('content')
<head>
    @vite(['resources/css/lab.css'])

    @php
        $nav = [
            ['route' => 'studio.about',   'i18n' => 'nav.about', 'keep' => true],
            ['route' => 'studio.works',   'i18n' => 'nav.games',  'keep' => true],
            ['route' => 'studio.lab',     'i18n' => 'nav.lab',  'keep' => false, 'external' => true],
        ];

        $systems = [
            ['file' => 'enemy-detection', 'name' => 'Détection & ciblage', 'niveau_fr' => 'Intermédiaire', 'niveau_en' => 'Intermediate',
                'stack' => 'C# · Physics.OverlapSphere · State Machine',
                'fr' => "Mécanique RTS : le déplacement d'une unité détecte l'ennemi le plus proche, le prend pour cible, puis ouvre le feu dès qu'il est à portée.",
                'en' => "RTS mechanic: a moving unit detects the nearest enemy, targets it, then opens fire once it's within range.",
                'code' => "Collider[] hits = Physics.OverlapSphere(pos, detectRange, enemyMask);\nTransform nearest = hits.OrderBy(h => Vector3.Distance(pos, h.transform.position))\n                         .FirstOrDefault()?.transform;\nif (nearest && Vector3.Distance(pos, nearest.position) <= fireRange)\n    Fire(nearest);"],
            ['file' => 'grid-placement', 'name' => 'Placement sur grille', 'niveau_fr' => 'Intermédiaire', 'niveau_en' => 'Intermediate',
                'stack' => 'C# · Grille spatiale · Snapping',
                'fr' => "Placement des structures sur la carte façon RTS : la position souris est convertie en cellule, puis validée avant de poser le bâtiment.",
                'en' => "RTS-style structure placement on the map: the mouse position is converted to a grid cell, then validated before the building is placed.",
                'code' => "Vector3Int cell = grid.WorldToCell(mouseWorldPos);\nVector3 snapped = grid.CellToWorld(cell) + grid.cellSize * 0.5f;\nbool valid = !occupied.Contains(cell) && IsInsideBounds(cell);\npreview.SetValid(valid);"],
            ['file' => 'ecs-performance', 'name' => 'Performance ECS', 'niveau_fr' => 'Expert', 'niveau_en' => 'Expert',
                'stack' => 'Unity ECS/DOTS · Burst · Jobs',
                'fr' => "Utilisation d'ECS/DOTS pour gagner en performance avec un nombre conséquent de mobs animés — comportements de chasse, repas, repos et mort, calculés en jobs Burst.",
                'en' => 'Uses ECS/DOTS to keep performance high with large numbers of animated mobs — hunting, eating, idling and dying, all computed in Burst-compiled jobs.',
                'code' => "[BurstCompile]\npartial struct MobBehaviorJob : IJobEntity {\n    void Execute(ref MobState state, in MobSensors sensors) {\n        state.Action = sensors.SeesFood ? Action.Eat\n                     : sensors.SeesPrey ? Action.Hunt\n                     : Action.Idle;\n    }\n}"],
        ];
    @endphp
</head>

<div style="position:relative;min-height:100vh;background:#05050a">
    <main style="position:relative;z-index:1">
        <section style="min-height:44vh;display:flex;flex-direction:column;justify-content:flex-end;gap:20px;padding:clamp(96px,14vh,150px) clamp(56px,9vw,180px) clamp(40px,6vw,80px)">
            <span data-reveal="rise" style="display:block;width:clamp(80px,12vw,180px);height:1px;background:linear-gradient(90deg,#9184d9,transparent)"></span>
            <h1 data-reveal="rise" style="margin:0;font-size:clamp(34px,6.2vw,86px);font-weight:200;letter-spacing:-.03em;line-height:.98">C# Code</h1>
            <p data-reveal="rise" style="margin:0;max-width:60ch;font-size:clamp(14px,1.1vw,16px);line-height:1.7;color:rgba(226,221,209,.62)">
                <span class="i18n-fr">Trois systèmes plus proches de l'algorithme que du visuel — détection et ciblage, placement en grille, performance ECS/DOTS. Chaque fiche ajoute un extrait représentatif du principe filmé.</span>
                <span class="i18n-en">Three systems closer to the algorithm than the visual — detection and targeting, grid placement, ECS/DOTS performance. Each card adds a snippet representative of the principle on screen.</span>
            </p>
        </section>

        <section style="padding:0 clamp(56px,9vw,180px) clamp(80px,10vh,140px)">
            <div style="display:grid;gap:24px;grid-template-columns:repeat(auto-fit,minmax(min(100%,460px),1fr))">
                @foreach ($systems as $i => $s)
                    <article data-reveal="rise" style="display:flex;flex-direction:column;gap:14px;border:1px solid rgba(233,233,237,.10);background:rgba(11,12,20,.55);border-radius:10px;overflow:hidden">
                        <div style="position:relative;aspect-ratio:16/9;background:#000">
                            <video src="{{ asset('videos/studio/' . $s['file'] . '.mp4') }}" controls preload="metadata" playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:10px;padding:6px 22px 4px">
                            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
                                <span style="font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#9184d9">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="tag tag-accent"><span class="i18n-fr">{{ $s['niveau_fr'] }}</span><span class="i18n-en">{{ $s['niveau_en'] }}</span></span>
                            </div>
                            <h3 style="margin:0;font-size:clamp(19px,2vw,24px);font-weight:200;letter-spacing:-.01em">{{ $s['name'] }}</h3>
                            <div style="font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:rgba(226,221,209,.34)">{{ $s['stack'] }}</div>
                            <p style="margin:0;font-size:13.5px;line-height:1.65;color:rgba(226,221,209,.62)">
                                <span class="i18n-fr">{{ $s['fr'] }}</span><span class="i18n-en">{{ $s['en'] }}</span>
                            </p>
                        </div>
                        <pre style="margin:0;padding:16px 22px 22px;font-family:ui-monospace,monospace;font-size:11.5px;line-height:1.65;color:rgba(226,221,209,.68);overflow-x:auto;border-top:1px solid rgba(233,233,237,.08);background:rgba(0,0,0,.2)"><code>{{ $s['code'] }}</code></pre>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
</div>
@endsection
