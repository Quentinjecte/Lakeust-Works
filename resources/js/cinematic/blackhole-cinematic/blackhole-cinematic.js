/* blackhole-cinematic.js — Theatre.js staging for the existing <black-hole-stage>.
   Nothing here renders: Three.js owns the scene, the shaders and the render loop.
   This module owns the *timeline* — camera, FOV, effect intensities, UI cues, the
   flash/blackout transition — and pushes values into the stage through setShot().
   The actual Theatre.js/subscribe-vs-poll/API plumbing lives in
   cinematic/shared/shot-driven-cinematic.js, shared with forest-cinematic.js.

   Boundaries
     Three.js  <black-hole-stage drive="external">  → rendering
     Theatre.js  this file                          → timeline / staging
     the Welcome cinematic page                     → orchestration

   Usage
     const cine = await createCinematic({ stage, root, studio: false, onPhase, onEnd });
     cine.play();  cine.pause();  cine.replay();  cine.dispose();

   The sequence is authored in SHOTS below and compiled into a real Theatre save file,
   so every keyframe is visible and draggable in the studio UI (?studio in the URL, or
   studio:true). Once you like your edits: studio's ⋮ → "Export Project" gives a JSON —
   drop it in as STATE_OVERRIDE and it takes precedence over SHOTS. */

import { createShotDrivenCinematic } from '../shared/shot-driven-cinematic.js';

export const LENGTH = 27;

/* Paste a studio export here to freeze the timeline; null = compile from SHOTS. */
const STATE_OVERRIDE = null;

/* ── the cinematic ────────────────────────────────────────────────────────────
   [time, value, easeToNext]. Two keyframes 0.02s apart = a hard cut.            */
const SHOTS = {
  Camera: {
    /* 320 units out the hole is a speck; 1.15 is inside the photon sphere */
    dist: [[0, 320, 'inOut'], [4, 210, 'out'], [9, 92, 'in'], [16, 26, 'expoIn'],
           [22, 4.2, 'expoIn'], [24.3, 1.15, 'hold'], [24.32, 30, 'out'], [27, 24, 'linear']],
    azim: [[0, 0.30, 'inOut'], [9, 0.42, 'in'], [16, 0.86, 'in'], [22, 1.5, 'out'],
           [24.32, 0.34, 'linear'], [27, 0.46, 'linear']],
    elev: [[0, 0.34, 'inOut'], [9, 0.30, 'inOut'], [16, 0.18, 'in'], [22, 0.06, 'out'],
           [24.32, 0.42, 'out'], [27, 0.34, 'linear']],
    fov: [[0, 54, 'out'], [9, 58, 'in'], [16, 72, 'expoIn'], [22, 86, 'out'],
          [24.3, 92, 'hold'], [24.32, 60, 'linear']],
    roll: [[0, 0, 'hold'], [16, 0, 'in'], [22, 0.10, 'out'], [24.3, 0.16, 'hold'], [24.32, 0, 'linear']],
    parallax: [[0, 1, 'linear'], [16, 0.6, 'linear'], [22, 0.15, 'hold'], [24.32, 0.8, 'linear']]
  },
  Effects: {
    fade: [[0, 0, 'out'], [2.2, 1, 'linear']],
    exposure: [[0, 0.06, 'out'], [4, 0.34, 'inOut'], [9, 0.58, 'linear'], [16, 0.72, 'linear'],
               [22, 0.78, 'expoIn'], [23.2, 3.2, 'expoIn'], [24.2, 0, 'hold'],
               [24.6, 0, 'out'], [25.6, 0.46, 'linear']],
    bgFade: [[0, 0.10, 'out'], [4, 0.55, 'out'], [9, 1.0, 'linear'], [20, 0.9, 'in'],
             [23.4, 0.02, 'hold'], [24.32, 0.55, 'linear']],
    diskBright: [[0, 0.10, 'out'], [4, 0.26, 'inOut'], [9, 0.44, 'in'], [16, 0.66, 'in'],
                 [22, 0.86, 'in'], [24.3, 1.05, 'hold'], [24.32, 0.52, 'linear']],
    diskThick: [[0, 0.55, 'linear'], [16, 0.72, 'linear'], [22, 0.98, 'hold'], [24.32, 0.62, 'linear']],
    diskMul: [[0, 1, 'hold'], [24.3, 1, 'hold'], [24.32, 1, 'out'], [26, 0.16, 'linear']],
    spin: [[0, 0.9, 'linear'], [9, 1.15, 'in'], [16, 1.9, 'in'], [22, 2.7, 'hold'],
           [24.32, 1.0, 'linear']],
    lens: [[0, 0.82, 'linear'], [16, 1.05, 'in'], [22, 1.62, 'hold'], [24.32, 0.86, 'linear']],
    aberr: [[0, 0.25, 'out'], [9, 0.55, 'in'], [16, 1.2, 'in'], [22, 2.2, 'hold'],
            [24.32, 0.45, 'linear']],
    vignette: [[0, 0.92, 'out'], [4, 0.74, 'linear'], [16, 0.86, 'in'], [22, 0.96, 'hold'],
               [24.32, 0.78, 'linear']],
    grain: [[0, 0.020, 'linear'], [9, 0.012, 'linear'], [22, 0.030, 'hold'], [24.32, 0.010, 'linear']],
    bloom: [[0, 1.1, 'out'], [9, 1.7, 'in'], [22, 2.0, 'hold'], [24.32, 1.5, 'linear']]
  },
  UI: {
    /* corner chrome */
    hud: [[0, 0, 'out'], [1.4, 0.5, 'linear'], [20, 0.5, 'in'], [22.4, 0, 'hold'],
          [24.32, 0, 'out'], [25.8, 0.5, 'linear']],
    signal: [[0, 0, 'out'], [1.0, 1, 'linear'], [3.4, 1, 'in'], [4.6, 0, 'hold']],
    space: [[0, 0, 'hold'], [4.6, 0, 'out'], [5.4, 1, 'linear'], [8.2, 1, 'in'], [9.2, 0, 'hold']],
    label: [[0, 0, 'hold'], [9.4, 0, 'out'], [10.4, 1, 'linear'], [15.2, 1, 'in'], [16.4, 0, 'hold']],
    readout: [[0, 0, 'hold'], [9.0, 0, 'out'], [10.0, 1, 'linear'], [21.4, 1, 'in'], [22.6, 0, 'hold']],
    title: [[0, 0, 'hold'], [24.6, 0, 'out'], [25.4, 1, 'linear']],
    enter: [[0, 0, 'hold'], [25.8, 0, 'out'], [26.6, 1, 'linear']]
  },
  Transition: {
    flash: [[0, 0, 'hold'], [23.0, 0, 'expoIn'], [23.7, 1, 'expoOut'], [24.25, 0, 'hold']],
    curtain: [[0, 0, 'hold'], [24.2, 0, 'linear'], [24.3, 1, 'linear'], [24.9, 0, 'hold']]
  }
};

const PHASES = [
  [0, 'init'], [4, 'reveal'], [9, 'approach'], [16, 'attraction'], [22.6, 'transition'], [24.5, 'title']
];

const PROP_TYPES = {
  Camera: { dist: [1.02, 400], azim: [-6.5, 6.5], elev: [-1.5, 1.5], fov: [18, 120], roll: [-1.5, 1.5], parallax: [0, 2] },
  Effects: { fade: [0, 1], exposure: [0, 6], bgFade: [0, 1], diskBright: [0, 3], diskThick: [0, 2], diskMul: [0, 2], spin: [0, 4], lens: [0.4, 2.4], aberr: [0, 5], vignette: [0, 1], grain: [0, 0.1], bloom: [0, 4] },
  UI: { hud: [0, 1], signal: [0, 1], space: [0, 1], label: [0, 1], readout: [0, 1], title: [0, 1], enter: [0, 1] },
  Transition: { flash: [0, 1], curtain: [0, 1] }
};

let cached = { current: null };   // one project per document — re-entering must not build a second

export async function createCinematic(opts) {
  return createShotDrivenCinematic({
    SHOTS, PROP_TYPES, LENGTH, PHASES,
    projectName: 'Welcome Cinematic', sheetName: 'Approach', idPrefix: 'wc',
    stageGroups: ['Camera', 'Effects'],
    logLabel: 'welcome-cinematic',
    stateOverride: STATE_OVERRIDE,
    cacheRef: cached,
    ...opts
  });
}
