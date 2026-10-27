{{-- Forme animée propre à une techno Web (styles : css/pages/web-theme.css).
     $kind : scroll | animation | barba | three | carousel ; $color : couleur CSS
     de la carte ; $fill : true pour remplir un cadre déjà dimensionné. --}}
@php
    $kind  = $kind ?? 'scroll';
    $color = $color ?? 'var(--web-1)';
    $cls   = ['scroll' => 'g-scroll', 'animation' => 'g-anim', 'barba' => 'g-barba', 'three' => 'g-three', 'carousel' => 'g-carousel'][$kind] ?? 'g-scroll';
@endphp
<div class="web-glyph {{ $cls }} {{ !empty($fill) ? 'web-glyph--fill' : '' }}" style="--c:{{ $color }}" aria-hidden="true">
    @switch($kind)
        @case('scroll')
        @case('carousel')
            <i></i><i></i><i></i><i></i>
            @break
        @case('animation')
            <i></i><b></b>
            @break
        @case('barba')
            <i></i><i></i><b></b>
            @break
        @case('three')
            <b><i></i><i></i><i></i></b>
            @break
    @endswitch
</div>
