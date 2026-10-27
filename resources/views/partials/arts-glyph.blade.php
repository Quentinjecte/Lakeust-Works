{{-- Forme animée d'une discipline de la page À propos d'Arts (styles :
     css/pages/arts-theme.css). SVG purement décoratif, tout en CSS (aucun JS).
       $kind  : facet (props, hard surface) | voxel (personnages) |
                terrain (décors) | rig (squelette, animation)
       $color : couleur CSS de la carte, lue par le SVG via --c --}}
@php
    $kind  = $kind ?? 'facet';
    $color = $color ?? 'var(--ar-gold)';
@endphp
<div class="ar-glyph ar-glyph--{{ $kind }}" style="--c:{{ $color }}" aria-hidden="true">
    @switch($kind)

        {{-- gemme taillée : six facettes qui scintillent en décalé --}}
        @case('facet')
            <svg viewBox="0 0 120 100" preserveAspectRatio="xMidYMid meet">
                <ellipse class="shadow" cx="60" cy="96" rx="28" ry="3.2"/>
                <g class="gem">
                    <polygon class="f" style="--i:0" points="40,14 80,14 70,38 50,38"/>
                    <polygon class="f" style="--i:1" points="40,14 50,38 14,38"/>
                    <polygon class="f" style="--i:2" points="80,14 106,38 70,38"/>
                    <polygon class="f" style="--i:3" points="14,38 50,38 60,92"/>
                    <polygon class="f" style="--i:4" points="50,38 70,38 60,92"/>
                    <polygon class="f" style="--i:5" points="70,38 106,38 60,92"/>
                </g>
            </svg>
            @break

        {{-- cubes isométriques qui se posent un à un, puis disparaissent --}}
        @case('voxel')
            @php
                $s = 24; $w = round($s * 0.866, 2); $h = $s / 2; $ox = 80; $oy = 40;
                /* [x, y, z], triés du plus loin au plus proche (x+z croissant, puis y) : ordre de dessin */
                $cubes = [[0, 0, 0], [0, 1, 0], [1, 0, 0], [0, 0, 1], [1, 1, 0], [1, 0, 1]];
            @endphp
            <svg viewBox="0 0 160 128" preserveAspectRatio="xMidYMid meet">
                <ellipse class="shadow" cx="80" cy="118" rx="46" ry="5"/>
                @foreach ($cubes as $i => [$x, $y, $z])
                    @php
                        $cx = round($ox + ($x - $z) * $w, 2);
                        $cy = round($oy + ($x + $z) * $h - $y * $s, 2);
                    @endphp
                    <g class="vx" style="--i:{{ $i }}">
                        <polygon class="t" points="{{ $cx }},{{ $cy }} {{ $cx + $w }},{{ $cy + $h }} {{ $cx }},{{ $cy + $s }} {{ $cx - $w }},{{ $cy + $h }}"/>
                        <polygon class="l" points="{{ $cx - $w }},{{ $cy + $h }} {{ $cx }},{{ $cy + $s }} {{ $cx }},{{ $cy + 2 * $s }} {{ $cx - $w }},{{ $cy + $h + $s }}"/>
                        <polygon class="r" points="{{ $cx }},{{ $cy + $s }} {{ $cx + $w }},{{ $cy + $h }} {{ $cx + $w }},{{ $cy + $h + $s }} {{ $cx }},{{ $cy + 2 * $s }}"/>
                    </g>
                @endforeach
            </svg>
            @break

        {{-- relief low poly en trois plans qui dérivent, soleil qui monte --}}
        @case('terrain')
            <svg viewBox="0 0 200 120" preserveAspectRatio="xMidYMid slice">
                <g class="stars"><circle cx="30" cy="20" r="1.2"/><circle cx="66" cy="12" r="1"/><circle cx="112" cy="24" r="1.1"/><circle cx="178" cy="14" r="1"/></g>
                <circle class="sun" cx="146" cy="46" r="11"/>
                <polygon class="r1" points="-20,86 20,62 52,78 84,42 114,78 142,56 176,80 220,60 220,120 -20,120"/>
                <polygon class="r2" points="-20,102 22,82 52,98 88,70 120,100 152,84 220,106 220,120 -20,120"/>
                <polygon class="r3" points="-20,114 40,100 80,114 124,102 164,116 220,106 220,120 -20,120"/>
                <polygon class="tree" points="22,114 29,94 36,114"/>
                <polygon class="tree" points="38,116 44,100 50,116"/>
                <polygon class="tree" points="158,118 165,98 172,118"/>
            </svg>
            @break

        {{-- squelette : bras et jambes balancent autour de leurs articulations --}}
        @case('rig')
            <svg viewBox="0 0 120 140" preserveAspectRatio="xMidYMid meet">
                <ellipse class="shadow" cx="60" cy="134" rx="30" ry="3.5"/>
                <g class="rig">
                    <g class="limb leg l"><line class="bone" x1="60" y1="78" x2="48" y2="104"/><g class="sub shin l"><line class="bone" x1="48" y1="104" x2="46" y2="130"/><circle class="joint" cx="48" cy="104" r="3"/></g></g>
                    <g class="limb leg r"><line class="bone" x1="60" y1="78" x2="72" y2="104"/><g class="sub shin r"><line class="bone" x1="72" y1="104" x2="74" y2="130"/><circle class="joint" cx="72" cy="104" r="3"/></g></g>
                    <line class="bone spine" x1="60" y1="33" x2="60" y2="78"/>
                    <circle class="head" cx="60" cy="22" r="10"/>
                    <g class="limb arm l"><line class="bone" x1="60" y1="40" x2="36" y2="58"/><g class="sub fore l"><line class="bone" x1="36" y1="58" x2="26" y2="82"/><circle class="joint" cx="36" cy="58" r="3"/></g></g>
                    <g class="limb arm r"><line class="bone" x1="60" y1="40" x2="84" y2="58"/><g class="sub fore r"><line class="bone" x1="84" y1="58" x2="94" y2="82"/><circle class="joint" cx="84" cy="58" r="3"/></g></g>
                    <circle class="joint" cx="60" cy="40" r="3.4"/>
                    <circle class="joint" cx="60" cy="78" r="3.4"/>
                </g>
            </svg>
            @break

    @endswitch
</div>
