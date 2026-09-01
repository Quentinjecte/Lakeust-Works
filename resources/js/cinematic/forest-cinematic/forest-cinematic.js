/* forest-cinematic.js — Theatre.js staging for <forest-stage> (dirt_road_forest.glb).
   Same three-layer split as blackhole-cinematic.js:

     Three.js    forest-stage.js   → rendering (sky, model, sun, shadows, bloom, RAF)
     Theatre.js  this file         → timeline / staging
     the page    forest-cinematic-entry.js → orchestration

   The Theatre.js/subscribe-vs-poll/API plumbing lives in
   cinematic/shared/shot-driven-cinematic.js, shared with blackhole-cinematic.js.

   Usage
     const cine = await createCinematic({ stage, root, studio: false, onPhase, onEnd });
     cine.play();  cine.pause();  cine.replay();  cine.dispose();

   Vingt secondes sur la route forestière, en plein midi.

   La caméra ne vole plus librement : elle est accrochée à l'axe de la route (échantillonné
   dans forest-stage.js), et la timeline ne pousse qu'un point kilométrique — `s`, 0 à 1 du
   premier plan vers le fond de la vallée — plus la façon de le filmer : `lift` (hauteur au
   sol), `side` (déport dans la largeur du chemin), `ahead` (à quelle distance devant on
   regarde), `aim` (relèvement du regard), `fov`, `roll`. Le décor ne peut plus être quitté,
   et le mouvement se lit comme un trajet et non comme un survol.

   Le profil de vitesse porte la dramaturgie : démarrage lent sous les arbres (0–3 s),
   la route s'ouvre et on accélère (3–13 s), puis la caméra s'élève et ralentit au-dessus
   du dernier virage pendant que le titre monte (16,4–20 s).

   Côté lumière il ne se passe presque rien, et c'est voulu : à midi le soleil est haut,
   blanc, à peu près fixe. Ce qui bouge est l'exposition et le voile d'horizon, d'un cran.
   Tune with ?studio in the URL. */

import { createShotDrivenCinematic } from '../shared/shot-driven-cinematic.js';

export const LENGTH = 20;

/* Paste a studio export here to freeze the timeline; null = compile from SHOTS. */
const STATE_OVERRIDE = null;

/* ── the cinematic ──────────────────────────────────────────────────────────
   [time, value, easeToNext]. `s` is arc length along the road (0 = the near
   plateau at Y≈173, 1 = the valley floor at Y≈133, 368 units apart). */
const SHOTS = {
  Camera: {
    s: [[0, 0.040, 'out'], [0, 0.100, 'linear'], [8, 0.34, 'linear'], [16, 0.64, 'linear'], [19.4, 0.85, 'out'], [23, 0.95, 'linear']],
    lift: [[0, 15, 'out'], [3, 7.0, 'inOut'], [8, 8.0, 'inOut'], [13, 9.0, 'in'], [15, 9.6, 'in'], [16.4, 10.4, 'out'], [18, 11.2, 'out'], [20, 6.8, 'linear']],
    /* `side` n'est pas un ornement : c'est la trajectoire dans la largeur du chemin.
       Relevé sur le décor (distance aux troncs le long de l'axe), il évite le gros
       conifère planté au milieu du virage vers 8 s et le passage étroit vers 13 s. */
    side: [[0, 5, 'inOut'], [2.4, 2, 'inOut'], [4.7, -5, 'inOut'], [7.8, -7, 'inOut'], [9.8, 6, 'inOut'], [11.5, 4, 'inOut'], [12.7, -1, 'inOut'], [14, 4.5, 'inOut'], [15.6, 4, 'inOut'], [20, 0, 'linear']],
    ahead: [[0, 22, 'out'], [3, 26, 'inOut'], [8, 34, 'inOut'], [13, 40, 'out'], [16.4, 44, 'out'], [20, 48, 'linear']],
    aim: [[0, -1.6, 'inOut'], [8, -1.2, 'inOut'], [13, -0.8, 'out'], [16.4, -0.4, 'out'], [20, 0, 'linear']],
    fov: [[0, 44, 'out'], [3, 45, 'inOut'], [8, 48, 'inOut'], [13, 50, 'in'], [16.4, 46, 'out'], [20, 43, 'linear']],
    roll: [[0, 0, 'inOut'], [5.5, 0.012, 'inOut'], [10.5, -0.014, 'inOut'], [13.5, 0.010, 'out'], [16.4, 0, 'linear'], [20, 0, 'linear']],
    drift: [[0, 0.5, 'linear'], [13, 0.7, 'in'], [16.4, 0.4, 'out'], [20, 0.2, 'linear']]
  },
  Environment: {
    sunAz: [[0, 300, 'linear'], [20, 308, 'linear']],
    sunEl: [[0, 68, 'inOut'], [13, 72, 'inOut'], [20, 75, 'linear']],
    sunIntensity: [[0, 4.3, 'inOut'], [8, 4.6, 'in'], [15.6, 4.9, 'expoIn'], [16.4, 5.6, 'out'], [17.2, 5.0, 'linear'], [20, 5.0, 'linear']],
    sunWarmth: [[0, 0.14, 'inOut'], [13, 0.09, 'linear'], [20, 0.06, 'linear']],
    hemiIntensity: [[0, 1.85, 'inOut'], [13, 1.95, 'linear'], [20, 2.00, 'linear']],
    exposure: [[0, 1.02, 'out'], [3, 1.07, 'inOut'], [13, 1.12, 'in'], [15.6, 1.14, 'expoIn'], [16.25, 1.32, 'expoOut'], [17.2, 1.10, 'linear'], [20, 1.08, 'linear']],
    skyDepth: [[0, 1, 'linear'], [13, 0.97, 'linear'], [20, 0.94, 'linear']],
    haze: [[0, 0.30, 'inOut'], [13, 0.26, 'linear'], [20, 0.24, 'linear']],
    glow: [[0, 0.9, 'inOut'], [15.6, 1.0, 'in'], [16.25, 1.5, 'out'], [17.2, 1.1, 'linear'], [20, 1.1, 'linear']],
    air: [[0, 0.26, 'inOut'], [8, 0.22, 'linear'], [20, 0.18, 'linear']],
    bloom: [[0, 0.14, 'inOut'], [13, 0.18, 'in'], [16.25, 0.42, 'out'], [17.2, 0.20, 'linear'], [20, 0.20, 'linear']]
  },
  UI: {
    hud: [[0, 0, 'out'], [0.8, 0, 'linear'], [2.2, 0.5, 'linear'], [15.8, 0.5, 'in'], [16.4, 0, 'hold'], [17.6, 0, 'out'], [18.3, 0.5, 'linear'], [20, 0.5, 'linear']],
    signal: [[0, 0, 'out'], [1.2, 0, 'linear'], [2.4, 1, 'linear'], [3.6, 1, 'in'], [4.4, 0, 'hold']],
    space: [[0, 0, 'hold'], [4.6, 0, 'out'], [5.4, 1, 'linear'], [7.9, 1, 'in'], [8.7, 0, 'hold']],
    label: [[0, 0, 'hold'], [9.4, 0, 'out'], [10.2, 1, 'linear'], [13.2, 1, 'in'], [14, 0, 'hold']],
    readout: [[0, 0, 'hold'], [9, 0, 'out'], [9.8, 1, 'linear'], [15.4, 1, 'in'], [16, 0, 'hold']],
    title: [[0, 0, 'hold'], [17.2, 0, 'out'], [18.2, 1, 'linear'], [20, 1, 'linear']],
    enter: [[0, 0, 'hold'], [18.5, 0, 'out'], [19.3, 1, 'linear'], [20, 1, 'linear']]
  },
  Transition: {
    /* plus de coupe au noir : à midi la transition vers le titre est une trouée de
       lumière dans la canopée, pas un fondu. `curtain` reste câblé, à zéro. */
    flash: [[0, 0, 'hold'], [15.6, 0, 'expoIn'], [16.3, 0.55, 'expoOut'], [17.1, 0, 'hold']],
    curtain: [[0, 0, 'hold'], [20, 0, 'linear']]
  }
};

const PHASES = [
  [0, 'depart'], [3, 'route'], [8, 'lacets'], [13, 'vallee'], [16.4, 'titre']
];

const PROP_TYPES = {
  Camera: {
    s: [0, 1], lift: [0.5, 60], side: [-14, 14], ahead: [6, 120], aim: [-40, 60],
    fov: [24, 80], roll: [-0.4, 0.4], drift: [0, 1]
  },
  Environment: {
    sunAz: [0, 360], sunEl: [20, 90], sunIntensity: [0, 8], sunWarmth: [0, 1],
    hemiIntensity: [0, 3], exposure: [0, 2], skyDepth: [0, 1], haze: [0, 1],
    glow: [0, 3], air: [0, 1], bloom: [0, 1.6]
  },
  UI: { hud: [0, 1], signal: [0, 1], space: [0, 1], label: [0, 1], readout: [0, 1], title: [0, 1], enter: [0, 1] },
  Transition: { flash: [0, 1], curtain: [0, 1] }
};

let cached = { current: null };   // one project per document — re-entering must not build a second

export async function createCinematic(opts) {
  return createShotDrivenCinematic({
    SHOTS, PROP_TYPES, LENGTH, PHASES,
    projectName: 'Forest Cinematic', sheetName: 'Flythrough', idPrefix: 'fc',
    stageGroups: ['Camera', 'Environment'],
    logLabel: 'forest-cinematic',
    stateOverride: STATE_OVERRIDE,
    cacheRef: cached,
    ...opts
  });
}
