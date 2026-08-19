/* animation.js — navigation, transition de page (rideau), reveals au scroll,
   dérive horizontale, parallax, liseré des cartes. Vanilla, sans dépendance. */

(() => {
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const raf = requestAnimationFrame;

    /* ---------------------------------------------------------------- nav --- */
    const nav = document.querySelector('[data-nav]');
    const progress = document.querySelector('[data-progress]');
    if (nav || progress) {
        let lastY = window.scrollY, ticking = false;
        const onScroll = () => {
            const y = window.scrollY;
            if (nav) {
                nav.classList.toggle('is-stuck', y > 24);
                // se cache en descendant vite, revient dès qu'on remonte
                nav.classList.toggle('is-hidden', y > 260 && y > lastY + 6);
            }
            if (progress) {
                const max = document.documentElement.scrollHeight - innerHeight;
                progress.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, y / max) : 0) + ')';
            }
            lastY = y;
            ticking = false;
        };
        addEventListener('scroll', () => { if (!ticking) { ticking = true; raf(onScroll); } }, { passive: true });
        onScroll();
    }

    /* indicateur de section actif dans la colonne collante */
    const marks = [...document.querySelectorAll('[data-section-link]')];
    if (marks.length) {
        const targets = marks.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                marks.forEach(a => a.classList.toggle('label-accent', a.getAttribute('href') === '#' + e.target.id));
            });
        }, { rootMargin: '-45% 0px -50% 0px' });
        targets.forEach(t => io.observe(t));
    }

    /* ------------------------------------------------------------- reveal --- */
    const revealables = document.querySelectorAll('[data-reveal], [data-count]');
    if (revealables.length) {
        if (reduced) revealables.forEach(el => { el.classList.add('is-in'); if (el.dataset.count !== undefined) countUp(el); });
        else {
            const io = new IntersectionObserver((entries, obs) => {
                entries.forEach(e => {
                    if (!e.isIntersecting) return;
                    const el = e.target;
                    const delay = parseInt(el.dataset.revealDelay || '0', 10);
                    setTimeout(() => el.classList.add('is-in'), delay);
                    if (el.dataset.count !== undefined) countUp(el);
                    obs.unobserve(el);            // ne rejoue jamais
                });
            }, { rootMargin: '0px 0px -12% 0px', threshold: 0.15 });
            revealables.forEach(el => io.observe(el));
        }
    }

    /* compteur des statistiques */
    function countUp(el) {
        const node = el.querySelector('[data-count-value]') || el;
        const to = parseFloat(el.dataset.count);
        const suffix = el.dataset.countSuffix || '';
        const dur = 1100;
        const t0 = performance.now();
        const tick = now => {
            const k = Math.min(1, (now - t0) / dur);
            const eased = 1 - Math.pow(1 - k, 3);
            const v = to % 1 ? (to * eased).toFixed(1) : Math.round(to * eased);
            node.textContent = v + suffix;
            if (k < 1) raf(tick);
        };
        raf(tick);
    }

    /* ---------------------------------------------- scrub (scroll lié) --- */
    /* Progression 0 → 1 d'un élément dans une bande du viewport, écrite dans --p.
       data-scrub="0.15 0.75" borne la bande (fractions de la hauteur d'écran) ;
       data-scrub-ease="out" applique un lissage. Une seule boucle rAF partagée. */
    const scrubbed = [...document.querySelectorAll('[data-scrub]')];
    if (scrubbed.length && !reduced) {
        const conf = scrubbed.map(el => {
            const b = (el.dataset.scrub || '').trim().split(/\s+/).map(parseFloat);
            return {
                el,
                from: isNaN(b[0]) ? 0.10 : b[0],
                to: isNaN(b[1]) ? 0.70 : b[1],
                ease: el.dataset.scrubEase || 'linear',
                last: -1
            };
        });
        let ticking = false;
        const easeOut = p => 1 - Math.pow(1 - p, 3);
        const run = () => {
            ticking = false;
            const vh = innerHeight;
            for (const c of conf) {
                const r = c.el.getBoundingClientRect();
                if (r.bottom < -400 || r.top > vh + 400) continue;
                // 0 quand le haut de l'élément atteint `to` de l'écran, 1 quand il atteint `from`
                const raw = (vh * c.to - r.top) / Math.max(1, vh * (c.to - c.from));
                let p = Math.max(0, Math.min(1, raw));
                if (c.ease === 'out') p = easeOut(p);
                if (Math.abs(p - c.last) < 0.002) continue;
                c.last = p;
                c.el.style.setProperty('--p', p.toFixed(4));
            }
        };
        const kick = () => { if (!ticking) { ticking = true; raf(run); } };
        addEventListener('scroll', kick, { passive: true });
        addEventListener('resize', kick);
        run();
    } else if (scrubbed.length) {
        scrubbed.forEach(el => el.style.setProperty('--p', '1'));
    }

    /* ----------------------------------------------------------- parallax --- */
    const parallaxed = [...document.querySelectorAll('.parallax')];
    if (parallaxed.length && !reduced) {
        let ticking = false;
        const update = () => {
            const vh = innerHeight;
            parallaxed.forEach(el => {
                const r = el.getBoundingClientRect();
                if (r.bottom < -200 || r.top > vh + 200) return;
                const amp = parseFloat(el.dataset.parallax || '26');
                const k = (r.top + r.height / 2 - vh / 2) / vh;     // -1 … 1
                el.style.setProperty('--py', (-k * amp).toFixed(2) + 'px');
            });
            ticking = false;
        };
        addEventListener('scroll', () => { if (!ticking) { ticking = true; raf(update); } }, { passive: true });
        addEventListener('resize', update);
        update();
    }

    /* ------------------------------------------------- liseré des cartes --- */
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('pointermove', e => {
            const r = card.getBoundingClientRect();
            card.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%');
            card.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%');
        });
    });

    /* -------------------------------------------------- dérive (1d) --------- */
    document.querySelectorAll('[data-drift]').forEach(setupDrift);

    function setupDrift(root) {
        const track = root.querySelector('.drift-track');
        const bar = root.querySelector('.drift-bar > span');
        const count = root.querySelector('[data-drift-count]');
        const items = [...root.querySelectorAll('.drift-item')];
        if (!track || !items.length) return;

        const mobile = () => matchMedia('(max-width: 900px)').matches;
        let span = 0, ticking = false;

        const measure = () => {
            span = Math.max(0, track.scrollWidth - innerWidth + parseFloat(getComputedStyle(track).paddingLeft) * 2);
            // hauteur de scroll = course horizontale + une hauteur d'écran
            root.style.height = mobile() ? '' : (innerHeight + span) + 'px';
        };

        const update = () => {
            ticking = false;
            if (mobile()) { track.style.transform = ''; return; }
            const r = root.getBoundingClientRect();
            const k = Math.min(1, Math.max(0, -r.top / (root.offsetHeight - innerHeight || 1)));
            track.style.transform = 'translate3d(' + (-k * span).toFixed(1) + 'px,0,0)';
            if (bar) bar.style.transform = 'translateX(' + (k * 195).toFixed(1) + '%)';
            const mid = innerWidth / 2;
            let active = 0, best = 1e9;
            items.forEach((it, i) => {
                const b = it.getBoundingClientRect();
                const d = Math.abs(b.left + b.width / 2 - mid);
                if (d < best) { best = d; active = i; }
                it.classList.toggle('is-near', d < b.width * 0.6);
                it.classList.toggle('is-far', d > b.width * 1.4);
            });
            if (count) count.textContent = String(active + 1).padStart(2, '0') + ' / ' + String(items.length).padStart(2, '0');
        };

        addEventListener('scroll', () => { if (!ticking) { ticking = true; raf(update); } }, { passive: true });
        addEventListener('resize', () => { measure(); update(); });
        measure(); update();
    }

    /* ------------------------------------------- transition de page (1k) --- */
    const curtain = document.querySelector('[data-curtain]');
    if (curtain) {
        // entrée : le rideau se lève au chargement
        if (!reduced) {
            curtain.classList.add('is-in');
            curtain.addEventListener('animationend', () => curtain.classList.remove('is-in'), { once: true });
        }

        const sameOrigin = a => a.host === location.host;
        const internal = a =>
            a && a.tagName === 'A' && a.href && sameOrigin(a) &&
            !a.hasAttribute('download') && !a.target && !a.dataset.noTransition &&
            !a.getAttribute('href').startsWith('#') &&
            !a.getAttribute('href').startsWith('mailto:') &&
            a.pathname !== location.pathname;

        document.addEventListener('click', e => {
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
            const a = e.target.closest && e.target.closest('a');
            if (!internal(a)) return;
            e.preventDefault();
            const url = a.href;
            if (reduced) { location.assign(url); return; }
            curtain.classList.remove('is-in');
            curtain.classList.add('is-out');
            setTimeout(() => location.assign(url), 520);
        });

        // retour navigateur : évite un rideau figé si la page revient du cache
        addEventListener('pageshow', e => { if (e.persisted) curtain.classList.remove('is-out'); });
    }
})();
