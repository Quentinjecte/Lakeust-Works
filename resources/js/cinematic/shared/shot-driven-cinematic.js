/* shot-driven-cinematic.js — fabrique Theatre.js partagée par
   blackhole-cinematic.js et forest-cinematic.js (~100 lignes identiques
   avant cette extraction : bootstrap Theatre.js/studio, réutilisation du
   projet en cache, construction des props Theatre depuis PROP_TYPES,
   cues UI (peintes en style inline sur les [data-wc="..."]), branche
   subscribe (état Theatre chargé) vs poll (état rejeté, secours par
   échantillonnage de la table), watcher de fin de séquence, API
   play/pause/replay/skipToEnd/seek/dispose).

   home-cinematic.js n'utilise PAS cette fabrique : il a son propre secours
   "horloge interne" quand @theatre/core échoue à s'importer DU TOUT (pas
   seulement un état rejeté), applique 6 groupes séparément à setShot (pas
   un Object.assign combiné), et embarque son propre bootPage() — trois
   différences réelles, pas cosmétiques (voir son fichier).

   Ce qui reste spécifique à l'appelant (passé en options) : la table SHOTS
   (chorégraphie), PROP_TYPES (bornes Theatre), PHASES, le nom de projet/
   sheet/préfixe d'ID, et `stageGroups` — quels groupes de SHOTS fusionner
   dans l'appel à stage.setShot() (les autres groupes, UI/Transition, sont
   peints en DOM, jamais poussés vers le stage). */

import { createIdFactory, compileState as compileTimelineState, sampleTable as sampleShotsTable } from './theatre-timeline.js';

export async function createShotDrivenCinematic({
  SHOTS, PROP_TYPES, LENGTH, PHASES,
  projectName, sheetName, idPrefix, stageGroups, logLabel,
  stateOverride = null,
  cacheRef,
  stage, root, studio = false, onPhase, onEnd
} = {}) {
  const nid = createIdFactory(idPrefix);
  const compileState = () => compileTimelineState({ SHOTS, LENGTH, sheetName, nid });
  const sampleTable = pos => sampleShotsTable(SHOTS, pos);

  const core = await import('@theatre/core');
  /* named exports sit on .default in some bundling paths — cover both */
  const api0 = (core && core.getProject) ? core : core.default;
  const { getProject, types } = api0;

  if (studio) {
    const sm = await import('@theatre/studio');
    const st = (sm.default && sm.default.initialize) ? sm.default
      : (sm.default && sm.default.default) ? sm.default.default : sm;
    if (!cacheRef.current || !cacheRef.current.studioOn) { st.initialize(); }
    cacheRef.current = cacheRef.current || {};
    cacheRef.current.studioOn = true;
  }

  const state = stateOverride || compileState();
  let project, usedState = true;
  if (cacheRef.current && cacheRef.current.project) {
    project = cacheRef.current.project;
    usedState = cacheRef.current.usedState;
  } else {
    try {
      project = getProject(projectName, { state });
    } catch (e) {
      console.warn('[' + logLabel + '] compiled state rejected, sampling the table instead', e);
      project = getProject(projectName);
      usedState = false;
    }
    cacheRef.current = Object.assign(cacheRef.current || {}, { project, usedState });
  }

  const sheet = project.sheet(sheetName);
  const seq = sheet.sequence;
  const groupKeys = Object.keys(SHOTS);

  const mkProps = group => {
    const p = {};
    for (const k in PROP_TYPES[group]) {
      const [min, max] = PROP_TYPES[group][k];
      const seed = SHOTS[group][k] ? SHOTS[group][k][0][1] : min;
      p[k] = types.number(seed, { range: [min, max], nudgeMultiplier: (max - min) / 400 });
    }
    return p;
  };
  const objs = {};
  groupKeys.forEach(g => { objs[g] = sheet.object(g, mkProps(g)); });

  /* UI cues: direct style writes on the markup's [data-wc] hooks. Per-frame
     re-renders for a handful of opacities would be far more expensive. */
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

  const apply = vals => {
    if (stage && stage.setShot) {
      const merged = {};
      stageGroups.forEach(g => Object.assign(merged, vals[g]));
      stage.setShot(merged);
    }
    const ui = vals.UI, tr = vals.Transition;
    for (const k in ui) paint(k, +ui[k].toFixed(3));
    paint('flash', +tr.flash.toFixed(3));
    paint('curtain', +tr.curtain.toFixed(3));
  };

  const unsubs = [];
  const vals = {};
  groupKeys.forEach(g => { vals[g] = objs[g].value; });

  if (usedState) {
    groupKeys.forEach(key => {
      unsubs.push(objs[key].onValuesChange(v => {
        vals[key] = v;
        apply(vals);
        setPhase(seq.position);
      }));
    });
  } else {
    /* fallback: one subscription to the sequence position, table-sampled */
    let raf = 0, lastPos = -1;
    const tick = () => {
      raf = requestAnimationFrame(tick);
      const pos = seq.position;
      if (pos === lastPos) return;
      lastPos = pos;
      apply(sampleTable(pos));
      setPhase(pos);
    };
    tick();
    unsubs.push(() => cancelAnimationFrame(raf));
  }

  /* first frame before playback so the screen is never an unstaged flash */
  apply(sampleTable(0));
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
      if (!usedState) apply(sampleTable(LENGTH));
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
