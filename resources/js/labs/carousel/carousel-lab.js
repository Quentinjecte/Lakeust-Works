/* labs/carousel-lab.js — entrée Vite de /carousel-lab. Page autonome (pas de
   layout partagé, pas de Barba) : douze écrans, un seul actif à la fois
   (rien ne défile — convention des autres labs), commutés par le rail
   gauche ou les touches 1-9 (10-12 restent accessibles par le rail et le
   catalogue — même limite que la maquette d'origine). Six d'entre eux sont
   un même jeu de huit plans rejoué sous six lois de placement différentes ;
   deux passent par post-fx.js (dissolve / fracture) ; trois occupent l'écran
   entier plutôt qu'une carte (bandeau / rideau / travelling — voir FULLS) ;
   le dernier est la fiche technique, statique.

   Une seule valeur pilote chaque écran-carrousel : la position fractionnaire
   dans la liste. Une section épinglée (ScrollTrigger, scrub) l'écrit depuis
   le scroll ; drag/clic/clavier ne font que déplacer ce scroll (via
   motion.js/Lenis) — aucune timeline ne s'empile, aucune entrée n'entre en
   conflit avec une autre. */

import * as M from '../../animation/motion.js';
import { createFX } from './post-fx.js';
import '../../three/carousel-field.js';

const clamp = (v, a, b) => v < a ? a : v > b ? b : v;
const DEG = 57.29577951;

/* Chaque loi renvoie la pose d'un plan pour une distance signée d à l'actif.
   Aucune ne connaît le scroll, le drag ni les autres : c'est le seul point
   d'extension du lab — ajouter une entrée ici suffit. */
const LAYOUTS = {
  arc(d, c) {
    const ad = Math.abs(d);
    return {
      x: d * c.U * 0.60, y: ad * ad * 7, z: -(ad * ad * 78 + ad * 66) * c.dp,
      rx: 0, ry: clamp(-d * 30, -44, 44), rz: 0,
      s: Math.max(0.60, 1 - ad * 0.10), o: ad > 3.4 ? 0 : clamp(1 - ad * 0.21, 0, 1), b: Math.min(5, Math.max(0, ad - 0.35) * 1.7)
    };
  },
  ring(d, c) {
    const ad = Math.abs(d), a = d * 0.52;
    return {
      x: Math.sin(a) * c.R, y: 0, z: (Math.cos(a) - 1) * c.R * c.dp,
      rx: 0, ry: -a * DEG, rz: 0,
      s: 1, o: ad > 3.6 ? 0 : clamp(1 - ad * 0.19, 0, 1), b: Math.min(5, Math.max(0, ad - 0.4) * 1.6)
    };
  },
  orbit(d, c) {
    const ad = Math.abs(d), sg = d < 0 ? -1 : 1, a = d * 0.60;
    return {
      x: Math.sin(a) * c.R * 0.92, y: sg * (1 - Math.cos(a)) * c.R * 0.58, z: (Math.cos(a) - 1) * c.R * 0.82 * c.dp,
      rx: -sg * ad * 9, ry: -a * DEG * 0.72, rz: -d * 6,
      s: 1, o: ad > 3.4 ? 0 : clamp(1 - ad * 0.20, 0, 1), b: Math.min(5, Math.max(0, ad - 0.4) * 1.7)
    };
  },
  helix(d, c) {
    const ad = Math.abs(d), a = d * 0.78;
    return {
      x: Math.sin(a) * c.R * 0.52, y: d * c.ch * 0.40, z: (Math.cos(a) - 1) * c.R * 0.58 * c.dp,
      rx: -d * 4.5, ry: -a * DEG * 0.62, rz: 0,
      s: Math.max(0.7, 1 - ad * 0.05), o: ad > 3.2 ? 0 : clamp(1 - ad * 0.22, 0, 1), b: Math.min(5, Math.max(0, ad - 0.35) * 1.8)
    };
  },
  stack(d, c) {
    const ad = Math.abs(d);
    return {
      x: d * 20, y: -ad * 13, z: -ad * 126 * c.dp, rx: 0, ry: d * 5, rz: d * 1.4,
      s: Math.max(0.72, 1 - ad * 0.045), o: ad > 4.4 ? 0 : clamp(1 - ad * 0.16, 0, 1), b: Math.min(5, Math.max(0, ad - 0.45) * 1.5)
    };
  },
  grid(d, c) {
    const ad = Math.abs(d), t = Math.max(0, 1 - ad * 1.5);
    const col = (c.i % 3) - 1, row = Math.floor(c.i / 3) - c.pos / 3;
    const gx = col * c.cw * 1.14, gy = row * c.ch * 1.16;
    return {
      x: gx * (1 - t), y: gy * (1 - t), z: (-150 * (1 - t) + 130 * t) * c.dp,
      rx: 0, ry: -col * 8 * (1 - t), rz: 0,
      s: 1 + 0.10 * t, o: ad > 4.6 ? 0 : clamp(1 - ad * 0.10, 0, 1), b: Math.min(4, Math.max(0, ad - 0.5) * 1.2)
    };
  }
};

/* Les lois plein cadre : le plan n'est plus une carte mais la section entière.
   Même contrat — une distance signée entre, une pose sort — mais la pose porte
   une découpe et une contre-transformation du média au lieu d'une pose 3D. */
const FULLS = {
  bandeau(d) {
    const ad = Math.abs(d);
    return {
      t: 'translate3d(' + (d * 100).toFixed(3) + '%,0,0)', o: ad > 1.04 ? 0 : 1,
      clip: 'none', mt: 'translate3d(' + (-d * 22).toFixed(2) + '%,0,0) scale(1.16)',
      ca: clamp(1 - ad * 2.1, 0, 1), z: 100 - Math.round(ad * 10)
    };
  },
  rideau(d, c) {
    const ad = Math.abs(d);
    if (d > 0) {
      return {
        t: 'none', o: d > 1.02 ? 0 : 1,
        clip: 'inset(' + (clamp(d, 0, 1) * 100).toFixed(3) + '% 0% 0% 0%)',
        mt: 'translate3d(0,' + (d * 14).toFixed(2) + '%,0) scale(1.08)',
        ca: clamp(1 - ad * 2.4, 0, 1), z: 100 + c.i
      };
    }
    return {
      t: 'scale(' + (1 + d * 0.06).toFixed(4) + ')', o: ad > 1.9 ? 0 : 1,
      clip: 'none', mt: 'scale(1.02)', ca: clamp(1 - ad * 2.4, 0, 1), z: 100 + c.i
    };
  },
  travelling(d) {
    const ad = Math.abs(d);
    return {
      t: 'scale(' + (d > 0 ? 1 + d * 0.16 : 1 + d * 0.10).toFixed(4) + ')',
      o: clamp(1 - ad * 1.35, 0, 1), clip: 'none',
      mt: 'scale(' + (1 + ad * 0.07).toFixed(4) + ') translate3d(0,' + (-d * 3).toFixed(2) + '%,0)',
      ca: clamp(1 - ad * 2.6, 0, 1), z: 100 - Math.round(ad * 10)
    };
  }
};

/* Une entrée par écran, dans l'ordre du rail — doit rester synchronisée avec
   la liste des sections rendues côté Blade (data-cl-index). */
/* L'ordre reflète celui du gabarit Blade ($sections) : la fiche/catalogue
   est le premier écran affiché, comme sur les autres labs (Catalogue
   d'abord, puis les mécaniques) — voir carousel-lab.blade.php. */
const SECTIONS = [
  { lay: null, n: 'IX', name: 'Catalogue' },
  { lay: 'arc', n: '01', name: 'Arc' },
  { lay: 'ring', n: '02', name: 'Anneau' },
  { lay: 'orbit', n: '03', name: 'Orbite' },
  { lay: 'helix', n: '04', name: 'Hélice' },
  { lay: 'stack', n: '05', name: 'Pile' },
  { lay: 'grid', n: '06', name: 'Grille' },
  { lay: null, fx: 'dissolve', n: '07', name: 'Dissolve' },
  { lay: null, fx: 'fracture', n: '08', name: 'Fracture' },
  { lay: null, full: 'bandeau', n: '09', name: 'Bandeau' },
  { lay: null, full: 'rideau', n: '10', name: 'Rideau' },
  { lay: null, full: 'travelling', n: '11', name: 'Travelling' }
];

const root = document.querySelector('[data-cl-root]');
if (root) boot(root);

/* Retour arrière depuis une page externe : le navigateur restaure ce
   document depuis le bfcache au lieu de le recharger, mais pagehide a déjà
   figé le ticker GSAP et coupé les listeners — sans ce ré-amorçage l'écran
   actif reste inerte jusqu'à un F5 (même défaut que sur le carrefour, voir
   welcome-lakeust.js). */
if (root) {
  addEventListener('pageshow', e => { if (e.persisted) boot(root); });
}

function boot(root) {
  const sections = Array.from(root.querySelectorAll('[data-cl-section]'));
  const railButtons = Array.from(root.querySelectorAll('[data-rail]'));
  const railIndicator = root.querySelector('[data-rail-indicator]');
  const registreButtons = Array.from(root.querySelectorAll('[data-registre]'));
  const progressBar = root.querySelector('[data-progress-bar]');
  const titleEl = root.querySelector('[data-lab-title]');
  const countEl = root.querySelector('[data-lab-count]');
  const fieldEl = root.querySelector('[data-cl-field]');

  let alive = true;
  let i = 0;
  let pos = 0, target = 0, lastPos = 0;
  let drag = null, dragV = 0;
  let cards = null, panels = null, media = null, copies = null, n = 0, lay = null, full = null;
  let cw = 240, ch = 320, mob = false, U = 0, R = 0, dp = 1;
  const depth = 1, warp = 1;

  let st = null, tick = null, fx = null, off = [];

  /* ------------------------------------------------------------ chrome -- */
  function updateChrome() {
    const s = SECTIONS[i];
    if (titleEl) titleEl.textContent = s.name;
    if (countEl) countEl.textContent = s.n + ' / ' + String(SECTIONS.length - 1).padStart(2, '0');
    if (progressBar) progressBar.style.transform = 'scaleX(' + ((i + 1) / SECTIONS.length).toFixed(4) + ')';
    if (railIndicator) railIndicator.style.transform = 'translateY(' + (i * 26) + 'px)';
    railButtons.forEach((b, k) => { b.style.color = k === i ? 'var(--text)' : 'rgba(226,221,209,.40)'; });
  }

  /* -------------------------------------------------------------- montage -- */
  function teardown() {
    if (tick) M.gsap.ticker.remove(tick);
    tick = null;
    if (st) { st.kill(true); st = null; }
    off.forEach(f => f());
    off = [];
    if (fx) { fx.destroy(); fx = null; }
    cards = null;
    panels = null;
    media = null;
    copies = null;
    full = null;
  }

  function measure() {
    const c = cards && cards[0];
    if (!c) return;
    cw = c.offsetWidth || 240;
    ch = c.offsetHeight || 320;
    mob = innerWidth < 760;
    U = cw * 1.06;
    R = Math.max(280, cw * (mob ? 1.55 : 1.95));
    dp = clamp(depth, 0.5, 1.8) * (mob ? 0.78 : 1);
  }

  function build(idx) {
    const s = SECTIONS[idx];
    const stage = sections[idx].querySelector('[data-stage]');
    lay = s.lay;
    if (s.fx) return buildFx(s.fx, stage);
    full = s.full || null;
    if (full) return buildFull(stage);
    if (!stage) return;
    cards = Array.from(stage.querySelectorAll('[data-card]'));
    n = cards.length;
    if (!n || !lay) return;

    measure();

    st = M.ScrollTrigger.create({
      trigger: stage,
      start: 'top top',
      end: '+=' + Math.round((n - 1) * 330),
      pin: stage,
      pinSpacing: true,
      scrub: true,
      invalidateOnRefresh: true,
      onUpdate: t => { target = t.progress * (n - 1); },
      onRefresh: () => measure()
    });

    tick = () => frame();
    M.gsap.ticker.add(tick);

    bind(stage);
    render(true);
    M.ScrollTrigger.refresh();
  }

  /* Écrans 09-11 : le plan n'est plus une carte mais la section entière —
     data-full-card/-media/-copy au lieu de data-card. Même pilote (scrub,
     scroll → position fractionnaire) que les six premières dispositions,
     juste une course de pin plus longue (le panneau traverse tout l'écran). */
  function buildFull(stage) {
    if (!stage) return;
    panels = Array.from(stage.querySelectorAll('[data-full-card]'));
    media = panels.map(p => p.querySelector('[data-full-media]'));
    copies = panels.map(p => p.querySelector('[data-full-copy]'));
    cards = panels;
    n = cards.length;
    if (!n) return;

    measure();

    st = M.ScrollTrigger.create({
      trigger: stage,
      start: 'top top',
      end: '+=' + Math.round((n - 1) * 430),
      pin: stage,
      pinSpacing: true,
      scrub: true,
      invalidateOnRefresh: true,
      onUpdate: t => { target = t.progress * (n - 1); },
      onRefresh: () => measure()
    });

    tick = () => frame();
    M.gsap.ticker.add(tick);

    bind(stage);
    render(true);
    M.ScrollTrigger.refresh();
  }

  /* Les écrans 07/08 ne scrubbent pas : le scroll ne fait qu'élire un index,
     et c'est la passe de post-traitement qui occupe l'intervalle. */
  function buildFx(kind, stage) {
    panels = stage ? Array.from(stage.querySelectorAll('[data-fx-card]')) : [];
    n = panels.length;
    if (!n) return;

    fx = createFX(kind, panels, { gsap: M.gsap });

    st = M.ScrollTrigger.create({
      trigger: stage,
      start: 'top top',
      end: '+=' + Math.round((n - 1) * 460),
      pin: stage,
      pinSpacing: true,
      invalidateOnRefresh: true,
      onUpdate: t => {
        const tt = t.progress * (n - 1);
        if (Math.abs(tt - fx.index) > 0.56) fx.show(Math.round(tt));
      }
    });

    tick = () => pushField();
    M.gsap.ticker.add(tick);
    bindFx(stage);
    M.ScrollTrigger.refresh();
  }

  function bindFx(stage) {
    const on = (el, ev, fn) => { el.addEventListener(ev, fn); off.push(() => el.removeEventListener(ev, fn)); };
    on(stage, 'click', e => {
      if (e.target.closest('image-slot, [data-no-nav]')) return;
      goTo(fx.index + 1);
    });
    on(window, 'keydown', e => {
      if (!st || !st.isActive) return;
      const k = fx.index;
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); goTo(k + 1); }
      else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); goTo(k - 1); }
      else if (e.key === 'Home') { e.preventDefault(); goTo(0); }
      else if (e.key === 'End') { e.preventDefault(); goTo(n - 1); }
    });
  }

  function pushField() {
    if (!fieldEl || !fieldEl.setField || !fx) return;
    const e = fx.energy;
    fieldEl.setField({ pos: fx.index, drag: 0, energy: clamp(e, 0, 1), warp: clamp(warp, 0, 2) * (1 + e * 0.7), focus: [0.5, 0.5] });
  }

  /* -------------------------------------------------------------- entrées -- */
  /* Aucune entrée n'écrit la position : toutes déplacent le scroll. */
  function goTo(idx, immediate) {
    if (!st || n < 2) return;
    const y = st.start + clamp(idx, 0, n - 1) / (n - 1) * (st.end - st.start);
    M.scrollTo(y, immediate ? { immediate: true } : { duration: 0.6 });
  }

  function bind(stage) {
    const on = (el, ev, fn, o) => { el.addEventListener(ev, fn, o); off.push(() => el.removeEventListener(ev, fn, o)); };

    on(stage, 'pointerdown', e => {
      if (e.target.closest('image-slot, [data-no-nav]')) return;
      if (e.pointerType === 'mouse' && e.button !== 0) return;
      drag = { x: e.clientX, i: target, moved: false };
      stage.setPointerCapture(e.pointerId);
      stage.style.cursor = 'grabbing';
    });
    on(stage, 'pointermove', e => {
      if (!drag) return;
      const dx = e.clientX - drag.x;
      if (Math.abs(dx) > 4) drag.moved = true;
      dragV = clamp(dx / (cw * 0.9), -1.6, 1.6);
      goTo(drag.i - dx / (cw * 0.78), true);
    });
    const end = e => {
      if (!drag) return;
      const moved = drag.moved;
      drag = null;
      dragV = 0;
      stage.style.cursor = 'grab';
      if (moved) goTo(Math.round(target));
      else {
        const card = e.target && e.target.closest ? e.target.closest('[data-card],[data-full-card]') : null;
        const k = card ? cards.indexOf(card) : -1;
        if (k >= 0 && k !== Math.round(target)) goTo(k);
      }
    };
    on(stage, 'pointerup', end);
    on(stage, 'pointercancel', end);

    on(window, 'keydown', e => {
      if (!st || !st.isActive) return;
      const k = Math.round(target);
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); goTo(k + 1); }
      else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); goTo(k - 1); }
      else if (e.key === 'Home') { e.preventDefault(); goTo(0); }
      else if (e.key === 'End') { e.preventDefault(); goTo(n - 1); }
    });

    on(window, 'resize', () => measure());
  }

  /* --------------------------------------------------------------- rendu -- */
  function frame() {
    const d = target - pos;
    pos += d * 0.17;
    if (Math.abs(d) < 0.0004) pos = target;
    render();
  }

  /* Même contrat que LAYOUTS (une distance signée entre, une pose sort),
     mais la pose porte une découpe (clip-path) et une contre-transformation
     du média au lieu d'une pose 3D — le plan est la section, pas une carte. */
  function renderFull() {
    const law = FULLS[full];
    const ctx = { n };
    for (let k = 0; k < n; k++) {
      ctx.i = k;
      const p = law(k - pos, ctx);
      const el = cards[k];
      el.style.transform = p.t;
      el.style.opacity = p.o.toFixed(3);
      el.style.clipPath = p.clip;
      el.style.zIndex = String(p.z);
      el.style.visibility = p.o < 0.003 ? 'hidden' : 'visible';
      el.style.pointerEvents = p.o > 0.5 ? 'auto' : 'none';
      const m = media[k];
      if (m) m.style.transform = p.mt;
      const c = copies[k];
      if (c) {
        c.style.opacity = p.ca.toFixed(3);
        c.style.transform = 'translate3d(0,' + ((1 - p.ca) * 22).toFixed(2) + 'px,0)';
      }
    }
  }

  function render(force) {
    if (full) return renderFull();
    if (!cards || !lay) return;
    const law = LAYOUTS[lay];
    const ctx = { U, R, cw, ch, dp, pos, n, i: 0 };
    const vel = pos - lastPos;
    lastPos = pos;

    for (let k = 0; k < n; k++) {
      ctx.i = k;
      const dd = k - pos, ad = Math.abs(dd);
      const p = law(dd, ctx);
      const el = cards[k];
      el.style.transform = 'translate(-50%,-50%) translate3d(' + p.x.toFixed(2) + 'px,' + p.y.toFixed(2) + 'px,' + p.z.toFixed(2) + 'px)' +
        ' rotateX(' + p.rx.toFixed(2) + 'deg) rotateY(' + p.ry.toFixed(2) + 'deg) rotateZ(' + p.rz.toFixed(2) + 'deg) scale(' + p.s.toFixed(4) + ')';
      el.style.opacity = p.o.toFixed(3);
      el.style.filter = p.b > 0.15 ? 'blur(' + p.b.toFixed(2) + 'px) brightness(' + (1 - Math.min(0.42, ad * 0.20)).toFixed(2) + ')' : '';
      el.style.zIndex = String(200 - Math.round(ad * 10));
      el.style.pointerEvents = p.o > 0.25 ? 'auto' : 'none';
      el.style.setProperty('--cl-a', Math.max(0, 1 - ad * 1.9).toFixed(3));
    }

    if (fieldEl && fieldEl.setField) {
      fieldEl.setField({
        pos,
        drag: drag ? dragV : clamp(-vel * 2.4, -1.4, 1.4),
        energy: clamp(Math.abs(vel) * 7 + Math.abs(dragV) * 0.6, 0, 1),
        warp: clamp(warp, 0, 2),
        focus: [0.5, 0.46]
      });
    }
  }

  /* ------------------------------------------------------------- écrans -- */
  function go(idx, immediate) {
    idx = clamp(idx, 0, SECTIONS.length - 1);
    if (idx === i && !immediate) return;
    i = idx;
    sections.forEach((sec, k) => { sec.style.display = k === i ? '' : 'none'; });
    updateChrome();
    teardown();
    pos = target = 0;
    build(i);
  }

  /* ------------------------------------------------------------- amorçage -- */
  updateChrome();
  sections.forEach((sec, k) => { sec.style.display = k === i ? '' : 'none'; });

  const onKey = e => {
    if (e.metaKey || e.ctrlKey || e.altKey) return;
    const num = parseInt(e.key, 10);
    if (num >= 1 && num <= SECTIONS.length) { e.preventDefault(); go(num - 1); }
  };
  addEventListener('keydown', onKey);

  railButtons.forEach((b, k) => b.addEventListener('click', () => go(k)));
  registreButtons.forEach(b => b.addEventListener('click', () => go(parseInt(b.dataset.registre, 10))));

  M.setMotion('auto');
  M.boot().then(() => { if (alive) build(i); }).catch(e => console.error('[carousel-lab]', e));

  addEventListener('pagehide', () => {
    alive = false;
    teardown();
    removeEventListener('keydown', onKey);
  }, { once: true });
}
