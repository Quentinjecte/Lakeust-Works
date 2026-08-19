/* Entrée Vite de /forest-cinematic.
   Ordre imposé : le custom element doit être défini avant que la timeline le cherche. */
import './forest-stage.js';
import { createCinematic } from './forest-cinematic.js';

const LABELS = {
  depart: 'départ', route: 'route', lacets: 'lacets',
  vallee: 'vallée', titre: 'titre'
};

/* Contrairement à <black-hole-stage>, <forest-stage> expose setShot dès
   connectedCallback (pas d'import dynamique de Three.js à attendre côté page) :
   il suffit d'attendre que l'élément soit dans le DOM. */
function waitForStage() {
  return new Promise((resolve, reject) => {
    const t0 = performance.now();
    const look = () => {
      const el = document.querySelector('forest-stage');
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

/* Télémétrie : la caméra n'a plus de coordonnées libres, elle est accrochée à la
   route (voir forest-stage.js). Altitude = lift (hauteur au-dessus du sol) directement
   depuis le shot ; cap et vitesse se lisent sur le déplacement du point de la route
   sous la caméra (stage.road.at(s)), quatre fois par seconde. */
function startReadout(stage) {
  const write = (k, v) => {
    const el = document.querySelector('[data-wc-num="' + k + '"]');
    if (el && el.textContent !== v) el.textContent = v;
  };
  let last = null, lastT = performance.now();
  return setInterval(() => {
    if (!stage.shot || !stage.road) return;
    const s = stage.shot();
    const here = stage.road.at(s.s);
    const now = performance.now();
    const dt = Math.max((now - lastT) / 1000, 0.001);
    let speed = 0, heading = 0;
    if (last) {
      const dx = here.x - last.x, dz = here.z - last.z;
      speed = Math.sqrt((here.x - last.x) ** 2 + (here.y - last.y) ** 2 + dz * dz) / dt;
      heading = ((Math.atan2(dx, -dz) * 180 / Math.PI) + 360) % 360;
    }
    last = here; lastT = now;
    write('alt', s.lift.toFixed(1) + ' m');
    write('speed', speed.toFixed(1) + ' m/s');
    write('heading', heading.toFixed(0) + '°');
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

  /* poignée console : __fcCine.seek(12) / .pause() / .replay() pour l'art direction */
  window.__fcCine = cine;
  startReadout(stage);

  document.querySelector('[data-wc="skip"]')?.addEventListener('click', () => { cine.skipToEnd(); paintSkip(0); });
  document.querySelector('[data-wc="replay"]')?.addEventListener('click', () => { paintSkip(0.5); cine.replay(); });

  cine.play();
}

if (document.readyState === 'loading') addEventListener('DOMContentLoaded', boot);
else boot();
