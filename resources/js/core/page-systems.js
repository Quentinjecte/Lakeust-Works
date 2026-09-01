/* page-systems.js — navigation, reveals au scroll, dérive horizontale,
   parallax, liseré des cartes. Vanilla, sans dépendance.

   Deux niveaux d'initialisation, depuis l'intégration Barba (voir
   core/barba-transitions.js) :
     - le bloc nav/progress ci-dessous s'exécute une seule fois : [data-nav] et
       [data-progress] vivent hors du wrapper Barba (chrome persistant), ils ne
       sont jamais remplacés par une transition de page.
     - initPage(root), exporté, monte tout ce qui dépend du contenu de la page
       (reveals, scrub, parallax, liseré des cartes, dérive horizontale) et
       renvoie une fonction de nettoyage. core/app.js l'appelle au premier
       chargement ; core/barba-transitions.js la rappelle à chaque entrée,
       après avoir nettoyé le montage précédent — sinon chaque transition
       empile un jeu de listeners supplémentaire sur des éléments déjà
       détachés du document. */

const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
const raf = requestAnimationFrame;

/* ---------------------------------------------------------------- nav --- */
/* Persistant : monté une fois, jamais démonté (le nav vit hors du conteneur
   que Barba remplace). */
(() => {
    const nav = document.querySelector('[data-nav]');
    const progress = document.querySelector('[data-progress]');
    if (!nav && !progress) return;

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
})();

/* Met à jour aria-current sur les liens de nav : Barba remplace le contenu
   mais pas le nav, donc rien ne rafraîchit cet attribut tout seul après une
   transition. */
export function setActiveNav(routeName) {
    document.querySelectorAll('[data-nav] .nav-link[data-route]').forEach(a => {
        a.toggleAttribute('aria-current', a.dataset.route === routeName);
        if (a.dataset.route === routeName) a.setAttribute('aria-current', 'page');
    });
}

/* -------------------------------------------------------------- reveal --- */

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

function setupReveal(root) {
    const revealables = root.querySelectorAll('[data-reveal], [data-count]');
    if (!revealables.length) return null;

    if (reduced) {
        revealables.forEach(el => { el.classList.add('is-in'); if (el.dataset.count !== undefined) countUp(el); });
        return null;
    }
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
    return () => io.disconnect();
}

/* indicateur de section actif dans la colonne collante */
function setupSectionMarks(root) {
    const marks = [...root.querySelectorAll('[data-section-link]')];
    if (!marks.length) return null;
    const targets = marks.map(a => root.querySelector(a.getAttribute('href'))).filter(Boolean);
    if (!targets.length) return null;
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            marks.forEach(a => a.classList.toggle('label-accent', a.getAttribute('href') === '#' + e.target.id));
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    targets.forEach(t => io.observe(t));
    return () => io.disconnect();
}

/* ---------------------------------------------- scrub (scroll lié) --- */
/* Progression 0 → 1 d'un élément dans une bande du viewport, écrite dans --p.
   data-scrub="0.15 0.75" borne la bande (fractions de la hauteur d'écran) ;
   data-scrub-ease="out" applique un lissage. Une seule boucle rAF partagée. */
function setupScrub(root) {
    const scrubbed = [...root.querySelectorAll('[data-scrub]')];
    if (!scrubbed.length) return null;

    if (reduced) { scrubbed.forEach(el => el.style.setProperty('--p', '1')); return null; }

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
    return () => { removeEventListener('scroll', kick); removeEventListener('resize', kick); };
}

/* ----------------------------------------------------------- parallax --- */
function setupParallax(root) {
    const parallaxed = [...root.querySelectorAll('.parallax')];
    if (!parallaxed.length || reduced) return null;

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
    const kick = () => { if (!ticking) { ticking = true; raf(update); } };
    addEventListener('scroll', kick, { passive: true });
    addEventListener('resize', update);
    update();
    return () => { removeEventListener('scroll', kick); removeEventListener('resize', update); };
}

/* ------------------------------------------------- liseré des cartes --- */
function setupCardSheen(root) {
    const cards = [...root.querySelectorAll('.card')];
    if (!cards.length) return null;
    const onMove = e => {
        const card = e.currentTarget;
        const r = card.getBoundingClientRect();
        card.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%');
        card.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%');
    };
    cards.forEach(card => card.addEventListener('pointermove', onMove));
    return () => cards.forEach(card => card.removeEventListener('pointermove', onMove));
}

/* -------------------------------------------------- dérive (1d) --------- */
function setupDrift(root) {
    const roots = [...root.querySelectorAll('[data-drift]')];
    if (!roots.length) return null;
    const disposers = roots.map(mountDrift).filter(Boolean);
    if (!disposers.length) return null;
    return () => disposers.forEach(fn => fn());
}

function mountDrift(root) {
    const track = root.querySelector('.drift-track');
    const bar = root.querySelector('.drift-bar > span');
    const count = root.querySelector('[data-drift-count]');
    const items = [...root.querySelectorAll('.drift-item')];
    if (!track || !items.length) return null;

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

    const kick = () => { if (!ticking) { ticking = true; raf(update); } };
    const onResize = () => { measure(); update(); };
    addEventListener('scroll', kick, { passive: true });
    addEventListener('resize', onResize);
    measure(); update();
    return () => { removeEventListener('scroll', kick); removeEventListener('resize', onResize); };
}

/* ------------------------------------------------------------ initPage --- */

let pageDisposers = [];

/* Monte tous les systèmes dépendant du contenu de la page, scopés à `root`
   (le conteneur Barba, ou document au premier chargement). Nettoie le montage
   précédent avant de rebrancher — obligatoire dès qu'on entre/sort de cette
   fonction plus d'une fois dans le même document. */
export function initPage(root = document) {
    pageDisposers.forEach(fn => fn());
    pageDisposers = [setupReveal, setupSectionMarks, setupScrub, setupParallax, setupCardSheen, setupDrift]
        .map(setup => setup(root))
        .filter(Boolean);
}

export function disposePage() {
    pageDisposers.forEach(fn => fn());
    pageDisposers = [];
}
