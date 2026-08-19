/* welcome-cinematic.js — Theatre.js staging for the existing <black-hole-stage>.
   Nothing here renders: Three.js owns the scene, the shaders and the render loop.
   This module owns the *timeline* — camera, FOV, effect intensities, UI cues, the
   flash/blackout transition — and pushes values into the stage through setShot().

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

export const LENGTH = 27;

/* Paste a studio export here to freeze the timeline; null = compile from SHOTS. */
const STATE_OVERRIDE = null;

/* Easing control points, CSS cubic-bezier convention. */
const E = {
  linear: [0, 0, 1, 1],
  in: [0.42, 0, 1, 1],
  out: [0, 0, 0.58, 1],
  inOut: [0.42, 0, 0.58, 1],
  expoIn: [0.7, 0, 0.84, 0],
  expoOut: [0.16, 1, 0.3, 1],
  hold: [1, 0, 1, 0]
};

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

/* ── save-file compiler ──────────────────────────────────────────────────────
   Theatre stores a sequence as one keyframed track per prop. Building the state
   here (rather than hard-coding tweens) is what makes every timing draggable.   */
let uid = 0;
const nid = () => 'wc' + (uid++).toString(36) + Math.floor(Math.random() * 1e6).toString(36);

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
      Approach: {
        staticOverrides: { byObject: {} },
        sequence: { subUnitsPerUnit: 30, length: LENGTH, type: 'PositionalSequence', tracksByObject }
      }
    },
    definitionVersion: '0.4.0',
    revisionHistory: [nid()]
  };
}

/* Table sampling — the safety net if a Theatre build rejects the compiled state.
   Same numbers, same easing, so the cinematic is identical either way. */
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

function sampleTable(pos) {
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
  Camera: { dist: [1.02, 400], azim: [-6.5, 6.5], elev: [-1.5, 1.5], fov: [18, 120], roll: [-1.5, 1.5], parallax: [0, 2] },
  Effects: { fade: [0, 1], exposure: [0, 6], bgFade: [0, 1], diskBright: [0, 3], diskThick: [0, 2], diskMul: [0, 2], spin: [0, 4], lens: [0.4, 2.4], aberr: [0, 5], vignette: [0, 1], grain: [0, 0.1], bloom: [0, 4] },
  UI: { hud: [0, 1], signal: [0, 1], space: [0, 1], label: [0, 1], readout: [0, 1], title: [0, 1], enter: [0, 1] },
  Transition: { flash: [0, 1], curtain: [0, 1] }
};

let cached = null;   // one project per document — re-entering must not build a second

export async function createCinematic({ stage, root, studio = false, onPhase, onEnd } = {}) {
  const core = await import('@theatre/core');
  /* named exports sit on .default in some bundling paths — cover both */
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
      project = getProject('Welcome Cinematic', { state });
    } catch (e) {
      console.warn('[welcome-cinematic] compiled state rejected, sampling the table instead', e);
      project = getProject('Welcome Cinematic');
      usedState = false;
    }
    cached = Object.assign(cached || {}, { project, usedState });
  }

  const sheet = project.sheet('Approach');
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
    Effects: sheet.object('Effects', mkProps('Effects')),
    UI: sheet.object('UI', mkProps('UI')),
    Transition: sheet.object('Transition', mkProps('Transition'))
  };

  /* UI cues: direct style writes on the markup's [data-wc] hooks. Per-frame React
     re-renders for six opacities would be far more expensive than this. */
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

  const apply = (camera, effects, ui, tr) => {
    if (stage && stage.setShot) stage.setShot(Object.assign({}, camera, effects));
    for (const k in ui) paint(k, +ui[k].toFixed(3));
    paint('flash', +tr.flash.toFixed(3));
    paint('curtain', +tr.curtain.toFixed(3));
  };

  const unsubs = [];
  let vals = { Camera: objs.Camera.value, Effects: objs.Effects.value, UI: objs.UI.value, Transition: objs.Transition.value };

  if (usedState) {
    for (const key in objs) {
      unsubs.push(objs[key].onValuesChange(v => {
        vals[key] = v;
        apply(vals.Camera, vals.Effects, vals.UI, vals.Transition);
        setPhase(seq.position);
      }));
    }
  } else {
    /* fallback: one subscription to the sequence position, table-sampled */
    let raf = 0, lastPos = -1;
    const tick = () => {
      raf = requestAnimationFrame(tick);
      const pos = seq.position;
      if (pos === lastPos) return;
      lastPos = pos;
      const s = sampleTable(pos);
      apply(s.Camera, s.Effects, s.UI, s.Transition);
      setPhase(pos);
    };
    tick();
    unsubs.push(() => cancelAnimationFrame(raf));
  }

  /* first frame before playback so the screen is never an unstaged flash */
  const seed = sampleTable(0);
  apply(seed.Camera, seed.Effects, seed.UI, seed.Transition);
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
      if (!usedState) { const s = sampleTable(LENGTH); apply(s.Camera, s.Effects, s.UI, s.Transition); }
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
