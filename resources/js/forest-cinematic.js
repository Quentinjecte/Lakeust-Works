/* forest-cinematic.js — Theatre.js staging for <forest-stage> (dirt_road_forest.glb).
   Same three-layer split as welcome-cinematic.js:

     Three.js    forest-stage.js   → rendering (sky, model, sun, shadows, bloom, RAF)
     Theatre.js  this file         → timeline / staging
     the page    forest-cinematic-entry.js → orchestration

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

export const LENGTH = 20;

/* Paste a studio export here to freeze the timeline; null = compile from SHOTS. */
const STATE_OVERRIDE = null;

const E = {
  linear: [0, 0, 1, 1],
  in: [0.42, 0, 1, 1],
  out: [0, 0, 0.58, 1],
  inOut: [0.42, 0, 0.58, 1],
  expoIn: [0.7, 0, 0.84, 0],
  expoOut: [0.16, 1, 0.3, 1],
  hold: [1, 0, 1, 0]
};

/* ── the cinematic ──────────────────────────────────────────────────────────
   [time, value, easeToNext]. `s` is arc length along the road (0 = the near
   plateau at Y≈173, 1 = the valley floor at Y≈133, 368 units apart). */
const SHOTS = {
  Camera: {
    s: [[0, 0.040, 'out'], [3, 0.100, 'linear'], [8, 0.34, 'linear'], [13, 0.64, 'linear'], [16.4, 0.85, 'out'], [20, 0.95, 'linear']],
    lift: [[0, 6.4, 'out'], [3, 7.0, 'inOut'], [8, 8.0, 'inOut'], [13, 9.0, 'in'], [15, 9.6, 'in'], [16.4, 10.4, 'out'], [18, 11.2, 'out'], [20, 11.8, 'linear']],
    /* `side` n'est pas un ornement : c'est la trajectoire dans la largeur du chemin.
       Relevé sur le décor (distance aux troncs le long de l'axe), il évite le gros
       conifère planté au milieu du virage vers 8 s et le passage étroit vers 13 s. */
    side: [[0, -1, 'inOut'], [4.7, 2, 'inOut'], [7.8, -7, 'inOut'], [9.8, 4, 'inOut'], [11.5, -4, 'inOut'], [12.7, -6, 'inOut'], [14, 4.5, 'inOut'], [15.6, 0, 'inOut'], [20, 0, 'linear']],
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

/* ── save-file compiler (identical shape to welcome-cinematic.js) ──────────── */
let uid = 0;
const nid = () => 'fc' + (uid++).toString(36) + Math.floor(Math.random() * 1e6).toString(36);

function compileState() {
  const tracksByObject = {};
  for (const objKey in SHOTS) {
    const trackData = {}, trackIdByPropPath = {};
    for (const prop in SHOTS[objKey]) {
      const rows = SHOTS[objKey][prop];
      const id = nid();
      const kfs = rows.map(([pos, value]) => ({
        id: nid(), position: pos, connectedRight: true,
        handles: [0.5, 1, 0.5, 0], type: 'bezier', value
      }));
      rows.forEach((row, i) => {
        if (i >= kfs.length - 1) return;
        const [x1, y1, x2, y2] = E[row[2] || 'inOut'] || E.inOut;
        kfs[i].handles[2] = x1; kfs[i].handles[3] = y1;
        kfs[i + 1].handles[0] = x2; kfs[i + 1].handles[1] = y2;
      });
      trackData[id] = {
        type: 'BasicKeyframedTrack',
        __debugName: objKey + ':["' + prop + '"]',
        keyframes: kfs
      };
      trackIdByPropPath['["' + prop + '"]'] = id;
    }
    tracksByObject[objKey] = { trackData, trackIdByPropPath };
  }
  return {
    sheetsById: {
      Flythrough: {
        staticOverrides: { byObject: {} },
        sequence: { subUnitsPerUnit: 30, length: LENGTH, type: 'PositionalSequence', tracksByObject }
      }
    },
    definitionVersion: '0.4.0',
    revisionHistory: [nid()]
  };
}

function bezier(t, p) {
  const [x1, y1, x2, y2] = p;
  let a = t;
  for (let i = 0; i < 6; i++) {
    const s = 1 - a;
    const x = 3 * s * s * a * x1 + 3 * s * a * a * x2 + a * a * a;
    const dx = 3 * s * s * x1 + 6 * s * a * (x2 - x1) + 3 * a * a * (1 - x2);
    if (Math.abs(dx) < 1e-5) break;
    a -= (x - t) / dx;
    a = Math.min(1, Math.max(0, a));
  }
  const s = 1 - a;
  return 3 * s * s * a * y1 + 3 * s * a * a * y2 + a * a * a;
}

export function sampleTable(pos) {
  const out = {};
  for (const objKey in SHOTS) {
    const vals = out[objKey] = {};
    for (const prop in SHOTS[objKey]) {
      const rows = SHOTS[objKey][prop];
      if (pos <= rows[0][0]) { vals[prop] = rows[0][1]; continue; }
      const last = rows[rows.length - 1];
      if (pos >= last[0]) { vals[prop] = last[1]; continue; }
      let i = 0;
      while (i < rows.length - 1 && rows[i + 1][0] <= pos) i++;
      const [t0, v0, ease] = rows[i], [t1, v1] = rows[i + 1];
      const u = (pos - t0) / Math.max(t1 - t0, 1e-6);
      vals[prop] = v0 + (v1 - v0) * bezier(u, E[ease || 'inOut'] || E.inOut);
    }
  }
  return out;
}

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

let cached = null;   // one project per document — re-entering must not build a second

export async function createCinematic({ stage, root, studio = false, onPhase, onEnd } = {}) {
  const core = await import('@theatre/core');
  const api0 = (core && core.getProject) ? core : core.default;
  const { getProject, types } = api0;

  if (studio) {
    const sm = await import('@theatre/studio');
    const st = (sm.default && sm.default.initialize) ? sm.default
      : (sm.default && sm.default.default) ? sm.default.default : sm;
    if (!cached || !cached.studioOn) { st.initialize(); }
    cached = cached || {};
    cached.studioOn = true;
  }

  const state = STATE_OVERRIDE || compileState();
  let project, usedState = true;
  if (cached && cached.project) {
    project = cached.project;
    usedState = cached.usedState;
  } else {
    try {
      project = getProject('Forest Cinematic', { state });
    } catch (e) {
      console.warn('[forest-cinematic] compiled state rejected, sampling the table instead', e);
      project = getProject('Forest Cinematic');
      usedState = false;
    }
    cached = Object.assign(cached || {}, { project, usedState });
  }

  const sheet = project.sheet('Flythrough');
  const seq = sheet.sequence;

  const mkProps = group => {
    const p = {};
    for (const k in PROP_TYPES[group]) {
      const [min, max] = PROP_TYPES[group][k];
      const seed = SHOTS[group][k] ? SHOTS[group][k][0][1] : min;
      p[k] = types.number(seed, { range: [min, max], nudgeMultiplier: (max - min) / 400 });
    }
    return p;
  };
  const objs = {
    Camera: sheet.object('Camera', mkProps('Camera')),
    Environment: sheet.object('Environment', mkProps('Environment')),
    UI: sheet.object('UI', mkProps('UI')),
    Transition: sheet.object('Transition', mkProps('Transition'))
  };

  const cue = {};
  const CUES = ['hud', 'signal', 'space', 'label', 'readout', 'title', 'enter', 'flash', 'curtain'];
  CUES.forEach(k => { cue[k] = root ? root.querySelector('[data-wc="' + k + '"]') : null; });
  const lastCue = {};
  const RISE = { signal: 10, space: 14, label: 16, title: 26, enter: 12 };

  const paint = (k, v) => {
    const el = cue[k];
    if (!el || lastCue[k] === v) return;
    lastCue[k] = v;
    el.style.opacity = String(v);
    const rise = RISE[k];
    if (rise) el.style.transform = 'translate3d(0,' + ((1 - v) * rise).toFixed(2) + 'px,0)';
    if (k === 'curtain' || k === 'flash') el.style.pointerEvents = v > 0.02 ? 'auto' : 'none';
  };

  let phase = '';
  const setPhase = pos => {
    let p = PHASES[0][1];
    for (const [t, name] of PHASES) if (pos >= t) p = name;
    if (p !== phase) { phase = p; onPhase && onPhase(p, pos); }
  };

  const apply = (camera, env, ui, tr) => {
    if (stage && stage.setShot) stage.setShot(Object.assign({}, camera, env));
    for (const k in ui) paint(k, +ui[k].toFixed(3));
    paint('flash', +tr.flash.toFixed(3));
    paint('curtain', +tr.curtain.toFixed(3));
  };

  const unsubs = [];
  let vals = { Camera: objs.Camera.value, Environment: objs.Environment.value, UI: objs.UI.value, Transition: objs.Transition.value };

  if (usedState) {
    for (const key in objs) {
      unsubs.push(objs[key].onValuesChange(v => {
        vals[key] = v;
        apply(vals.Camera, vals.Environment, vals.UI, vals.Transition);
        setPhase(seq.position);
      }));
    }
  } else {
    let raf = 0, lastPos = -1;
    const tick = () => {
      raf = requestAnimationFrame(tick);
      const pos = seq.position;
      if (pos === lastPos) return;
      lastPos = pos;
      const s = sampleTable(pos);
      apply(s.Camera, s.Environment, s.UI, s.Transition);
      setPhase(pos);
    };
    tick();
    unsubs.push(() => cancelAnimationFrame(raf));
  }

  const seed = sampleTable(0);
  apply(seed.Camera, seed.Environment, seed.UI, seed.Transition);
  setPhase(0);

  let ended = false;
  const watchEnd = () => {
    if (ended || seq.position < LENGTH - 0.05) return;
    ended = true;
    onEnd && onEnd();
  };
  const endWatch = setInterval(watchEnd, 250);

  const api = {
    sheet, sequence: seq, objects: objs, project, usingTheatreState: usedState,
    async play() {
      await project.ready;
      if (matchMedia('(prefers-reduced-motion: reduce)').matches) { api.skipToEnd(); return; }
      seq.position = 0; ended = false;
      return seq.play({ range: [0, LENGTH], rate: 1 });
    },
    pause() { seq.pause(); },
    replay() { ended = false; seq.position = 0; seq.play({ range: [0, LENGTH] }); },
    skipToEnd() {
      seq.pause();
      seq.position = LENGTH - 0.01;
      if (!usedState) { const s = sampleTable(LENGTH); apply(s.Camera, s.Environment, s.UI, s.Transition); }
      setPhase(LENGTH);
      if (!ended) { ended = true; onEnd && onEnd(); }
    },
    seek(pos) { seq.position = Math.max(0, Math.min(LENGTH, pos)); },
    dispose() {
      clearInterval(endWatch);
      unsubs.forEach(fn => fn());
      unsubs.length = 0;
      try { seq.pause(); } catch (e) { /* sequence already torn down */ }
    }
  };
  return api;
}
