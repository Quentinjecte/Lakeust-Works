/* Entrée Vite de /welcome-cinematic.
   Ordre imposé : le custom element doit être défini avant que la timeline le cherche. */
import './welcome-blackhole.js';
import { createCinematic } from './welcome-cinematic.js';

const LABELS = {
  init: 'initialisation', reveal: 'révélation', approach: 'approche',
  attraction: 'attraction', transition: 'transition', title: 'arrivée'
};

/* <black-hole-stage> se déclare de façon asynchrone (import dynamique de three/gsap
   à l'intérieur de connectedCallback) — attendre que setShot existe plutôt que de
   supposer qu'il est prêt une fois le custom element défini. */
function waitForStage() {
  return new Promise((resolve, reject) => {
    const t0 = performance.now();
    const look = () => {
      const el = document.querySelector('black-hole-stage');
      if (el && typeof el.setShot === 'function') return resolve(el);
      if (performance.now() - t0 > 15000) return reject(new Error('stage timeout'));
      requestAnimationFrame(look);
    };
    look();
  });
}

function paintSkip(v) {
  const el = document.querySelector('[data-wc="skip"]');
  if (el) { el.style.opacity = String(v); el.style.pointerEvents = v ? 'auto' : 'none'; }
}

function paintPhase(p) {
  const el = document.querySelector('[data-wc="phaseName"]');
  if (el) el.textContent = LABELS[p] || p;
}

/* La télémétrie lit les valeurs de mise en scène du stage quatre fois par seconde —
   un nombre qui change n'a pas besoin d'un rendu par frame. */
function startReadout(stage) {
  const write = (k, v) => {
    const el = document.querySelector('[data-wc-num="' + k + '"]');
    if (el && el.textContent !== v) el.textContent = v;
  };
  return setInterval(() => {
    if (!stage.shot) return;
    const s = stage.shot();
    write('dist', s.dist.toFixed(1) + ' rs');
    write('lens', '×' + s.lens.toFixed(2));
    write('fov', s.fov.toFixed(0) + '°');
  }, 250);
}

async function boot() {
  const root = document.querySelector('[data-wc-root]') || document.body;
  const stage = await waitForStage();
  const studio = new URLSearchParams(location.search).has('studio');

  const cine = await createCinematic({
    stage, root, studio,
    onPhase: p => paintPhase(p),
    onEnd: () => paintSkip(0)
  });

  /* poignée console : __wcCine.seek(12) / .pause() / .replay() pour l'art direction */
  window.__wcCine = cine;
  startReadout(stage);

  document.querySelector('[data-wc="skip"]')?.addEventListener('click', () => { cine.skipToEnd(); paintSkip(0); });
  document.querySelector('[data-wc="replay"]')?.addEventListener('click', () => { paintSkip(0.5); cine.replay(); });

  cine.play();
}

if (document.readyState === 'loading') addEventListener('DOMContentLoaded', boot);
else boot();
