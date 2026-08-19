/* motion.js — socle d'animation réutilisable : Lenis (smooth scroll) + GSAP/ScrollTrigger.
   Version Vite : les dépendances viennent de npm (gsap, lenis), plus de CDN.
   Plugins gratuits uniquement — pas de SplitText, splitChars() le remplace.

   Un seul appel de boot() par page, avant tout autre appel :
     import * as M from './portfolio/motion.js';
     await M.boot();
   Chaque système renvoie sa fonction de nettoyage quand il en a une. */

import gsapLib from 'gsap';
import ScrollTriggerPlugin from 'gsap/ScrollTrigger';
import LenisLib from 'lenis';

/* Mode de mouvement : 'auto' suit prefers-reduced-motion, 'full' et 'reduce' forcent.
   Le prototype force 'full' (l'aperçu annonce reduce) ; en production, garder 'auto'. */
let motionMode = 'auto';
export function setMotion(mode) { motionMode = mode || 'auto'; }
export const reduced = () => motionMode === 'reduce' ||
  (motionMode !== 'full' && matchMedia('(prefers-reduced-motion: reduce)').matches);

/* Palier de performance. Sous 900px les scènes coûteuses (champ de points,
   corridor 3D) réduisent leur densité et leur cadence. Lu au montage ET au
   redimensionnement — une rotation d'écran doit changer de palier. */
export const lite = () => matchMedia('(max-width: 900px)').matches;

export let gsap = null;
export let ScrollTrigger = null;
export let lenis = null;

const HEX_0 = 'polygon(50% 50%, 50% 50%, 50% 50%, 50% 50%, 50% 50%, 50% 50%)';
const HEX_1 = 'polygon(0% -50%, 100% -50%, 150% 50%, 100% 150%, 0% 150%, -50% 50%)';

/* ------------------------------------------------------------------ boot --- */

export async function boot() {
  if (gsap) return { gsap, ScrollTrigger, lenis };
  gsap = gsapLib;
  ScrollTrigger = ScrollTriggerPlugin;
  gsap.registerPlugin(ScrollTrigger);
  gsap.defaults({ ease: 'power3.out', duration: 0.9 });
  gsap.ticker.lagSmoothing(0);

  if (!reduced()) {
    lenis = new LenisLib({
      duration: 1.05,
      smoothWheel: true,
      touchMultiplier: 1.6,
      easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t))
    });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(t => lenis.raf(t * 1000));
  }
  watchScroll(null);
  gsap.ticker.add(() => { if (performance.now() - _lastT > 70) velocity *= 0.82; });
  return { gsap, ScrollTrigger, lenis };
}

/* Valeur de scroll mise en cache (lue par les systèmes en boucle). */
let scrollY = 0;
export const getScroll = () => scrollY;

/* Vitesse de scroll normalisée (px / frame à 60 Hz), signée. Alimentée par le
   même flux que getScroll ; les systèmes « vitesse » (champ magnétique) la lisent. */
let velocity = 0, _lastY = 0, _lastT = 0;
export const getVelocity = () => velocity;
const clamp01 = t => t < 0 ? 0 : t > 1 ? 1 : t;
const smooth = t => { t = clamp01(t); return t * t * (3 - 2 * t); };
function track(y) {
  const t = performance.now(), dt = t - _lastT;
  if (dt > 8) { velocity = (y - _lastY) / dt * 16.67; _lastY = y; _lastT = t; }
}
/* Renvoie toujours sa fonction de désabonnement : les systèmes montés par écran
   doivent la remonter à mountScreen(), sinon un abonné fuit par visite. */
export function watchScroll(cb) {
  const push = y => { scrollY = y; track(y); if (cb) cb(y); };
  if (lenis) {
    const fn = e => push(e.scroll);
    lenis.on('scroll', fn);
    push(window.scrollY);
    return () => lenis.off('scroll', fn);
  }
  const fn = () => push(window.scrollY);
  addEventListener('scroll', fn, { passive: true });
  push(window.scrollY);
  return () => removeEventListener('scroll', fn);
}

export function scrollTo(y, opts = {}) {
  if (lenis) lenis.scrollTo(y, { duration: 0.7, ...opts });
  else window.scrollTo({ top: y, behavior: opts.immediate ? 'auto' : 'smooth' });
}

/* --------------------------------------------------------------- reveals --- */
/* Déclaratif : data-reveal="hex|iris|wipe|wipeUp|box|line|lineY|up|left|right|blur|persp|converge"
   data-reveal-delay (ms), data-reveal-stagger (ms, applique aux enfants directs). */

const SPECS = {
  up:      { from: { opacity: 0, y: 46 } },
  down:    { from: { opacity: 0, y: -46 } },
  left:    { from: { opacity: 0, x: -70 } },
  right:   { from: { opacity: 0, x: 70 } },
  fade:    { from: { opacity: 0, y: 14 } },
  blur:    { from: { opacity: 0, filter: 'blur(16px)', scale: 1.03 }, to: { filter: 'blur(0px)' } },
  persp:   { from: { opacity: 0, rotateX: -16, y: 48, transformPerspective: 900, transformOrigin: '50% 100%' } },
  zoom:    { from: { opacity: 0, scale: 0.88 } },
  iris:    { from: { clipPath: 'circle(0% at 50% 55%)' }, to: { clipPath: 'circle(85% at 50% 55%)' }, d: 1.15 },
  hex:     { from: { clipPath: HEX_0 }, to: { clipPath: HEX_1 }, d: 1.1 },
  wipe:    { from: { clipPath: 'inset(0% 100% 0% 0%)' }, to: { clipPath: 'inset(0% 0% 0% 0%)' }, d: 1 },
  wipeUp:  { from: { clipPath: 'inset(100% 0% 0% 0%)' }, to: { clipPath: 'inset(0% 0% 0% 0%)' }, d: 1 },
  box:     { from: { clipPath: 'inset(46% 46% 46% 46%)', opacity: 0.2 }, to: { clipPath: 'inset(0% 0% 0% 0%)', opacity: 1 }, d: 1.05 },
  line:    { from: { scaleX: 0, transformOrigin: '0% 50%' }, d: 0.8 },
  lineY:   { from: { scaleY: 0, transformOrigin: '50% 0%' }, d: 0.8 }
};

function applyReveal(el, delay = 0) {
  const spec = SPECS[el.dataset.mreveal] || SPECS.fade;
  const to = { ...(spec.to || {}), opacity: 1, x: 0, y: 0, scale: 1, rotateX: 0, scaleX: 1, scaleY: 1 };
  Object.keys(spec.from).forEach(k => { if (!(k in to) && k !== 'transformOrigin' && k !== 'transformPerspective') to[k] = null; });
  gsap.to(el, { ...to, duration: spec.d || 0.95, delay: delay / 1000, ease: spec.ease || 'power3.out',
    onComplete: () => { el.style.clipPath = ''; el.style.filter = ''; } });
}

export function reveals(root) {
  /* Attribut distinct de data-reveal : animation.js possède déjà data-reveal
     (IntersectionObserver + classe .is-in, cf. app.css) et partage plusieurs
     noms de variante avec SPECS ("blur", "hex", "iris", "wipe", "line",
     "left", "right") — les deux systèmes se disputaient alors le même
     élément. data-mreveal reste exclusif à ce système GSAP. */
  const els = [...root.querySelectorAll('[data-mreveal]')];
  const groups = [...root.querySelectorAll('[data-reveal-stagger]')];
  if (reduced()) return;

  els.forEach(el => {
    const spec = SPECS[el.dataset.mreveal] || SPECS.fade;
    gsap.set(el, spec.from);
    ScrollTrigger.create({
      trigger: el, start: 'top 90%', once: true,
      onEnter: () => applyReveal(el, parseInt(el.dataset.mrevealDelay || '0', 10))
    });
  });

  groups.forEach(g => {
    const step = parseInt(g.dataset.revealStagger || '80', 10);
    const kids = [...g.children];
    gsap.set(kids, { opacity: 0, y: 34 });
    ScrollTrigger.create({
      trigger: g, start: 'top 88%', once: true,
      onEnter: () => gsap.to(kids, { opacity: 1, y: 0, duration: 0.85, stagger: step / 1000 })
    });
  });
}

/* Rejoint le centre depuis les deux côtés : data-converge="-1|1" */
export function converge(root) {
  if (reduced()) return;
  root.querySelectorAll('[data-converge]').forEach(el => {
    const dir = parseFloat(el.dataset.converge) || 1;
    gsap.fromTo(el,
      { x: dir * (el.dataset.convergeDist ? +el.dataset.convergeDist : 220), opacity: 0, filter: 'blur(6px)' },
      { x: 0, opacity: 1, filter: 'blur(0px)', duration: 1.2, ease: 'power4.out',
        scrollTrigger: { trigger: el.parentElement, start: 'top 80%', once: true } });
  });
}

/* -------------------------------------------------------------- parallax --- */
/* data-speed : >0 = plus lent que le scroll, <0 = plus rapide.
   data-scrub-scale, data-scrub-rot : ajouts optionnels sur la même plage. */

export function parallax(root) {
  if (reduced()) return;
  root.querySelectorAll('[data-speed]').forEach(el => {
    const speed = parseFloat(el.dataset.speed) || 0;
    const scope = el.closest('[data-parallax-scope]') || el;
    const amp = () => innerHeight * 0.35 * speed;
    const vars = { ease: 'none', y: () => amp(), scrollTrigger: { trigger: scope, start: 'top bottom', end: 'bottom top', scrub: true, invalidateOnRefresh: true } };
    if (el.dataset.scrubScale) vars.scale = parseFloat(el.dataset.scrubScale);
    if (el.dataset.scrubRot) vars.rotate = parseFloat(el.dataset.scrubRot);
    gsap.fromTo(el, { y: () => -amp() }, vars);
  });
}

/* ------------------------------------------------- pin / scroll-driven --- */

export function pinned(el, build, opts = {}) {
  const tl = gsap.timeline({
    defaults: { ease: 'none' },
    scrollTrigger: {
      trigger: el,
      start: opts.start || 'top top',
      end: opts.end || '+=' + (opts.length || 2400),
      pin: opts.pin === false ? false : (opts.pinTarget || true),
      scrub: opts.scrub === undefined ? 0.6 : opts.scrub,
      anticipatePin: opts.anticipatePin === undefined ? 1 : opts.anticipatePin,
      invalidateOnRefresh: true,
      onUpdate: opts.onUpdate
    }
  });
  build(tl);
  if (reduced()) { tl.progress(1).pause(); tl.scrollTrigger && tl.scrollTrigger.disable(); }
  return tl;
}

export function driven(el, build, opts = {}) {
  return pinned(el, build, { ...opts, pin: false, scrub: opts.scrub ?? true });
}

/* --------------------------------------------------------- panels + snap --- */
/* Snap maison piloté par Lenis (un seul système contrôle le scroll). */

export function snapPanels(container, { onChange } = {}) {
  const panels = [...container.querySelectorAll('[data-panel]')];
  if (!panels.length) return () => {};
  let active = -1, snapping = false, timer = 0;

  const tops = () => panels.map(p => p.getBoundingClientRect().top + window.scrollY);

  const setActive = i => { if (i !== active) { active = i; onChange && onChange(i, panels[i]); } };

  const onScroll = y => {
    if (!container.isConnected) return;
    const t = tops();
    let i = 0;
    for (let k = 0; k < t.length; k++) if (y >= t[k] - innerHeight * 0.5) i = k;
    setActive(i);
    if (reduced() || !lenis) return;
    clearTimeout(timer);
    timer = setTimeout(() => {
      if (snapping) return;
      const cur = window.scrollY;
      const inRange = cur > t[0] - innerHeight * 0.4 && cur < t[t.length - 1] + innerHeight * 0.6;
      if (!inRange) return;
      let best = t[0];
      t.forEach(v => { if (Math.abs(v - cur) < Math.abs(best - cur)) best = v; });
      const d = best - cur;
      if (Math.abs(d) < 3 || Math.abs(d) > innerHeight * 0.55) return;
      snapping = true;
      lenis.scrollTo(best, { duration: 0.55, easing: x => 1 - Math.pow(1 - x, 3), lock: true,
        onComplete: () => { snapping = false; } });
      setTimeout(() => { snapping = false; }, 900);
    }, 150);
  };

  // parallaxe interne à chaque panneau
  if (!reduced()) panels.forEach(p => {
    p.querySelectorAll('[data-panel-speed]').forEach(el => {
      const s = parseFloat(el.dataset.panelSpeed) || 0.2;
      gsap.fromTo(el, { y: () => innerHeight * 0.28 * s }, {
        y: () => -innerHeight * 0.28 * s, ease: 'none',
        scrollTrigger: { trigger: p, start: 'top bottom', end: 'bottom top', scrub: true, invalidateOnRefresh: true }
      });
    });
  });

  const off = watchScroll(onScroll);
  onScroll(window.scrollY);
  return () => { clearTimeout(timer); off(); };
}

/* ------------------------------------------------------- scroll infini --- */
/* Pool de nœuds recyclés dans un conteneur virtuel qui s'allonge. */

export function infinite(root, { itemH = 168, gap = 14, render, initial = 40, pool = 0 } = {}) {
  const step = itemH + gap;
  let virtual = initial;
  const need = pool || Math.ceil(innerHeight / step) + 4;
  const nodes = [];
  root.style.position = 'relative';
  root.style.height = virtual * step + 'px';

  for (let i = 0; i < need; i++) {
    const n = document.createElement('div');
    n.style.cssText = `position:absolute;left:0;right:0;height:${itemH}px;will-change:transform;`;
    n._idx = -1;
    root.appendChild(n);
    nodes.push(n);
  }

  let last = -1;
  const draw = () => {
    if (!root.isConnected) return;
    const top = root.getBoundingClientRect().top + window.scrollY;
    const y = window.scrollY - top;
    const first = Math.max(0, Math.floor(y / step) - 2);
    if (first === last) return;
    last = first;
    nodes.forEach((n, k) => {
      const idx = first + k;
      n.style.transform = `translate3d(0,${idx * step}px,0)`;
      if (n._idx !== idx) { n._idx = idx; render(n, idx); }
    });
    if (first + need > virtual - 6) { virtual += 30; root.style.height = virtual * step + 'px'; ScrollTrigger.refresh(); }
  };

  const off = watchScroll(draw);
  draw();
  requestAnimationFrame(draw);
  return () => { off(); root.innerHTML = ''; };
}

/* ------------------------------------------------- typographie découpée --- */
/* Remplacement libre de SplitText : renvoie les <span> de caractères. */

export function splitChars(el) {
  if (el._split) return el._split;
  const text = el.textContent;
  el.textContent = '';
  const out = [];
  text.split(' ').forEach((word, wi, arr) => {
    const w = document.createElement('span');
    w.style.cssText = 'display:inline-flex;overflow:hidden;vertical-align:top;';
    [...word].forEach(ch => {
      const s = document.createElement('span');
      s.textContent = ch;
      s.style.cssText = 'display:inline-block;will-change:transform;';
      w.appendChild(s);
      out.push(s);
    });
    el.appendChild(w);
    if (wi < arr.length - 1) el.appendChild(document.createTextNode(' '));
  });
  el._split = out;
  return out;
}

export function charsIn(el, opts = {}) {
  const chars = splitChars(el);
  if (reduced()) return;
  gsap.fromTo(chars, { yPercent: 118, opacity: 0 }, {
    yPercent: 0, opacity: 1, duration: 0.9, ease: 'power4.out', stagger: opts.stagger || 0.022,
    scrollTrigger: opts.trigger === false ? undefined : { trigger: opts.scope || el, start: 'top 88%', once: true },
    delay: opts.delay || 0
  });
}

/* -------------------------------------------------------------- compteur --- */

export function counters(root) {
  root.querySelectorAll('[data-count]').forEach(el => {
    const end = parseFloat(el.dataset.count);
    const dec = (el.dataset.countDec | 0);
    const suffix = el.dataset.countSuffix || '';
    const o = { v: 0 };
    const set = () => { el.textContent = o.v.toFixed(dec) + suffix; };
    if (reduced()) { o.v = end; set(); return; }
    set();
    gsap.to(o, { v: end, duration: 1.6, ease: 'power2.out', onUpdate: set,
      scrollTrigger: { trigger: el, start: 'top 92%', once: true } });
  });
}

/* --------------------------------------------- transition géométrique --- */
/* Ferme sur un hexagone qui grandit, échange le contenu, puis deux
   panneaux s'écartent depuis une ligne centrale. */

export function geoTransition(swap) {
  const hex = document.querySelector('[data-geo-hex]');
  const top = document.querySelector('[data-geo-top]');
  const bot = document.querySelector('[data-geo-bot]');
  const line = document.querySelector('[data-geo-line]');
  const done = () => Promise.resolve(swap && swap());

  if (reduced() || !hex || !top || !bot) { return done(); }

  return new Promise(resolve => {
    const tl = gsap.timeline({ onComplete: resolve });
    gsap.set([hex, top, bot], { visibility: 'visible' });
    gsap.set(hex, { scale: 0, rotate: -12, opacity: 1 });
    gsap.set([top, bot], { yPercent: 0, opacity: 0 });
    gsap.set(line, { scaleX: 0, opacity: 1 });

    tl.to(hex, { scale: 2.6, rotate: 0, duration: 0.62, ease: 'power3.inOut' })
      .set([top, bot], { opacity: 1 }, '>-0.02')
      .set(hex, { opacity: 0, scale: 0 })
      .to(line, { scaleX: 1, duration: 0.3, ease: 'power2.out' }, '<')
      .add(() => { done(); ScrollTrigger.refresh(); scrollTo(0, { immediate: true }); })
      .to({}, { duration: 0.14 })
      .to(line, { opacity: 0, duration: 0.3 }, '>-0.05')
      .to(top, { yPercent: -100, duration: 0.72, ease: 'power3.inOut' }, '<')
      .to(bot, { yPercent: 100, duration: 0.72, ease: 'power3.inOut' }, '<')
      .set([hex, top, bot], { visibility: 'hidden' });
  });
}

/* ------------------------------------------------------- 07 · orbite --- */
/* Le scroll devient un contrôleur orbital : arrivée sur l'orbite, rotation +
   bascule du plan, puis effondrement de l'anneau en liste. Position calculée
   à chaque frame depuis la progression — aucune animation autonome. */

export function orbit(stage, { length = 3600, spins = 1.25, onUpdate } = {}) {
  const items = [...stage.querySelectorAll('[data-orb]')];
  if (!items.length) return;
  const N = items.length;
  let last = 0;

  const layout = p => {
    const r = stage.getBoundingClientRect();
    if (!r.width) return;
    const R = Math.min(r.width * 0.34, r.height * 0.42);
    const arrive = smooth(p / 0.22);
    const tilt = 0.24 + 0.46 * smooth((p - 0.24) / 0.30);
    const collapse = smooth((p - 0.74) / 0.24);
    const spin = p * spins * Math.PI * 2;
    /* état final : l'anneau se range en grille (mesurée sur la première fiche) */
    const iw = items[0].offsetWidth || 200, ih = items[0].offsetHeight || 110;
    const cols = Math.max(1, Math.min(Math.floor(r.width * 0.82 / (iw * 1.1)), Math.ceil(Math.sqrt(N))));
    const rows = Math.ceil(N / cols);
    const gw = iw * 1.12, gh = ih * 1.18;
    items.forEach((el, i) => {
      const a = (i / N) * Math.PI * 2 + spin - Math.PI / 2;
      const rad = R * (0.26 + 0.74 * arrive);
      const ox = Math.cos(a) * rad, oy = Math.sin(a) * rad * tilt;
      const depth = (Math.sin(a) + 1) / 2;
      const os = 0.54 + 0.56 * depth;
      const sx = ((i % cols) - (cols - 1) / 2) * gw;
      const sy = (Math.floor(i / cols) - (rows - 1) / 2) * gh;
      const op = ((0.20 + 0.80 * depth) * (1 - collapse) + collapse) * (0.10 + 0.90 * arrive);
      gsap.set(el, {
        x: r.width / 2 + ox + (sx - ox) * collapse,
        y: r.height / 2 + oy + (sy - oy) * collapse,
        xPercent: -50, yPercent: -50,
        scale: os + (1 - os) * collapse,
        rotate: (1 - collapse) * Math.sin(a) * 7,
        opacity: op,
        filter: 'blur(' + ((1 - depth) * 2.4 * (1 - collapse)).toFixed(2) + 'px)',
        zIndex: Math.round(depth * 60) + (collapse > 0.5 ? 40 : 0)
      });
    });
    last = p;
    onUpdate && onUpdate(p, { collapse, tilt });
  };

  pinned(stage, () => {}, { length, scrub: 0.5, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 0.55 : 0);
  const onResize = () => layout(last);
  addEventListener('resize', onResize);
  return () => removeEventListener('resize', onResize);
}

/* --------------------------------------------- 08 · champ magnétique --- */
/* Ce n'est plus la position du scroll qui pilote, mais sa VITESSE et son SIGNE :
   scroll descendant = attraction, scroll montant = répulsion, amplitude = vitesse.
   À l'arrêt le champ se détend de lui-même. */

export function magnet(stage, { cols, rows, radius = 0.30, onUpdate } = {}) {
  const field = stage.querySelector('[data-field]') || stage;
  const attractor = stage.querySelector('[data-attractor]');
  let dots = [], small = lite();

  /* La grille est reconstruite quand on franchit le palier : 135 nœuds sur
     desktop, 54 sous 900px (mêmes lois, moins de matière à écrire par frame). */
  const build = () => {
    dots.forEach(d => d.remove());
    dots = [];
    const nx = cols || (small ? 9 : 15), ny = rows || (small ? 6 : 9);
    const s = small ? 4 : 5;
    for (let ry = 0; ry < ny; ry++) for (let cx = 0; cx < nx; cx++) {
      const d = document.createElement('span');
      d.style.cssText = 'position:absolute;width:' + s + 'px;height:' + s + 'px;margin:' +
        (-s / 2) + 'px;border-radius:50%;background:#9184d9;will-change:transform,opacity;';
      d._fx = (cx + 0.5) / nx; d._fy = (ry + 0.5) / ny;
      d.style.left = (d._fx * 100) + '%'; d.style.top = (d._fy * 100) + '%';
      d._x = 0; d._y = 0; d._s = 1;
      field.appendChild(d); dots.push(d);
    }
  };
  build();

  let amp = 0, pol = 1, frame = 0;
  const tick = () => {
    if (!stage.isConnected) return;
    /* Sous 900px : une frame sur deux — le lissage à 0.10 absorbe l'écart. */
    if (small && (++frame & 1)) return;
    const r = stage.getBoundingClientRect();
    if (!r.height) return;
    const p = clamp01((innerHeight - r.top) / (innerHeight + r.height));
    const v = getVelocity();
    if (Math.abs(v) > 0.6) pol = v > 0 ? 1 : -1;
    amp += (Math.min(1, Math.abs(v) / 46) - amp) * 0.09;
    const ax = 0.5 + Math.sin(p * Math.PI * 2) * 0.30;
    const ay = 0.12 + p * 0.76;
    if (attractor) {
      attractor.style.left = (ax * 100) + '%';
      attractor.style.top = (ay * 100) + '%';
      attractor.style.transform = 'translate(-50%,-50%) scale(' + (0.7 + amp * 0.8).toFixed(2) + ')';
      attractor.style.borderColor = pol > 0 ? 'rgba(145,132,217,.85)' : 'rgba(226,221,209,.45)';
    }
    const ratio = r.height / r.width;
    dots.forEach(d => {
      const dx = d._fx - ax, dy = (d._fy - ay) * ratio;
      const dist = Math.hypot(dx, dy) || 1e-4;
      const f = Math.max(0, 1 - dist / radius);
      const push = f * f * pol * (0.05 + amp * 0.20) * r.width;
      const tx = dx / dist * push, ty = dy / dist * push / ratio;
      d._x += (tx - d._x) * 0.10; d._y += (ty - d._y) * 0.10;
      d._s += ((1 + f * (1.4 + amp * 2.6)) - d._s) * 0.10;
      d.style.transform = 'translate3d(' + d._x.toFixed(1) + 'px,' + d._y.toFixed(1) + 'px,0) scale(' + d._s.toFixed(2) + ')';
      d.style.opacity = (0.16 + f * 0.84).toFixed(2);
    });
    onUpdate && onUpdate({ p, v, pol, amp });
  };

  const onResize = () => { if (lite() === small) return; small = lite(); build(); };
  addEventListener('resize', onResize);
  const stop = () => {
    removeEventListener('resize', onResize);
    dots.forEach(d => d.remove());
  };

  if (reduced()) { tick(); return stop; }
  gsap.ticker.add(tick);
  return () => { gsap.ticker.remove(tick); stop(); };
}

/* ----------------------------------------------------- 09 · corridor --- */
/* Scroll spatial : la page ne défile pas, la caméra avance en Z. Les plans
   passent devant puis derrière l'observateur. */

export function corridor(stage, { length = 4400, travel = 7200, anticipatePin, onUpdate } = {}) {
  const inner = stage.querySelector('[data-corridor]');
  const planes = [...stage.querySelectorAll('[data-plane]')];
  if (!inner || !planes.length) return;
  const base = planes.map(el => parseFloat(el.dataset.plane) || 0);
  let last = 0, small = lite();

  const layout = p => {
    /* Sous 900px on garde l'axe Z et on abandonne le roulis du volume : c'est
       lui qui recompose tout le sous-arbre 3D à chaque frame. Course raccourcie
       pour compenser la perspective plus courte. */
    const cam = p * travel * (small ? 0.7 : 1);
    inner.style.transform = small ? 'none' :
      'rotateY(' + (Math.sin(p * Math.PI * 2) * 5).toFixed(2) + 'deg) rotateX(' +
      (Math.cos(p * Math.PI * 2) * 2.2).toFixed(2) + 'deg)';
    planes.forEach((el, i) => {
      const z = base[i] + cam;
      const dx = parseFloat(el.dataset.planeX || '0'), dy = parseFloat(el.dataset.planeY || '0');
      const op = z > 220 || z < -6200 ? 0 :
        Math.min(1, (z + 6200) / 2400) * Math.min(1, (220 - z) / 900);
      el.style.transform = 'translate3d(calc(-50% + ' + dx + 'px), calc(-50% + ' + dy + 'px), ' + z.toFixed(0) + 'px)' +
        (el.dataset.planeRot ? ' rotate(' + el.dataset.planeRot + 'deg)' : '');
      el.style.opacity = op.toFixed(3);
      el.style.visibility = op < 0.01 ? 'hidden' : 'visible';
    });
    last = p;
    onUpdate && onUpdate(p, cam);
  };

  pinned(stage, () => {}, { length, scrub: 0.45, anticipatePin, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 0.45 : 0);
  const onResize = () => { small = lite(); layout(last); };
  addEventListener('resize', onResize);
  return () => removeEventListener('resize', onResize);
}

/* ---------------------------------------------- 10 · déconstruction --- */
/* La section sortante est démontée pièce par pièce ; la suivante se construit
   à partir des trajectoires libérées. data-part / data-build = "x,y,rot". */

const _rnd = i => { const v = Math.sin((i + 1) * 12.9898) * 43758.5453; return v - Math.floor(v); };

export function deconstruct(stage, { length = 3400, onUpdate } = {}) {
  const title = stage.querySelector('[data-decon-title]');
  const parts = [...stage.querySelectorAll('[data-part]')];
  const builds = [...stage.querySelectorAll('[data-build]')];
  const chars = title ? splitChars(title) : [];
  const vec = (t, key, k) => {
    const a = (t.dataset[key] || '').split(',').map(Number);
    return isFinite(a[k]) ? a[k] : 0;
  };

  gsap.set(builds, { opacity: 0 });

  pinned(stage, tl => {
    if (chars.length) tl.to(chars, {
      x: i => (_rnd(i) - 0.5) * 780, y: i => (_rnd(i + 31) - 0.5) * 620,
      rotate: i => (_rnd(i + 7) - 0.5) * 200, scale: i => 0.35 + _rnd(i + 13),
      opacity: 0, duration: 1.8, ease: 'power2.in', stagger: { each: 0.02, from: 'random' }
    }, 0);
    if (parts.length) tl.to(parts, {
      x: (i, t) => vec(t, 'part', 0), y: (i, t) => vec(t, 'part', 1),
      rotate: (i, t) => vec(t, 'part', 2), opacity: 0,
      duration: 1.9, ease: 'power2.in', stagger: 0.06
    }, 0.12);
    if (builds.length) tl.fromTo(builds, {
      x: (i, t) => vec(t, 'build', 0), y: (i, t) => vec(t, 'build', 1),
      rotate: (i, t) => vec(t, 'build', 2), opacity: 0
    }, {
      x: 0, y: 0, rotate: 0, opacity: 1, duration: 2, ease: 'power3.out', stagger: 0.08
    }, 1.5);
  }, { length, scrub: 0.4, onUpdate: st => onUpdate && onUpdate(st.progress) });
}

/* ------------------------------------------------ 11 · zones fracturées --- */
/* Une même section, quatre lois de mouvement : data-zone (vitesse signée),
   data-zone-scale, data-zone-rot. Les valeurs négatives remontent le contenu. */

export function fracture(stage) {
  if (reduced()) return;
  stage.querySelectorAll('[data-zone]').forEach(z => {
    const inner = z.querySelector('[data-zone-inner]') || z.firstElementChild;
    if (!inner) return;
    const s = parseFloat(z.dataset.zone) || 0.4;
    const amp = () => (z.getBoundingClientRect().height || innerHeight) * 0.55 * s;
    const vars = { y: () => -amp(), ease: 'none',
      scrollTrigger: { trigger: stage, start: 'top bottom', end: 'bottom top', scrub: true, invalidateOnRefresh: true } };
    if (z.dataset.zoneScale) vars.scale = parseFloat(z.dataset.zoneScale);
    if (z.dataset.zoneRot) vars.rotate = parseFloat(z.dataset.zoneRot);
    gsap.fromTo(inner, { y: () => amp() }, vars);
  });
}

/* --------------------------------------------------- 12 · morphogenèse --- */
/* Interpolation de formes par échantillonnage angulaire : chaque forme est
   rendue avec le même nombre de points, dans le même ordre, donc n'importe
   quel couple de formes s'interpole exactement. */

const SHAPES = {
  rect: [[0, 0], [100, 0], [100, 100], [0, 100]],
  hex: [[25, 0], [75, 0], [100, 50], [75, 100], [25, 100], [0, 50]],
  tri: [[50, 0], [100, 100], [0, 100]],
  diamond: [[50, 0], [100, 50], [50, 100], [0, 50]]
};

function rayHit(verts, c, s) {
  let best = Infinity;
  for (let i = 0; i < verts.length; i++) {
    const [x1, y1] = verts[i], [x2, y2] = verts[(i + 1) % verts.length];
    const ex = x2 - x1, ey = y2 - y1;
    const D = -c * ey + ex * s;
    if (Math.abs(D) < 1e-9) continue;
    const t = (-(x1 - 50) * ey + ex * (y1 - 50)) / D;
    const u = (c * (y1 - 50) - s * (x1 - 50)) / D;
    if (t > 0 && u >= -1e-6 && u <= 1 + 1e-6) best = Math.min(best, t);
  }
  return isFinite(best) ? best : 50;
}

const _pts = {};
export function shapePoints(name, n = 48) {
  const key = name + n;
  if (_pts[key]) return _pts[key];
  const out = [];
  for (let k = 0; k < n; k++) {
    const a = -Math.PI / 2 + (k / n) * Math.PI * 2;
    const c = Math.cos(a), s = Math.sin(a);
    const r = name === 'circle' ? 50 : rayHit(SHAPES[name] || SHAPES.rect, c, s);
    out.push([50 + c * r, 50 + s * r]);
  }
  _pts[key] = out;
  return out;
}

export function morph(stage, { shapes = ['rect', 'hex', 'circle', 'rect'], length = 3400, onUpdate } = {}) {
  const el = stage.querySelector('[data-morph]');
  if (!el) return;
  const seq = shapes.map(s => shapePoints(s));
  const layers = [...stage.querySelectorAll('[data-morph-at]')];

  const layout = p => {
    const seg = clamp01(p) * (seq.length - 1);
    const i = Math.min(seq.length - 2, Math.floor(seg));
    const t = smooth(seg - i);
    const a = seq[i], b = seq[i + 1];
    let out = 'polygon(';
    for (let k = 0; k < a.length; k++) {
      out += (a[k][0] + (b[k][0] - a[k][0]) * t).toFixed(2) + '% ' +
             (a[k][1] + (b[k][1] - a[k][1]) * t).toFixed(2) + '%' + (k < a.length - 1 ? ', ' : '');
    }
    el.style.clipPath = out + ')';
    el.style.transform = 'rotate(' + (-14 * Math.sin(p * Math.PI)).toFixed(2) + 'deg) scale(' + (0.74 + 0.26 * p).toFixed(3) + ')';
    layers.forEach(l => {
      const at = parseFloat(l.dataset.morphAt);
      const win = parseFloat(l.dataset.morphWin || '0.20');
      l.style.opacity = clamp01(1 - Math.abs(p - at) / win).toFixed(3);
    });
    onUpdate && onUpdate(p, i, t);
  };

  pinned(stage, () => {}, { length, scrub: 0.4, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 1 : 0);
  const onResize = () => layout(0);
  return () => removeEventListener('resize', onResize);
}

/* ------------------------------------------------------- 13 · entrelacs --- */
/* Un seul angle de phase pilote deux choses : la position latérale (sinus) et
   la profondeur (cosinus). Les brins se croisent donc vraiment — celui qui
   passe devant est celui dont le cosinus est positif, et il repasse derrière
   un demi-tour plus loin. data-strand sur chaque brin. */

export function braid(stage, { length = 3800, turns = 1.35, anticipatePin, onUpdate } = {}) {
  const strands = [...stage.querySelectorAll('[data-strand]')];
  if (!strands.length) return;
  const n = strands.length;
  let last = 0;

  const layout = p => {
    last = p;
    const amp = (stage.getBoundingClientRect().width || innerWidth) * 0.19;
    let front = 0, best = -2;
    strands.forEach((s, i) => {
      const ph = (i / n) * Math.PI * 2 + p * turns * Math.PI * 2;
      const d = Math.cos(ph), k = (d + 1) / 2;
      if (d > best) { best = d; front = i; }
      s.style.transform = 'translate3d(' + (Math.sin(ph) * amp).toFixed(1) + 'px,' +
        (-d * 30).toFixed(1) + 'px,0) scale(' + (0.80 + 0.20 * k).toFixed(3) + ')';
      s.style.zIndex = String(100 + Math.round(d * 60));
      s.style.opacity = (0.30 + 0.70 * k).toFixed(3);
      s.style.filter = d < 0 ? 'blur(' + (-d * 3.2).toFixed(2) + 'px)' : 'none';
    });
    onUpdate && onUpdate(p, front);
  };

  pinned(stage, () => {}, { length, scrub: 0.35, anticipatePin, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 0.12 : 0);
  const onResize = () => layout(last);
  addEventListener('resize', onResize);
  return () => removeEventListener('resize', onResize);
}

/* ----------------------------------------------------------- 14 · repli --- */
/* La progression du scroll est repliée en onde triangulaire : passé la moitié,
   continuer à descendre rejoue la scène à l'envers. Le retour se signale par
   son propre sol et un reflet inversé — la page avance, le contenu recule. */

export function foldback(stage, { length = 4000, onUpdate } = {}) {
  const items = [...stage.querySelectorAll('[data-fold]')];
  const ground = stage.querySelector('[data-fold-ground]');
  const ghost = stage.querySelector('[data-fold-ghost]');

  const layout = p => {
    const back = p > 0.5;
    const t = back ? (1 - p) * 2 : p * 2;
    items.forEach(el => {
      const at = parseFloat(el.dataset.fold) || 0;
      const win = parseFloat(el.dataset.foldWin || '0.24');
      const d = (t - at) / win;
      const k = clamp01(1 - Math.abs(d));
      el.style.opacity = (k * k).toFixed(3);
      el.style.transform = 'translate3d(0,' + (-d * 110 * (back ? -1 : 1)).toFixed(1) + 'px,0)';
    });
    if (ground) ground.style.opacity = back ? Math.min(1, (p - 0.5) * 3).toFixed(3) : '0';
    if (ghost) ghost.style.opacity = back ? (0.06 + 0.20 * (1 - t)).toFixed(3) : '0';
    onUpdate && onUpdate(t, back, p);
  };

  pinned(stage, () => {}, { length, scrub: 0.3, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 0.5 : 0);
}

/* ------------------------------------------------ 15 · focale verticale --- */
/* Fisheye sur l'axe vertical : la hauteur de chaque bande est une gaussienne
   de sa distance au point de lecture, normalisée pour que la somme reste
   exactement la hauteur du cadre — aucune boucle de rétroaction, la hauteur
   ne dépend que du scroll et jamais de la mise en page obtenue. */

export function fisheye(stage, { length = 3600, min = 44, sigma = 0.85, onUpdate } = {}) {
  const wrap = stage.querySelector('[data-bands]') || stage;
  const bands = [...stage.querySelectorAll('[data-band]')];
  if (!bands.length) return;
  const n = bands.length;
  let last = 0, floor = min;

  /* La bande comprimée doit encore montrer son titre en entier : le plancher
     est mesuré sur le plus grand en-tête rendu, pas fixé en dur. */
  const measure = () => {
    floor = min;
    bands.forEach(b => {
      const head = b.querySelector('[data-band-head]');
      if (!head) return;
      const box = head.parentElement || head;
      const cs = getComputedStyle(box);
      const pad = parseFloat(cs.paddingTop) + parseFloat(cs.paddingBottom);
      floor = Math.max(floor, Math.ceil(head.getBoundingClientRect().height + pad));
    });
  };

  const layout = p => {
    last = p;
    const H = wrap.getBoundingClientRect().height || innerHeight;
    const min = floor;
    const f = clamp01(p) * n;
    const ks = []; let sum = 0;
    for (let i = 0; i < n; i++) {
      const d = (i + 0.5 - f) / sigma;
      const k = Math.exp(-d * d);
      ks.push(k); sum += k;
    }
    const spare = Math.max(0, H - n * min);
    bands.forEach((b, i) => {
      const share = ks[i] / sum;
      b.style.height = (min + spare * share).toFixed(2) + 'px';
      const k = clamp01(ks[i] / (ks[0] === undefined ? 1 : Math.max.apply(null, ks)));
      const body = b.querySelector('[data-band-body]');
      if (body) body.style.opacity = clamp01((k - 0.30) / 0.45).toFixed(3);
      const head = b.querySelector('[data-band-head]');
      if (head) head.style.color = k > 0.5 ? '#e2ddd1' : 'rgba(226,221,209,.40)';
      b.style.background = 'rgba(145,132,217,' + (0.055 * k).toFixed(3) + ')';
    });
    onUpdate && onUpdate(p, Math.min(n - 1, Math.max(0, Math.round(f - 0.5))));
  };

  measure();
  pinned(stage, () => {}, { length, scrub: 0.28, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 0.5 : 0);
  const onResize = () => { measure(); layout(last); };
  addEventListener('resize', onResize);
  return () => removeEventListener('resize', onResize);
}

/* --------------------------------------------------------------- montage --- */
/* Un contexte GSAP par écran : ctx.revert() nettoie tweens + ScrollTriggers. */

export function mountScreen(root, systems) {
  const cleanups = [];
  const ctx = gsap.context(() => {
    systems.forEach(fn => { const off = fn(root); if (typeof off === 'function') cleanups.push(off); });
  }, root);
  ScrollTrigger.refresh();
  return {
    revert() {
      cleanups.forEach(off => { try { off(); } catch (e) { /* noop */ } });
      cleanups.length = 0;
      ctx.revert();
    }
  };
}

export default { boot, setMotion, reveals, converge, parallax, pinned, driven, snapPanels, infinite, splitChars, charsIn, counters, geoTransition, mountScreen, watchScroll, scrollTo, getScroll, getVelocity, reduced, orbit, magnet, corridor, deconstruct, fracture, morph, shapePoints, braid, foldback, fisheye };
