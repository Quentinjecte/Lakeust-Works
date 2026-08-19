/* lab.js — entrée Vite de /laboratoire, page autonome (pas de layout partagé,
   pas d'animation.js : seul motion.js est nécessaire ici).

   Une seule mécanique montée à la fois (M.mountScreen), pilotée par :
     - le sommaire à gauche (clic)
     - les touches 1-9, 0, I, flèches ← →
     - les boutons « Ouvrir » du catalogue
   Le passage d'une mécanique à l'autre se fait via M.geoTransition() (le
   flash hexagonal déjà fourni par motion.js) plutôt qu'un simple show/hide. */

import * as M from './portfolio/motion.js';

M.setMotion('full');

/* ------------------------------------------------------------- registre --- */
/* Index 0-14 = les quinze mécaniques, dans l'ordre du catalogue. Index 15 =
   Catalogue lui-même. ORDER fixe l'ordre de navigation au clavier (flèches) :
   Catalogue d'abord, puis les mécaniques dans l'ordre. */

const SLUGS = ['panels', 'depth', 'progress', 'story', 'flux', 'reveal', 'orbit', 'magnet',
               'corridor', 'deconstruct', 'fracture', 'morph', 'braid', 'foldback', 'fisheye', 'catalogue'];

const LABS = [
  'Panneaux · scroll snap', 'Profondeur · parallaxe', 'Progression · scroll-driven', 'Récit · section épinglée',
  'Flux · scroll infini', 'Révélation · masques', 'Chambre orbitale · contrôleur orbital', 'Champ polarisé · vitesse et signe',
  'Corridor · traversée spatiale', 'Démontage · déconstruction', 'Quadrants · lois contrariées', 'Morphogenèse · interpolation de formes',
  'Entrelacs · tressage en profondeur', 'Repli · onde triangulaire', 'Focale verticale · fisheye de lecture', 'Catalogue · quinze mécaniques'
];

const ORDER = [15, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
const PANELS = ['Orbite', 'Silence', 'Densité', 'Seuil'];

const STORY = [
  { t: 'Repérage', b: 'On relève les contraintes réelles avant de dessiner : supports, contenus, volumes, délais.', l: ['Audit des pages existantes', 'Inventaire des composants', "Budget d'animation"] },
  { t: 'Structure', b: 'La grille et les rythmes de section sont fixés une fois, puis réutilisés partout.', l: ['Colonnes et gouttières', 'Échelle typographique', 'Rythme vertical'] },
  { t: 'Matière', b: 'Lumière, masques et vitesses différentielles donnent la profondeur sans 3D.', l: ['Masques géométriques', 'Parallaxe multi-couches', 'Transitions liées au scroll'] },
  { t: 'Livraison', b: 'Chaque système est isolé pour être remonté sur une nouvelle page sans copie.', l: ['Contexte GSAP par écran', 'Reveals déclaratifs', 'Un seul contrôleur de scroll'] }
];

const FLUX = [
  { t: 'Champ magnétique', m: 'Identité · Web' }, { t: 'Table rase', m: 'Direction artistique' },
  { t: 'Ligne de fuite', m: 'Éditorial' }, { t: 'Vitesse limite', m: 'Produit · Interface' },
  { t: 'Halo', m: 'Motion' }, { t: 'Pierre calcaire', m: 'Identité' },
  { t: 'Second souffle', m: 'Web · WebGL' }, { t: 'Angle mort', m: 'Recherche' }
];

/* ---------------------------------------------------------------- état --- */

const root = document.querySelector('[data-lab-root]');
const railIndicator = document.querySelector('[data-rail-indicator]');
const railButtons = [...document.querySelectorAll('[data-rail]')];
const progressBar = document.querySelector('[data-lab-progress-bar]');
const panelLabel = document.querySelector('[data-panel-label]');
const labTitleEl = document.querySelector('[data-lab-title]');
const labCountEl = document.querySelector('[data-lab-count]');

let current = 15;
let busy = false;
let ctx = null;

function label(i) { return i === 15 ? 'IX' : String(i + 1).padStart(2, '0'); }

function setRail() {
  const pos = Math.max(0, ORDER.indexOf(current));
  if (railIndicator) railIndicator.style.transform = 'translateY(' + pos * 26 + 'px)';
  railButtons.forEach((b, i) => {
    b.style.color = i === pos ? '#e2ddd1' : 'rgba(226,221,209,.40)';
  });
  if (labTitleEl) labTitleEl.textContent = LABS[current];
  if (labCountEl) labCountEl.textContent = label(current) + ' / 15';
}

function chars(sec) {
  sec.querySelectorAll('[data-chars]').forEach(el => M.charsIn(el, { delay: 0.1 }));
}

const SETUPS = [
  setupPanels, setupDepth, setupDriven, setupStory, setupFlux, setupMasks,
  setupOrbit, setupMagnet, setupCorridor, setupDecon, setupFracture, setupMorph,
  setupBraid, setupFold, setupFocal, () => {}
];

function mount() {
  const sec = document.querySelector('.lab-sec[data-lab="' + SLUGS[current] + '"]');
  if (!sec || !root) return;
  if (ctx) ctx.revert();
  document.querySelectorAll('.lab-sec').forEach(s => s.classList.toggle('is-active', s === sec));
  M.scrollTo(0, { immediate: true });

  const extra = SETUPS[current] || (() => {});
  ctx = M.mountScreen(sec, [
    r => M.reveals(r), r => M.converge(r), r => M.parallax(r), r => M.counters(r), chars,
    r => extra(r, M)
  ]);
}

function go(i) {
  if (i === current || busy || !SLUGS[i]) return;
  busy = true;
  M.geoTransition(() => { current = i; setRail(); mount(); }).then(() => { busy = false; });
}

/* ------------------------------------------------------------- montage --- */

M.boot().then(() => {
  M.watchScroll(() => {
    if (!progressBar) return;
    const max = document.documentElement.scrollHeight - innerHeight;
    progressBar.style.transform = 'scaleX(' + (max > 0 ? Math.min(1, window.scrollY / max) : 0) + ')';
  });

  addEventListener('keydown', e => {
    if (e.metaKey || e.ctrlKey || e.altKey) return;
    const n = parseInt(e.key, 10);
    if (n >= 1 && n <= 9) return go(n - 1);
    if (e.key === '0') return go(9);
    if (e.key.toLowerCase() === 'i') return go(15);
    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
      const pos = ORDER.indexOf(current);
      const next = ORDER[pos + (e.key === 'ArrowRight' ? 1 : -1)];
      if (next !== undefined) { e.preventDefault(); go(next); }
    }
  });

  railButtons.forEach((b, i) => {
    b.addEventListener('click', () => go(ORDER[i]));
  });

  document.querySelectorAll('[data-cta]').forEach(b => {
    const target = parseInt(b.dataset.cta, 10);
    if (isFinite(target)) b.addEventListener('click', () => go(target));
  });

  setRail();
  mount();
});

/* ------------------------------------------------------ 01 · panneaux --- */

function setupPanels(sec, M) {
  const wrap = sec.querySelector('[data-panels]');
  if (!wrap) return;
  const dots = [...sec.querySelectorAll('[data-panel-dots] span')];
  return M.snapPanels(wrap, {
    onChange: i => {
      dots.forEach((d, k) => {
        d.style.width = k === i ? '30px' : '16px';
        d.style.background = k === i ? '#9184d9' : 'rgba(233,233,237,.22)';
      });
      if (panelLabel) panelLabel.textContent = PANELS[i] || '';
    }
  });
}

/* ----------------------------------------------------- 02 · profondeur --- */

function setupDepth(sec, M) {
  const scope = sec.querySelector('[data-parallax-scope]');
  if (!scope || M.reduced()) return;
  M.gsap.fromTo(scope.lastElementChild, { opacity: 0 }, {
    opacity: 1, ease: 'none',
    scrollTrigger: { trigger: scope, start: 'center center', end: 'bottom top', scrub: true }
  });
}

/* ---------------------------------------------------- 03 · progression --- */

function setupDriven(sec, M) {
  const stage = sec.querySelector('[data-driven]');
  if (!stage) return;
  const shape = stage.querySelector('[data-driven-shape]');
  const ring = stage.querySelector('[data-driven-ring]');
  const bg = stage.querySelector('[data-driven-bg]');
  const bar = stage.querySelector('[data-driven-bar]');
  const out = stage.querySelector('[data-driven-readout]');
  const steps = [...stage.querySelectorAll('[data-driven-step]')];

  M.pinned(stage, tl => {
    tl.fromTo(shape, { xPercent: -190, yPercent: 40, rotate: -60, scale: .5, opacity: 0 },
                     { xPercent: -80, yPercent: 0, rotate: -20, scale: .8, opacity: 1, duration: 1 })
      .to(shape, { xPercent: 0, rotate: 0, scale: 1, duration: 1 })
      .to(shape, { rotate: 120, scale: 1.5, borderRadius: '50%', duration: 1 }, '>')
      .to(shape, { rotate: 180, scale: 1.1, xPercent: 0, duration: 1 })
      .fromTo(ring, { scale: .4, opacity: 0, rotate: 0 }, { scale: 1, opacity: 1, rotate: 90, duration: 4 }, 0)
      .fromTo(bg, { opacity: .2 }, { opacity: 1, duration: 4 }, 0);
  }, {
    length: 2600,
    onUpdate: st => {
      const p = st.progress;
      if (out) out.textContent = p.toFixed(3);
      if (bar) bar.style.transform = 'scaleX(' + p + ')';
      steps.forEach((s, i) => {
        const on = p >= i * 0.25 - 0.02;
        s.style.color = on ? '#e2ddd1' : 'rgba(226,221,209,.30)';
      });
      if (shape) shape.style.filter = 'drop-shadow(0 0 ' + (10 + p * 40) + 'px rgba(145,132,217,' + (0.2 + p * 0.4) + '))';
    }
  });

  if (M.reduced()) { if (out) out.textContent = '1.000'; if (bar) bar.style.transform = 'scaleX(1)'; }
}

/* --------------------------------------------------------- 04 · récit --- */

function setupStory(sec, M) {
  const stage = sec.querySelector('[data-story]');
  if (!stage) return;
  const g = M.gsap;
  const idx = stage.querySelector('[data-story-index]');
  const title = stage.querySelector('[data-story-title]');
  const body = stage.querySelector('[data-story-body]');
  const bullets = [...stage.querySelectorAll('[data-story-bullet]')];
  const plates = [...stage.querySelectorAll('[data-story-plate]')];
  const mark = stage.querySelector('[data-story-mark]');
  const ticks = [...stage.querySelectorAll('[data-story-tick]')];
  const bg = stage.querySelector('[data-story-bg]');
  let cur = -1;

  const paint = i => {
    if (i === cur) return;
    const prev = cur; cur = i;
    const s = STORY[i];
    if (idx) idx.textContent = 'Étape 0' + (i + 1) + ' / 04';
    const dir = i > prev ? 1 : -1;
    if (!M.reduced()) {
      g.fromTo(title, { yPercent: 40 * dir, opacity: 0 }, { yPercent: 0, opacity: 1, duration: .5, ease: 'power3.out' });
      g.fromTo(body, { y: 14 * dir, opacity: 0 }, { y: 0, opacity: 1, duration: .5, delay: .04 });
      g.fromTo(bullets, { x: 18, opacity: 0 }, { x: 0, opacity: 1, duration: .45, stagger: .05 });
    }
    title.textContent = s.t;
    body.textContent = s.b;
    bullets.forEach((b, k) => { b.lastChild.textContent = s.l[k] || ''; });
    plates.forEach((p, k) => g.to(p, { opacity: k === i ? 1 : 0, duration: .6, ease: 'none' }));
    ticks.forEach((t, k) => { t.style.background = k <= i ? '#9184d9' : 'rgba(233,233,237,.14)'; });
  };

  M.pinned(stage, tl => {
    tl.to(bg, { backgroundPosition: '100% 100%', duration: 4, ease: 'none' }, 0)
      .fromTo(mark, { rotate: 0, scale: .9 }, { rotate: 180, scale: 1.15, duration: 4, ease: 'none' }, 0)
      .fromTo(bg, { opacity: .5 }, { opacity: 1, duration: 4, ease: 'none' }, 0);
  }, {
    length: 3200, scrub: 0.4,
    onUpdate: st => paint(Math.min(3, Math.floor(st.progress * 3.999)))
  });

  paint(0);
}

/* --------------------------------------------------------- 05 · flux --- */

function setupFlux(sec, M) {
  const host = sec.querySelector('[data-flux]');
  if (!host) return;
  const nodesOut = sec.querySelector('[data-flux-nodes]');
  const idxOut = sec.querySelector('[data-flux-index]');
  let peak = 0;

  const list = M.infinite(host, {
    itemH: 150, gap: 14, initial: 44,
    render: (node, i) => {
      const s = FLUX[i % FLUX.length];
      const year = 2019 + (i % 7);
      node.innerHTML = '';
      node.style.cssText += 'display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:clamp(16px,4vw,56px);padding:0 clamp(4px,2vw,24px);border-top:1px solid rgba(233,233,237,.08);background:' + (i % 2 ? 'rgba(233,233,237,.012)' : 'transparent') + ';';
      const num = document.createElement('span');
      num.textContent = String(i + 1).padStart(3, '0');
      num.style.cssText = 'font-size:11px;letter-spacing:.22em;color:rgba(226,221,209,.34);font-variant-numeric:tabular-nums';
      const mid = document.createElement('span');
      mid.style.cssText = 'display:flex;flex-direction:column;gap:8px;min-width:0';
      const t = document.createElement('span');
      t.textContent = s.t;
      t.style.cssText = 'font-size:clamp(20px,2.6vw,34px);font-weight:200;letter-spacing:-.02em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis';
      const m = document.createElement('span');
      m.textContent = s.m + ' · ' + year;
      m.style.cssText = 'font-size:11px;letter-spacing:.20em;text-transform:uppercase;color:rgba(226,221,209,.34)';
      mid.append(t, m);
      const plate = document.createElement('span');
      plate.style.cssText = 'width:clamp(56px,9vw,132px);aspect-ratio:16/10;border:1px solid rgba(233,233,237,.12);background:linear-gradient(' + (120 + i * 17 % 180) + 'deg,#12131d,#05050a 60%,#191a33);clip-path:polygon(8% 0,100% 0,92% 100%,0 100%)';
      node.append(num, mid, plate);
      if (idxOut) { peak = Math.max(peak, i + 1); idxOut.textContent = peak; }
    }
  });

  if (nodesOut) nodesOut.textContent = host.children.length;
  return list;
}

/* ---------------------------------------------------- 06 · révélation --- */

function setupMasks(sec, M) {
  const g = M.gsap;
  const sc = (trigger, vars) => ({ trigger, start: 'top 80%', end: 'bottom bottom', scrub: 0.5, ...vars });

  const hex = sec.querySelector('[data-mask-hex]');
  if (hex) g.fromTo(hex.querySelector('[data-mask-hex-in]'),
    { clipPath: 'polygon(50% 50%,50% 50%,50% 50%,50% 50%,50% 50%,50% 50%)' },
    { clipPath: 'polygon(0% -50%,100% -50%,150% 50%,100% 150%,0% 150%,-50% 50%)', ease: 'none', scrollTrigger: sc(hex) });

  const split = sec.querySelector('[data-mask-split]');
  if (split) {
    g.fromTo(split.querySelector('[data-mask-left]'), { xPercent: 0 }, { xPercent: -100, ease: 'none', scrollTrigger: sc(split, { start: 'top 70%' }) });
    g.fromTo(split.querySelector('[data-mask-right]'), { xPercent: 0 }, { xPercent: 100, ease: 'none', scrollTrigger: sc(split, { start: 'top 70%' }) });
  }

  const line = sec.querySelector('[data-mask-line]');
  if (line) {
    const tl = g.timeline({ scrollTrigger: sc(line, { start: 'top 75%', end: 'bottom 60%' }) });
    tl.to(line.querySelector('[data-mask-line-rule]'), { scaleX: 1, duration: 1, ease: 'none' })
      .fromTo(line.querySelector('[data-mask-line-top]'), { y: 40, opacity: .2 }, { y: 0, opacity: 1, duration: 1, ease: 'none' }, 0)
      .fromTo(line.querySelector('[data-mask-line-bot]'), { y: -40, opacity: .2 }, { y: 0, opacity: 1, duration: 1, ease: 'none' }, 0);
  }

  const img = sec.querySelector('[data-mask-img]');
  if (img) g.fromTo(img.querySelector('[data-mask-img-in]'),
    { clipPath: 'inset(100% 0% 0% 0%)', scale: 1.14 },
    { clipPath: 'inset(0% 0% 0% 0%)', scale: 1, ease: 'none', scrollTrigger: sc(img, { start: 'top 75%', end: 'bottom 70%' }) });

  if (M.reduced()) {
    sec.querySelectorAll('[data-mask-hex-in],[data-mask-img-in]').forEach(el => { el.style.clipPath = 'none'; });
    sec.querySelectorAll('[data-mask-left],[data-mask-right]').forEach(el => { el.style.display = 'none'; });
  }
}

/* -------------------------------------------------------- 07 · orbite --- */

function setupOrbit(sec, M) {
  const stage = sec.querySelector('[data-orbit]');
  if (!stage) return;
  const ph = stage.querySelector('[data-orbit-phase]');
  const val = stage.querySelector('[data-orbit-val]');
  return M.orbit(stage, {
    onUpdate: p => {
      if (val) val.textContent = p.toFixed(3);
      if (ph) ph.textContent = p < 0.22 ? 'Approche' : p < 0.54 ? 'Bascule du plan' : p < 0.74 ? 'Rotation' : 'Effondrement';
    }
  });
}

/* --------------------------------------------------- 08 · magnétique --- */

function setupMagnet(sec, M) {
  const stage = sec.querySelector('[data-magnet]');
  if (!stage) return;
  const pol = stage.querySelector('[data-mag-pol]');
  const vv = stage.querySelector('[data-mag-v]');
  const amp = stage.querySelector('[data-mag-amp]');
  return M.magnet(stage, {
    onUpdate: s => {
      if (vv) vv.textContent = s.v.toFixed(1);
      if (pol) {
        pol.textContent = s.pol > 0 ? 'Attraction' : 'Répulsion';
        pol.style.color = s.pol > 0 ? '#9184d9' : 'rgba(226,221,209,.72)';
      }
      if (amp) amp.style.transform = 'scaleX(' + s.amp.toFixed(3) + ')';
    }
  });
}

/* ----------------------------------------------------- 09 · corridor --- */

function setupCorridor(sec, M) {
  const stage = sec.querySelector('[data-corridor-stage]');
  if (!stage) return;
  const z = stage.querySelector('[data-corridor-z]');
  return M.corridor(stage, { onUpdate: (p, cam) => { if (z) z.textContent = Math.round(cam); } });
}

/* ------------------------------------------------- 10 · déconstruction --- */

function setupDecon(sec, M) {
  const stage = sec.querySelector('[data-decon]');
  if (!stage) return;
  const ph = stage.querySelector('[data-decon-phase]');
  const val = stage.querySelector('[data-decon-val]');
  M.deconstruct(stage, {
    onUpdate: p => {
      if (val) val.textContent = p.toFixed(2);
      if (ph) ph.textContent = p < 0.42 ? 'Démontage' : p < 0.6 ? 'Transfert' : 'Reconstruction';
    }
  });
}

/* ------------------------------------------------------ 11 · quadrants --- */

function setupFracture(sec, M) {
  const stage = sec.querySelector('[data-fracture]');
  if (stage) M.fracture(stage);
}

/* --------------------------------------------------- 12 · morphogenèse --- */

function setupMorph(sec, M) {
  const stage = sec.querySelector('[data-morphstage]');
  if (!stage) return;
  const val = stage.querySelector('[data-morph-val]');
  return M.morph(stage, {
    shapes: ['rect', 'hex', 'circle', 'rect'],
    onUpdate: p => { if (val) val.textContent = p.toFixed(3); }
  });
}

/* ------------------------------------------------------- 13 · entrelacs --- */

function setupBraid(sec, M) {
  const stage = sec.querySelector('[data-braid]');
  if (!stage) return;
  const names = ['Champ magnétique', 'Ligne de fuite', 'Halo'];
  const front = stage.querySelector('[data-braid-front]');
  const val = stage.querySelector('[data-braid-val]');
  return M.braid(stage, {
    onUpdate: (p, i) => {
      if (val) val.textContent = p.toFixed(3);
      if (front) front.textContent = names[i] || '';
    }
  });
}

/* ----------------------------------------------------------- 14 · repli --- */

function setupFold(sec, M) {
  const stage = sec.querySelector('[data-fold-stage]');
  if (!stage) return;
  const dir = stage.querySelector('[data-fold-dir]');
  const val = stage.querySelector('[data-fold-val]');
  return M.foldback(stage, {
    onUpdate: (t, back) => {
      if (val) val.textContent = t.toFixed(2);
      if (dir) {
        dir.textContent = back ? 'Retour' : 'Aller';
        dir.style.color = back ? '#b3a9e6' : '#9184d9';
      }
    }
  });
}

/* ------------------------------------------------ 15 · focale verticale --- */

function setupFocal(sec, M) {
  const stage = sec.querySelector('[data-focal]');
  if (!stage) return;
  const val = stage.querySelector('[data-focal-val]');
  const name = stage.querySelector('[data-focal-name]');
  const heads = [...stage.querySelectorAll('[data-band-head]')];
  return M.fisheye(stage, {
    onUpdate: (p, i) => {
      if (val) val.textContent = String(i + 1).padStart(2, '0');
      if (name && heads[i]) name.textContent = heads[i].lastChild.textContent.trim();
    }
  });
}
