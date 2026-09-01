/* Entrée Vite de /blackhole-cinematic.
   Ordre imposé : le custom element doit être défini avant que la timeline le cherche. */
import '../../three/blackhole.js';
import { createCinematic } from './blackhole-cinematic.js';
import { waitForStage as waitForStageEl } from '../shared/theatre-timeline.js';
import { bootCinematicPage } from '../shared/boot-cinematic-page.js';

const LABELS = {
  init: 'initialisation', reveal: 'révélation', approach: 'approche',
  attraction: 'attraction', transition: 'transition', title: 'arrivée'
};

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

bootCinematicPage({
  /* <black-hole-stage> se déclare de façon asynchrone (import dynamique de
     three/gsap à l'intérieur de connectedCallback) — attendre que setShot
     existe plutôt que de supposer qu'il est prêt une fois le custom
     element défini. */
  waitForStage: () => waitForStageEl('black-hole-stage'),
  createCinematic,
  labels: LABELS,
  startReadout,
  windowHandle: '__wcCine'
});
