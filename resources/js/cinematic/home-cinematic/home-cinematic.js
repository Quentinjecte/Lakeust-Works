/* orbital-cinematic.js — mise en scène Theatre.js pour <orbital-stage>.
   Rien ne se rend ici : Three.js possède la scène, les shaders et la boucle.
   Ce fichier possède le *temps* — caméra, FOV, ciel, nuages, ville, cues UI — et
   pousse des valeurs dans le stage par setShot().

     Three.js    <orbital-stage drive="external">   → rendu
     Theatre.js  ce fichier                         → timeline / mise en scène
     la page     orbital-cinematic.blade.php        → orchestration

   Tous les timings vivent dans T. Changer CLOUD_SECS retime toute la fin de la
   séquence : c'est le seul nombre à toucher pour allonger ou raccourcir la
   traversée des nuages. Les valeurs sont compilées en vrai fichier de sauvegarde
   Theatre, donc chaque keyframe reste visible et déplaçable dans l'éditeur (?studio).

   Si @theatre/core n'est pas disponible, la séquence tourne sur une horloge interne
   avec les mêmes nombres et les mêmes easings : la page ne tombe jamais en panne. */

import { createIdFactory, compileState as compileTimelineState, sampleTable as sampleShotsTable, waitForStage as waitForStageEl } from '../shared/theatre-timeline.js';


/* ── durée de la traversée des nuages : le paramètre demandé ─────────────────
   4 s = la vue est bouchée de 35.5 s à 39.5 s. Tout ce qui suit se décale.     */
export const CLOUD_SECS = 4;

const T = {
  start: 0,
  refEnd: 26,        // fin du plan repris de la référence
  dive: 29.5,        // le limbe s'ouvre, le rim atmosphérique s'allume
  atmo: 33,          // haute atmosphère : brume, traînées, étoiles lavées
  cloudIn: 35.5      // entrée dans la couche
};
T.cloudOut = T.cloudIn + CLOUD_SECS;          // sortie de la couche
T.swap = T.cloudIn + CLOUD_SECS * 0.38;       // bascule espace → sol, sous le voile
T.reveal = T.cloudOut;                        // la ville se découvre
T.wide = T.cloudOut + 3.4;                    // cadrage large
T.end = T.cloudOut + 6.5;                     // fin, Three.js reprend la main

export const LENGTH = T.end;
export const TIMES = T;

/* Coller ici un export studio pour figer le montage ; null = compilé depuis SHOTS. */
const STATE_OVERRIDE = null;

/* ── la cinématique ───────────────────────────────────────────────────────────
   [temps, valeur, easing vers la suivante]. Deux keyframes à 0.02 s = une coupe.

   La courbe de `dist` est relevée sur Reference.mp4 image par image : le diamètre
   apparent de la planète passe de 3 % à 41 % de la hauteur du cadre, en
   décélérant — ce n'est pas une approche linéaire, c'est un freinage continu.
   Toute la pièce garde cette signature : elle décélère aussi dans la ville.     */
const SHOTS = {
  Camera: {
    world: [[0, 0, 'hold'], [T.swap - 0.02, 0, 'hold'], [T.swap, 1, 'hold']],
    dist: [[0, 10050, 'linear'], [2, 7600, 'linear'], [4, 4900, 'linear'], [6, 2900, 'linear'],
           [8, 1900, 'linear'], [11, 1330, 'linear'], [14, 1030, 'linear'], [17.5, 880, 'linear'],
           [21, 790, 'linear'], [T.refEnd, 723, 'in'], [T.dive, 300, 'linear'],
           [T.atmo, 150, 'out'], [T.cloudIn, 104, 'hold']],
    azim: [[0, 0, 'linear'], [T.refEnd, 0.05, 'in'], [T.atmo, 0.14, 'linear'], [T.cloudIn, 0.17, 'hold']],
    elev: [[0, 0.02, 'linear'], [T.refEnd, 0.015, 'linear'], [T.cloudIn, 0.0, 'hold']],
    fov: [[0, 38, 'hold'], [T.refEnd, 38, 'in'], [T.dive, 44, 'linear'], [T.atmo, 56, 'out'],
          [T.cloudIn, 64, 'hold'], [T.reveal, 64, 'out'], [T.wide, 60, 'out'], [T.end, 58, 'linear']],
    roll: [[0, 0, 'hold'], [T.dive, 0, 'inOut'], [T.atmo, 0.045, 'inOut'], [T.cloudIn, 0.02, 'out'],
           [T.reveal, -0.012, 'out'], [T.end, 0, 'linear']],
    /* position du sujet dans le cadre, en demi-cadres : la planète dérive vers la
       gauche comme dans la référence, puis offY plonge pour dégager l'horizon */
    offX: [[0, -0.06, 'linear'], [12, -0.20, 'linear'], [T.refEnd, -0.36, 'out'],
           [T.dive, -0.22, 'linear'], [T.cloudIn, -0.10, 'hold']],
    offY: [[0, 0.06, 'linear'], [14, 0.02, 'linear'], [T.refEnd, -0.04, 'in'],
           [T.dive, -0.55, 'linear'], [T.atmo, -1.25, 'out'], [T.cloudIn, -1.55, 'hold']]
  },
  Descent: {
    alt: [[0, 1560, 'hold'], [T.swap, 1560, 'linear'], [T.reveal, 900, 'out'],
          [T.reveal + 1.5, 700, 'out'], [T.wide, 560, 'out'], [T.end, 520, 'linear']],
    pitch: [[0, -0.62, 'hold'], [T.swap, -0.62, 'out'], [T.reveal, -0.42, 'out'],
            [T.reveal + 1.5, -0.18, 'out'], [T.wide, -0.030, 'out'], [T.end, 0.016, 'linear']],
    yaw: [[0, 0.12, 'hold'], [T.swap, 0.12, 'inOut'], [T.wide, 0.03, 'out'], [T.end, 0, 'linear']],
    fwd: [[0, 9800, 'hold'], [T.swap, 9800, 'linear'], [T.reveal, 6400, 'linear'],
          [T.reveal + 1.5, 4200, 'in'], [T.wide, 1500, 'out'], [T.end, 260, 'linear']],
    sway: [[0, -900, 'hold'], [T.swap, -900, 'inOut'], [T.wide, -180, 'out'], [T.end, 0, 'linear']]
  },
  Sky: {
    /* Le soleil ne bouge pas pendant la partie orbitale (le terminateur de la
       référence en dépend). Il passe derrière l'infrastructure et remonte au
       zénith pendant la traversée des nuages — donc à l'abri du regard. */
    sunAz: [[0, -0.87, 'hold'], [T.cloudIn, -0.87, 'inOut'], [T.swap, 3.02, 'hold']],
    sunEl: [[0, 0.18, 'hold'], [T.cloudIn, 0.18, 'inOut'], [T.swap, 0.50, 'hold']],
    atmo: [[0, 0.34, 'linear'], [T.refEnd, 0.55, 'in'], [T.dive, 1.15, 'linear'],
           [T.atmo, 1.75, 'out'], [T.cloudIn, 1.50, 'hold']],
    nightMix: [[0, 1, 'hold'], [T.refEnd, 1, 'out'], [T.dive + 1.5, 0.55, 'out'], [T.atmo + 1, 0.15, 'hold']],
    cloudSpin: [[0, 0, 'linear'], [T.end, 0.06, 'linear']],
    starFade: [[0, 1, 'hold'], [T.refEnd, 1, 'in'], [T.dive, 0.75, 'linear'],
               [T.atmo, 0.28, 'out'], [T.cloudIn, 0.05, 'hold']],
    moonPhase: [[0, 3.05, 'out'], [6, 2.36, 'linear'], [12, 1.85, 'out'], [18, 1.57, 'linear'], [T.refEnd, 1.45, 'hold']],
    moonRise: [[0, 0.10, 'linear'], [T.refEnd, -0.03, 'hold']],
    ambient: [[0, 0, 'hold'], [T.atmo, 0, 'out'], [T.swap, 0.80, 'out'], [T.reveal, 1.20, 'linear'], [T.end, 1.15, 'linear']],
    haze: [[0, 0, 'hold'], [T.atmo, 0.10, 'in'], [T.cloudIn, 0.62, 'in'], [T.swap - 0.1, 1.0, 'hold']]
  },
  Clouds: {
    /* densité de la dalle ; elle reste au-dessus de la caméra dans le plan final */
    deck: [[0, 0, 'hold'], [T.cloudIn + 0.5, 0.45, 'in'], [T.swap, 1.35, 'hold'],
           [T.reveal, 1.05, 'out'], [T.wide, 0.60, 'linear'], [T.end, 0.46, 'linear']],
    /* occlusion : 0.80 plutôt que 1, pour que la matière des nuages continue de
       bouger pendant que la vue est bouchée — on ne voit plus rien, mais on descend */
    veil: [[0, 0, 'hold'], [T.cloudIn - 0.9, 0, 'in'], [T.cloudIn, 0.46, 'in'],
           [T.cloudIn + 1.1, 0.80, 'linear'], [T.cloudOut - 1.3, 0.78, 'out'],
           [T.reveal, 0.26, 'out'], [T.reveal + 1.1, 0.05, 'linear'], [T.wide, 0.02, 'linear']]
  },
  Site: {
    reveal: [[0, 0, 'hold'], [T.reveal, 0.15, 'out'], [T.reveal + 1.5, 0.6, 'out'], [T.wide, 1, 'hold']],
    /* à midi les intérieurs ne brûlent pas : le verre se lit par sa trame, pas
       par sa lumière */
    windows: [[0, 0, 'hold'], [T.cloudOut - 0.7, 0.06, 'out'], [T.reveal + 0.5, 0.18, 'out'],
              [T.wide - 0.9, 0.30, 'linear'], [T.end, 0.34, 'linear']],
    beams: [[0, 0, 'hold'], [T.reveal + 0.3, 0.10, 'out'], [T.reveal + 2, 0.34, 'out'],
            [T.wide + 0.6, 0.50, 'linear'], [T.end, 0.48, 'linear']],
    holo: [[0, 0, 'hold'], [T.reveal + 1, 0.20, 'out'], [T.wide, 0.7, 'linear'], [T.end, 0.85, 'linear']],
    traffic: [[0, 0, 'hold'], [T.reveal + 0.5, 0.30, 'out'], [T.wide, 0.9, 'linear'], [T.end, 1.0, 'linear']]
  },
  FX: {
    exposure: [[0, 0.34, 'linear'], [T.refEnd, 0.40, 'in'], [T.dive, 0.52, 'linear'],
               [T.atmo, 0.66, 'linear'], [T.cloudIn, 0.80, 'out'], [T.cloudOut - 1.5, 0.78, 'out'],
               [T.reveal, 0.78, 'out'], [T.wide, 0.74, 'linear'], [T.end, 0.72, 'linear']],
    bloom: [[0, 0.55, 'linear'], [T.refEnd, 0.62, 'in'], [T.atmo, 0.95, 'out'],
            [T.swap, 1.15, 'out'], [T.reveal + 0.5, 0.90, 'linear'], [T.end, 0.70, 'linear']],
    vignette: [[0, 0.86, 'linear'], [T.refEnd, 0.82, 'out'], [T.atmo, 0.72, 'out'],
               [T.swap, 0.58, 'linear'], [T.reveal, 0.48, 'linear'], [T.end, 0.40, 'linear']],
    grain: [[0, 0.022, 'linear'], [T.refEnd, 0.020, 'in'], [T.atmo, 0.030, 'in'],
            [T.swap, 0.036, 'out'], [T.reveal + 0.5, 0.016, 'linear'], [T.end, 0.011, 'linear']],
    aberr: [[0, 0.10, 'linear'], [T.refEnd, 0.14, 'in'], [T.dive, 0.55, 'linear'],
            [T.atmo, 1.30, 'in'], [T.cloudIn + 0.5, 1.70, 'out'], [T.reveal, 0.90, 'out'],
            [T.wide, 0.30, 'linear'], [T.end, 0.22, 'linear']],
    smear: [[0, 0, 'hold'], [T.refEnd + 1, 0, 'in'], [T.dive, 0.20, 'linear'], [T.atmo, 0.55, 'in'],
            [T.cloudIn + 1, 0.75, 'out'], [T.reveal, 0.42, 'out'], [T.reveal + 2, 0.16, 'out'], [T.wide, 0.02, 'hold']],
    streaks: [[0, 0, 'hold'], [T.refEnd + 1.5, 0, 'in'], [T.dive, 0.35, 'linear'], [T.atmo, 0.85, 'in'],
              [T.cloudIn + 1, 1.0, 'out'], [T.reveal, 0.55, 'out'], [T.reveal + 2, 0.18, 'out'], [T.wide, 0, 'hold']],
    fade: [[0, 1, 'out'], [2.4, 0, 'hold']],
    /* pas de flash de transition : juste l'éclat de lumière en perçant la dalle */
    flash: [[0, 0, 'hold'], [T.reveal + 0.1, 0, 'expoOut'], [T.reveal + 0.5, 0.18, 'out'], [T.reveal + 1.3, 0, 'hold']]
  },
  UI: {
    hud: [[0, 0, 'out'], [1.6, 0.5, 'linear'], [T.atmo + 1, 0.5, 'in'], [T.cloudIn + 0.1, 0, 'hold'],
          [T.reveal + 1.1, 0, 'out'], [T.reveal + 2.3, 0.5, 'linear']],
    signal: [[0, 0, 'out'], [1.2, 1, 'linear'], [4.4, 1, 'in'], [5.6, 0, 'hold']],
    space: [[0, 0, 'hold'], [5.8, 0, 'out'], [6.8, 1, 'linear'], [11, 1, 'in'], [12, 0, 'hold']],
    label: [[0, 0, 'hold'], [13, 0, 'out'], [14, 1, 'linear'], [22, 1, 'in'], [23.4, 0, 'hold']],
    readout: [[0, 0, 'hold'], [9, 0, 'out'], [10, 1, 'linear'], [T.atmo + 2, 1, 'in'],
              [T.cloudIn + 0.5, 0, 'hold'], [T.wide - 1.9, 0, 'out'], [T.wide - 0.9, 1, 'linear']],
    descent: [[0, 0, 'hold'], [T.refEnd + 1.5, 0, 'out'], [T.refEnd + 2.6, 1, 'linear'],
              [T.atmo - 0.5, 1, 'in'], [T.atmo + 0.6, 0, 'hold']],
    blind: [[0, 0, 'hold'], [T.cloudIn + 0.7, 0, 'out'], [T.cloudIn + 1.4, 1, 'linear'],
            [T.cloudOut - 0.9, 1, 'in'], [T.cloudOut - 0.1, 0, 'hold']],
    city: [[0, 0, 'hold'], [T.reveal + 1.1, 0, 'out'], [T.reveal + 2.1, 1, 'linear'],
           [T.wide + 0.8, 1, 'in'], [T.wide + 1.7, 0, 'hold']],
    title: [[0, 0, 'hold'], [T.end - 2, 0, 'out'], [T.end - 0.8, 1, 'linear']],
    enter: [[0, 0, 'hold'], [T.end - 0.8, 0, 'out'], [T.end, 1, 'linear']],
    pages: [[0, 0, 'hold'], [T.end - 1, 0, 'out'], [T.end, 1, 'linear']]
  }
};

const PHASES = [
  [0, 'orbite'], [T.refEnd, 'entrée'], [T.atmo, 'atmosphère'],
  [T.cloudIn, 'nuages'], [T.reveal, 'révélation'], [T.wide, 'site'], [T.end, 'libre']
];

const PROP_TYPES = {
  Camera: { world: [0, 1], dist: [101, 12000], azim: [-6.5, 6.5], elev: [-1.5, 1.5], fov: [18, 120], roll: [-1.5, 1.5], offX: [-2.5, 2.5], offY: [-2.5, 2.5] },
  Descent: { alt: [40, 6000], pitch: [-1.5, 1.5], yaw: [-3.2, 3.2], fwd: [-12000, 20000], sway: [-8000, 8000] },
  Sky: { sunAz: [-3.2, 3.2], sunEl: [-1.5, 1.5], atmo: [0, 3], nightMix: [0, 2], cloudSpin: [-1, 1], starFade: [0, 1.5], moonPhase: [0, 6.3], moonRise: [-1, 1], ambient: [0, 3], haze: [0, 1] },
  Clouds: { deck: [0, 1.6], veil: [0, 1] },
  Site: { reveal: [0, 1], windows: [0, 2], beams: [0, 2], holo: [0, 2], traffic: [0, 2] },
  FX: { exposure: [0, 3], bloom: [0, 3], vignette: [0, 1], grain: [0, 0.1], aberr: [0, 4], smear: [0, 1.5], streaks: [0, 1.5], fade: [0, 1], flash: [0, 1] },
  UI: { hud: [0, 1], signal: [0, 1], space: [0, 1], label: [0, 1], readout: [0, 1], descent: [0, 1], blind: [0, 1], city: [0, 1], title: [0, 1], enter: [0, 1], pages: [0, 1] }
};

/* ── compilateur de fichier de sauvegarde ─────────────────────────────────────
   Theatre stocke une séquence en une piste keyframée par prop. Construire l'état
   ici — au lieu de coder des tweens — est ce qui rend chaque timing déplaçable.
   Compilateur, résolveur de Bézier et échantillonneur de table sont partagés
   avec blackhole-cinematic.js / forest-cinematic.js — voir
   cinematic/shared/theatre-timeline.js. */
const nid = createIdFactory('ob');
const compileState = () => compileTimelineState({ SHOTS, LENGTH, sheetName: 'Descent', nid });

function sampleTable(pos, out) { return sampleShotsTable(SHOTS, pos, out); }

let cached = null;   // un projet par document : re-rentrer ne doit pas en monter deux

export async function createCinematic({ stage, root, studio = false, onPhase, onEnd } = {}) {
  let core = null;
  try {
    /* Vite/npm expose les exports nommés ; un CDN peut ne livrer que `default`. */
    const mod = await import('@theatre/core');
    core = mod && typeof mod.getProject === 'function' ? mod
      : (mod && mod.default && typeof mod.default.getProject === 'function' ? mod.default : null);
    if (!core) throw new Error('exports @theatre/core introuvables');
    if (studio) {
      const smod = await import('@theatre/studio');
      const st = smod.default && smod.default.initialize ? smod.default
        : (smod.initialize ? smod : (smod.default && smod.default.default) || null);
      if (!cached || !cached.studioOn) st.initialize();
      cached = Object.assign(cached || {}, { studioOn: true });
    }
  } catch (e) {
    core = null;
    console.warn('[orbital-cinematic] Theatre.js absent — horloge interne, mêmes valeurs', e && e.message || e);
  }

  const cue = {};
  const CUES = Object.keys(SHOTS.UI);
  CUES.forEach(k => { cue[k] = root ? root.querySelector('[data-wc="' + k + '"]') : null; });
  const RISE = { signal: 10, space: 14, label: 16, descent: 14, blind: 12, city: 16, title: 26, enter: 12 };
  const lastCue = {};

  const paint = (k, v) => {
    const el = cue[k];
    if (!el || lastCue[k] === v) return;
    lastCue[k] = v;
    el.style.opacity = String(v);
    const rise = RISE[k];
    if (rise) el.style.transform = 'translate3d(0,' + ((1 - v) * rise).toFixed(2) + 'px,0)';
    if (k === 'pages') el.style.pointerEvents = v > 0.9 ? 'auto' : 'none';
  };

  let phase = '';
  const setPhase = pos => {
    let p = PHASES[0][1];
    for (const [t, name] of PHASES) if (pos >= t) p = name;
    if (p !== phase) { phase = p; onPhase && onPhase(p, pos); }
  };

  const apply = v => {
    if (stage && stage.setShot) {
      stage.setShot(v.Camera); stage.setShot(v.Descent); stage.setShot(v.Sky);
      stage.setShot(v.Clouds); stage.setShot(v.Site); stage.setShot(v.FX);
    }
    for (const k in v.UI) paint(k, +v.UI[k].toFixed(3));
  };

  const scratch = {};
  let driver;

  if (core) {
    const state = STATE_OVERRIDE || compileState();
    let project, usedState = true;
    if (cached && cached.project) { project = cached.project; usedState = cached.usedState; }
    else {
      try { project = core.getProject('Orbital Cinematic', { state }); }
      catch (e) {
        console.warn('[orbital-cinematic] état compilé refusé, échantillonnage de la table', e);
        project = core.getProject('Orbital Cinematic');
        usedState = false;
      }
      cached = Object.assign(cached || {}, { project, usedState });
    }
    const sheet = project.sheet('Descent');
    const seq = sheet.sequence;
    const mk = group => {
      const p = {};
      for (const k in PROP_TYPES[group]) {
        const [min, max] = PROP_TYPES[group][k];
        const seed = SHOTS[group][k] ? SHOTS[group][k][0][1] : min;
        p[k] = core.types.number(seed, { range: [min, max], nudgeMultiplier: (max - min) / 400 });
      }
      return p;
    };
    const objs = {};
    for (const g in PROP_TYPES) objs[g] = sheet.object(g, mk(g));

    const vals = {};
    for (const g in objs) vals[g] = objs[g].value;
    const unsubs = [];
    if (usedState) {
      for (const g in objs) unsubs.push(objs[g].onValuesChange(v => {
        vals[g] = v;
        apply(vals);
        setPhase(seq.position);
      }));
    } else {
      let raf = 0, lastPos = -1;
      const tick = () => {
        raf = requestAnimationFrame(tick);
        const pos = seq.position;
        if (pos === lastPos) return;
        lastPos = pos;
        apply(sampleTable(pos, scratch));
        setPhase(pos);
      };
      tick();
      unsubs.push(() => cancelAnimationFrame(raf));
    }
    driver = {
      project, sheet, sequence: seq, objects: objs, usingTheatreState: usedState,
      get position() { return seq.position; },
      set position(p) { seq.position = p; },
      async play(from) { await project.ready; seq.position = from; return seq.play({ range: [0, LENGTH], rate: 1 }); },
      pause() { seq.pause(); },
      settle() { if (!usedState) apply(sampleTable(seq.position, scratch)); },
      stop() { unsubs.forEach(f => f()); unsubs.length = 0; try { seq.pause(); } catch (e) { /* déjà démonté */ } }
    };
  } else {
    /* horloge interne : mêmes nombres, mêmes easings, aucun rendu supplémentaire */
    let pos = 0, playing = false, raf = 0, last = 0;
    const tick = now => {
      raf = requestAnimationFrame(tick);
      const dt = last ? Math.min((now - last) / 1000, 0.1) : 0;
      last = now;
      if (playing) pos = Math.min(LENGTH, pos + dt);
      apply(sampleTable(pos, scratch));
      setPhase(pos);
      if (playing && pos >= LENGTH) playing = false;
    };
    raf = requestAnimationFrame(tick);
    driver = {
      usingTheatreState: false,
      get position() { return pos; },
      set position(p) { pos = p; last = 0; },
      async play(from) { pos = from; playing = true; last = 0; },
      pause() { playing = false; },
      settle() { apply(sampleTable(pos, scratch)); },
      stop() { cancelAnimationFrame(raf); playing = false; }
    };
  }

  /* première frame avant lecture : l'écran n'est jamais une image non mise en scène */
  apply(sampleTable(0, scratch));
  setPhase(0);

  let ended = false;
  const finish = () => {
    if (ended) return;
    ended = true;
    if (stage && stage.release) stage.release();
    onEnd && onEnd();
  };
  const endWatch = setInterval(() => { if (driver.position >= LENGTH - 0.05) finish(); }, 200);

  const api = {
    LENGTH, TIMES: T, CLOUD_SECS,
    sequence: driver.sequence, objects: driver.objects, project: driver.project,
    usingTheatreState: driver.usingTheatreState,
    get position() { return driver.position; },
    async play() {
      if (matchMedia('(prefers-reduced-motion: reduce)').matches) { api.skipToEnd(); return; }
      ended = false;
      if (stage && stage.hold) stage.hold();
      return driver.play(0);
    },
    pause() { driver.pause(); },
    replay() { ended = false; if (stage && stage.hold) stage.hold(); driver.play(0); },
    seek(p) { driver.position = Math.max(0, Math.min(LENGTH, p)); driver.settle(); },
    skipToEnd() {
      driver.pause();
      driver.position = LENGTH - 0.01;
      driver.settle();
      apply(sampleTable(LENGTH, scratch));
      setPhase(LENGTH);
      finish();
    },
    dispose() { clearInterval(endWatch); driver.stop(); }
  };
  return api;
}

/* ── amorçage de la page ──────────────────────────────────────────────────────
   Un seul boot, une seule scène, une seule boucle de rendu. waitForStage vient
   de cinematic/shared/theatre-timeline.js — même sonde que
   blackhole/forest, sélecteur propre. */
const waitForStage = () => waitForStageEl('orbital-stage[drive="external"]');

let booted = false;

export async function bootPage() {
  if (booted) return;
  booted = true;
  const root = document.querySelector('[data-wc-root]') || document.body;
  const q = s => root.querySelector(s);
  const useStudio = new URLSearchParams(location.search).has('studio');

  let stage, cine;
  try {
    stage = await waitForStage();
    cine = await createCinematic({
      stage, root, studio: useStudio,
      onPhase: p => { const el = q('[data-wc="phaseName"]'); if (el) el.textContent = p; },
      onEnd: () => { paintSkip(0); stage.bindpages(root.querySelectorAll('[data-orb-spot]')); }
    });
  } catch (e) {
    console.error('[orbital-cinematic]', e);
    return;
  }

  /* poignée console pour la direction artistique : __obCine.seek(37) / .pause() */
  window.__obCine = cine;

  function paintSkip(v) {
    const el = q('[data-wc="skip"]');
    if (el) { el.style.opacity = String(v); el.style.pointerEvents = v ? 'auto' : 'none'; }
  }
  paintSkip(0.55);

  /* télémétrie : quatre lectures par seconde, pas un rendu par frame */
  const write = (k, v) => {
    const el = q('[data-wc-num="' + k + '"]');
    if (el && el.textContent !== v) el.textContent = v;
  };
  const fmt = n => n.toLocaleString('fr-FR', { maximumFractionDigits: 0 });
  let prev = null, prevT = 0;
  const readTimer = setInterval(() => {
    if (!stage.shot) return;
    const s = stage.shot(), now = performance.now();
    const ground = s.world >= 0.5;
    /* 1 unité spatiale = 63,71 km (rayon terrestre = 100 unités) ; au sol, 1 u = 1 m */
    const km = ground ? s.alt / 1000 : (s.dist - 100) * 63.71;
    write('alt', ground ? fmt(s.alt) + ' m' : fmt(km) + ' km');
    if (prev !== null) {
      const dt = Math.max((now - prevT) / 1000, 0.001);
      const v = Math.abs(km - prev) / dt;
      write('speed', ground ? fmt(v * 1000) + ' m/s' : fmt(v) + ' km/s');
    }
    prev = km; prevT = now;
    write('fov', s.fov.toFixed(0) + '°');
  }, 250);

  const skip = q('[data-wc="skip"]');
  if (skip) skip.addEventListener('click', () => { cine.skipToEnd(); paintSkip(0); });
  const replay = q('[data-wc="replay"]');
  if (replay) replay.addEventListener('click', () => { paintSkip(0.55); cine.replay(); });

  addEventListener('pagehide', () => { clearInterval(readTimer); cine.dispose(); }, { once: true });
  cine.play();
}

if (document.readyState === 'loading') addEventListener('DOMContentLoaded', bootPage, { once: true });
else bootPage();

