/* carousel.js — carousel réutilisable, vanilla.
   Usage : <div class="carousel" data-carousel data-mode="perspective" data-autoplay="6000">
   Modes : perspective (défaut) | slide | fade | stack
   Options (attributs) : data-mode, data-autoplay (ms), data-loop="false", data-gap
   API : el.carousel.next() / prev() / goTo(i) / pause() / play() / index
   Événement : "carousel:change" ({index, total}) sur l'élément racine. */

(() => {
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

    class Carousel {
        constructor(root) {
            this.root = root;
            this.viewport = root.querySelector('[data-carousel-viewport]') || root.querySelector('.carousel-viewport');
            this.slides = [...root.querySelectorAll('.carousel-slide')];
            this.dots = [...root.querySelectorAll('.carousel-dot')];
            this.indexLabel = root.querySelector('[data-carousel-index]');
            this.mode = root.dataset.mode || 'perspective';
            this.loop = root.dataset.loop !== 'false';
            this.autoplayMs = parseInt(root.dataset.autoplay || '0', 10);
            this.index = 0;
            this.drag = null;
            this.timer = null;

            if (!this.slides.length) return;
            this.bind();
            this.layout(0);
            this.startAutoplay();
            root.carousel = this;
        }

        /* ------------------------------------------------------------ state -- */
        goTo(i, from) {
            const n = this.slides.length;
            this.index = this.loop ? (i + n) % n : Math.max(0, Math.min(n - 1, i));
            this.layout(from === undefined ? 0 : from);
            this.dots.forEach((d, k) => d.setAttribute('aria-selected', String(k === this.index)));
            if (this.indexLabel) this.indexLabel.textContent = String(this.index + 1).padStart(2, '0') + ' / ' + String(n).padStart(2, '0');
            this.root.dispatchEvent(new CustomEvent('carousel:change', { detail: { index: this.index, total: n }, bubbles: true }));
        }
        next() { this.goTo(this.index + 1); }
        prev() { this.goTo(this.index - 1); }

        /* --------------------------------------------------------- placement -- */
        /* offset : décalage fractionnaire pendant le drag (en nombre de slides) */
        layout(offset = 0) {
            const n = this.slides.length;
            const w = this.slides[0].offsetWidth || 400;
            const gap = parseFloat(this.root.dataset.gap || '') || w * 0.14;

            this.slides.forEach((slide, i) => {
                let d = i - this.index - offset;
                if (this.loop) {                       // distance la plus courte en boucle
                    if (d > n / 2) d -= n;
                    if (d < -n / 2) d += n;
                }
                const ad = Math.abs(d);
                let tf = '', opacity = 1, filter = '', z = 10 - Math.round(ad * 2);

                if (this.mode === 'perspective') {
                    const x = d * (w * 0.52 + gap);
                    const rot = Math.max(-30, Math.min(30, -d * 26));
                    const scale = Math.max(0.72, 1 - ad * 0.12);
                    const zt = -ad * 130;
                    tf = `translate(-50%,-50%) translate3d(${x}px,0,${zt}px) rotateY(${rot}deg) scale(${scale})`;
                    opacity = ad > 2.2 ? 0 : Math.max(0, 1 - ad * 0.34);
                    filter = ad > 0.3 ? `brightness(${(1 - Math.min(0.42, ad * 0.22)).toFixed(2)})` : '';
                } else if (this.mode === 'slide') {
                    tf = `translate(-50%,-50%) translate3d(${d * (w + gap)}px,0,0)`;
                    opacity = ad > 1.6 ? 0 : 1;
                } else if (this.mode === 'fade') {
                    tf = `translate(-50%,-50%) scale(${1 - ad * 0.04})`;
                    opacity = ad < 0.5 ? 1 : 0;
                } else if (this.mode === 'stack') {
                    const dir = d < 0 ? -1 : 1;
                    tf = `translate(-50%,-50%) translate3d(${d > 0 ? ad * 26 : ad * -8}px, ${ad * -10}px, ${-ad * 60}px) rotate(${dir * ad * 2.2}deg) scale(${1 - ad * 0.06})`;
                    opacity = ad > 3 ? 0 : 1 - ad * 0.18;
                    z = 20 - Math.round(ad * 2);
                }

                slide.style.transform = tf;
                slide.style.opacity = String(opacity);
                slide.style.filter = filter;
                slide.style.zIndex = String(z);
                const active = Math.round(Math.abs(d)) === 0;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', String(!active));
            });
        }

        /* ------------------------------------------------------------- input -- */
        bind() {
            const root = this.root;

            root.querySelectorAll('[data-carousel-prev]').forEach(b => b.addEventListener('click', () => { this.prev(); this.bump(); }));
            root.querySelectorAll('[data-carousel-next]').forEach(b => b.addEventListener('click', () => { this.next(); this.bump(); }));
            this.dots.forEach((d, i) => d.addEventListener('click', () => { this.goTo(i); this.bump(); }));

            /* clavier */
            this.viewport.tabIndex = 0;
            this.viewport.setAttribute('role', 'group');
            this.viewport.addEventListener('keydown', e => {
                if (e.key === 'ArrowRight') { e.preventDefault(); this.next(); this.bump(); }
                if (e.key === 'ArrowLeft') { e.preventDefault(); this.prev(); this.bump(); }
                if (e.key === 'Home') { e.preventDefault(); this.goTo(0); }
                if (e.key === 'End') { e.preventDefault(); this.goTo(this.slides.length - 1); }
            });

            /* clic sur un plan latéral → il devient actif */
            this.slides.forEach((s, i) => s.addEventListener('click', () => {
                if (this.dragMoved) return;
                if (i !== this.index) { this.goTo(i); this.bump(); }
            }));

            /* drag souris + swipe tactile */
            const vp = this.viewport;
            vp.addEventListener('pointerdown', e => {
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                this.drag = { x: e.clientX, y: e.clientY, t: performance.now(), moved: false };
                this.dragMoved = false;
                vp.classList.add('is-drag');
                vp.setPointerCapture(e.pointerId);
                this.pause();
            });
            vp.addEventListener('pointermove', e => {
                if (!this.drag) return;
                const dx = e.clientX - this.drag.x;
                if (Math.abs(dx) > 4) { this.drag.moved = true; this.dragMoved = true; }
                const w = this.slides[0].offsetWidth || 400;
                this.layout(-dx / (w * 0.62));
            });
            const end = e => {
                if (!this.drag) return;
                const dx = (e.clientX ?? this.drag.x) - this.drag.x;
                const dt = performance.now() - this.drag.t;
                const w = this.slides[0].offsetWidth || 400;
                const flick = Math.abs(dx) / dt > 0.55;
                vp.classList.remove('is-drag');
                this.drag = null;
                if (Math.abs(dx) > w * 0.18 || flick) this.goTo(this.index + (dx < 0 ? 1 : -1));
                else this.layout(0);
                setTimeout(() => { this.dragMoved = false; }, 40);
                this.startAutoplay();
            };
            vp.addEventListener('pointerup', end);
            vp.addEventListener('pointercancel', end);

            /* pause au survol, et hors écran */
            root.addEventListener('pointerenter', () => this.pause());
            root.addEventListener('pointerleave', () => this.startAutoplay());
            root.addEventListener('focusin', () => this.pause());
            root.addEventListener('focusout', () => this.startAutoplay());
            document.addEventListener('visibilitychange', () => document.hidden ? this.pause() : this.startAutoplay());

            if (this.autoplayMs) {
                this.io = new IntersectionObserver(([e]) => e.isIntersecting ? this.startAutoplay() : this.pause(), { threshold: 0.35 });
                this.io.observe(root);
            }

            addEventListener('resize', () => this.layout(0));
        }

        bump() { this.pause(); this.startAutoplay(); }
        pause() { if (this.timer) { clearInterval(this.timer); this.timer = null; } }
        startAutoplay() {
            if (!this.autoplayMs || reduced || this.timer) return;
            this.timer = setInterval(() => this.next(), this.autoplayMs);
        }
    }

    const init = () => document.querySelectorAll('[data-carousel]').forEach(el => { if (!el.carousel) new Carousel(el); });
    if (document.readyState === 'loading') addEventListener('DOMContentLoaded', init);
    else init();

    window.Carousel = Carousel;
})();
