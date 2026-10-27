{{-- Décor animé d'un hero Web : nappes de couleur + anneau, cube 3D et croix
     flottants (styles : css/pages/web-theme.css, tout en CSS — aucun WebGL).
     À placer dans un .web-hero ; les formes se masquent d'elles-mêmes sous
     760px. L'identifiant du dégradé de l'anneau est propre à ce partial : à
     n'inclure qu'une fois par page. --}}
<div class="web-hero-bg" aria-hidden="true">
    <span class="web-blob b1"></span>
    <span class="web-blob b2"></span>
    <span class="web-blob b3"></span>

    <div class="web-shape sh-ring">
        <svg viewBox="0 0 200 200" fill="none">
            <defs>
                <linearGradient id="web-shapes-ring-g" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#8b7bff"/><stop offset="1" stop-color="#4ee0ff"/>
                </linearGradient>
            </defs>
            <circle class="r1" cx="100" cy="100" r="92" stroke="url(#web-shapes-ring-g)" stroke-width="1.5" stroke-dasharray="6 10"/>
            <circle class="r2" cx="100" cy="100" r="64" stroke="#ff5fa2" stroke-width="1.5" stroke-opacity=".7"/>
            <circle cx="100" cy="8" r="5" fill="#4ee0ff"/>
        </svg>
    </div>
    <div class="web-shape plus">
        <svg viewBox="0 0 40 40" fill="none"><path d="M20 4v32M4 20h32" stroke="#ffb86b" stroke-width="2.5" stroke-linecap="round"/></svg>
    </div>
    <div class="web-shape cube"><div class="web-cube"><i></i><i></i><i></i><i></i><i></i><i></i></div></div>
</div>
