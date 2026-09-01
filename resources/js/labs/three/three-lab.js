/* three-lab.js — entrée Vite de /laboratoire/three, page autonome (pas de
   layout partagé, pas d'app.js). Quatre scènes Three.js réelles montées à la
   demande sur une seule page, plus six écrans d'explication sans WebGL.

   La règle de la page : un seul contexte WebGL vivant à la fois. Un
   navigateur plafonne les contextes ; au-delà il abandonne les plus anciens
   sans prévenir. Ce fichier ne monte donc jamais une deuxième scène sans
   avoir démonté la première — voir mount()/dismount() plus bas.

   Modules réellement importés — jamais les *-cinematic.js, qui s'auto-
   montent en fin de fichier (bootPage()) et démarreraient la vraie
   cinématique sur la première <*-stage> trouvée dans le document. Les
   chemins ci-dessous pointent vers resources/js/three/ (blackhole.js) et
   resources/js/cinematic/ (forest-stage.js, home-stage.js) — pas les
   chemins historiques (welcome-blackhole.js, forest-stage.js,
   orbital-stage.js à la racine) que la doc d'origine suppose encore. */
const MODULES = {
  blackhole: { tag: 'black-hole-stage', load: () => import('../../three/blackhole.js') },
  forest: { tag: 'forest-stage', load: () => import('../../cinematic/forest-cinematic/forest-stage.js') },
  orbital: { tag: 'orbital-stage', load: () => import('../../cinematic/home-cinematic/home-stage.js') }
};

/* ── tables de plans ──────────────────────────────────────────────────────
   Extraits des vraies timelines (welcome-cinematic.js / forest-cinematic.js /
   home-cinematic.js) — assez de points pour montrer la mécanique de chaque
   scène, pas le montage de référence complet. [temps, valeur], interpolation
   lissée (smoothstep) entre deux bornes, palier au-delà de la dernière. */
const TRACKS = {
  welcome: {
    len: 27, rate: 3.4,
    phases: [[0, 'approche'], [9, 'chute'], [16, 'plongée'], [22, 'horizon'], [24.32, 'sortie']],
    fields: {
      dist: [[0, 320], [4, 210], [9, 92], [16, 26], [22, 4.2], [24.3, 1.15], [24.32, 30], [27, 24]],
      azim: [[0, 0.30], [9, 0.42], [16, 0.86], [22, 1.5], [24.32, 0.34], [27, 0.46]],
      elev: [[0, 0.34], [9, 0.30], [16, 0.18], [22, 0.06], [24.32, 0.42], [27, 0.34]],
      fov: [[0, 54], [9, 58], [16, 72], [22, 86], [24.3, 92], [24.32, 60]],
      exposure: [[0, 0.06], [9, 0.58], [16, 0.72], [22, 0.78], [23.2, 3.2], [24.2, 0], [25.6, 0.46]],
      diskBright: [[0, 0.10], [9, 0.44], [16, 0.66], [22, 0.86], [24.3, 1.05], [24.32, 0.52]],
      fade: [[0, 0], [2.2, 1]]
    }
  },
  forest: {
    len: 20, rate: 2.5,
    phases: [[0, 'départ'], [8, 'route'], [13, 'vallée'], [16.4, 'titre']],
    fields: {
      s: [[0, 0.04], [8, 0.34], [16, 0.64], [19.4, 0.85], [20, 0.95]],
      lift: [[0, 15], [3, 7], [13, 9], [16.4, 10.4], [20, 6.8]],
      side: [[0, 5], [4.7, -5], [9.8, 6], [12.7, -1], [20, 0]],
      ahead: [[0, 22], [8, 34], [16.4, 44], [20, 48]],
      aim: [[0, -1.6], [8, -1.2], [16.4, -0.4], [20, 0]],
      fov: [[0, 44], [8, 48], [16.4, 46], [20, 43]],
      exposure: [[0, 1.02], [13, 1.12], [16.25, 1.32], [20, 1.08]],
      bloom: [[0, 0.14], [16.25, 0.42], [20, 0.20]]
    }
  },
  orbital: {
    len: 46, rate: 3.4,
    phases: [[0, 'orbite'], [26, 'entrée'], [33, 'atmosphère'], [35.5, 'nuages'], [39.5, 'révélation'], [46, 'libre']],
    fields: {
      world: [[0, 0], [35.1, 0], [35.5, 1]],
      dist: [[0, 10050], [8, 1900], [17.5, 880], [26, 723], [29.5, 300], [35.5, 104]],
      fov: [[0, 38], [29.5, 44], [33, 56], [35.5, 64], [42, 58]],
      alt: [[0, 1560], [35.5, 1560], [39.5, 900], [43, 560], [46, 520]],
      fwd: [[0, 9800], [35.5, 9800], [39.5, 6400], [46, 260]],
      deck: [[0, 0], [36, 0.45], [36.9, 1.35], [39.5, 1.05], [46, 0.46]],
      exposure: [[0, 0.34], [33, 0.66], [35.5, 0.80], [46, 0.72]]
    }
  }
};

function sampleTrack(rows, t) {
  if (t <= rows[0][0]) return rows[0][1];
  const last = rows[rows.length - 1];
  if (t >= last[0]) return last[1];
  let i = 0;
  while (i < rows.length - 1 && rows[i + 1][0] <= t) i++;
  const [t0, v0] = rows[i], [t1, v1] = rows[i + 1];
  const u = (t - t0) / Math.max(t1 - t0, 1e-6);
  const s = u * u * (3 - 2 * u);
  return v0 + (v1 - v0) * s;
}
function sampleShot(fields, t) {
  const out = {};
  for (const k in fields) out[k] = sampleTrack(fields[k], t);
  return out;
}
function phaseAt(list, t) {
  let p = list[0][1];
  for (const [at, name] of list) if (t >= at) p = name;
  return p;
}

const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

const root = document.querySelector('[data-tl-root]');
if (root) boot();

function boot() {
  const loadedModules = new Map();
  function ensureModule(key) {
    if (!loadedModules.has(key)) loadedModules.set(key, MODULES[key].load());
    return loadedModules.get(key);
  }

  function waitForReady(el, timeoutMs = 12000) {
    return new Promise((resolve, reject) => {
      const t0 = performance.now();
      (function poll() {
        if (typeof el.setShot === 'function') return resolve(el);
        if (performance.now() - t0 > timeoutMs) return reject(new Error('stage timeout'));
        requestAnimationFrame(poll);
      })();
    });
  }

  let currentQuality = 'low';
  let live = null; // { key, slug, tag, el, card, plate, raf, pos, dismissTimer, fpsT, fpsN, lastT }

  function card(plate) { return plate.closest('[data-tl-card]'); }
  function hud(plate, name) { return card(plate).querySelector('[data-tl-hud="' + name + '"]'); }
  function writeHud(plate, name, text) { const el = hud(plate, name); if (el && el.textContent !== text) el.textContent = text; }
  function setPlateState(plate, state) { plate.dataset.tlState = state; }

  function updateLiveCount() {
    const el = root.querySelector('[data-tl-live-count]');
    if (el) el.textContent = live ? '1' : '0';
  }

  function startLoop(slug) {
    const track = TRACKS[slug] || null;
    if (track) live.el.setShot(sampleShot(track.fields, 0));
    live.lastT = performance.now();
    live.fpsT = live.lastT;
    live.fpsN = 0;
    const tick = now => {
      if (!live || live.slug !== slug) return;
      live.raf = requestAnimationFrame(tick);
      live.fpsN++;
      if (now - live.fpsT > 500) {
        writeHud(live.plate, 'fps', String(Math.round((live.fpsN * 1000) / (now - live.fpsT))));
        live.fpsN = 0; live.fpsT = now;
      }
      if (track) {
        if (!reduced) {
          const dt = (now - live.lastT) / 1000;
          live.pos = (live.pos + dt * track.rate) % track.len;
        }
        live.lastT = now;
        live.el.setShot(sampleShot(track.fields, live.pos));
        writeHud(live.plate, 'time', live.pos.toFixed(1) + ' s');
        writeHud(live.plate, 'phase', phaseAt(track.phases, live.pos));
      } else if (live.el.shot) {
        const s = live.el.shot();
        if (s) { writeHud(live.plate, 'time', (s.dist || 0).toFixed(0)); writeHud(live.plate, 'phase', 'interactif'); }
      }
    };
    live.raf = requestAnimationFrame(tick);
  }

  async function mount(plate, key, slug) {
    if (live && live.plate === plate) { clearTimeout(live.dismissTimer); live.dismissTimer = null; return; }
    if (live) await dismount(true);

    const mod = MODULES[key];
    setPlateState(plate, 'loading');
    try {
      await ensureModule(key);
      await customElements.whenDefined(mod.tag);
    } catch (e) {
      console.error('[three-lab] échec du chargement de ' + key, e);
      setPlateState(plate, 'error');
      return;
    }
    if (plate.dataset.tlActive !== '1') { setPlateState(plate, 'idle'); return; } // survol quitté pendant le chargement

    const el = document.createElement(mod.tag);
    /* drive et quality avant prepend() : l'élément ne démarre jamais en mode
       interne par erreur, ni au mauvais palier. */
    el.setAttribute('quality', currentQuality);
    if (key === 'blackhole') el.setAttribute('drive', slug === 'welcome' ? 'external' : 'internal');
    if (key === 'forest') el.setAttribute('src', '/GLB/dirt_road_forest.glb');
    plate.prepend(el);

    live = { key, slug, tag: mod.tag, el, plate, raf: 0, pos: 0, dismissTimer: null, fpsT: 0, fpsN: 0, lastT: 0 };
    setPlateState(plate, 'live');

    try {
      await waitForReady(el);
    } catch (e) {
      console.error('[three-lab] la scène ' + key + ' ne répond pas', e);
      setPlateState(plate, 'error');
      return;
    }
    if (!live || live.el !== el) return; // démontée pendant l'attente
    startLoop(slug);
    updateLiveCount();
  }

  function dismount(immediate) {
    return new Promise(resolve => {
      if (!live) return resolve();
      const finish = () => {
        cancelAnimationFrame(live.raf);
        const el = live.el, plate = live.plate;
        /* orbital-stage force déjà la perte du contexte dans son _dispose ;
           black-hole-stage et forest-stage se contentent de renderer.dispose().
           Un context-loss explicite en plus, après remove(), garantit la
           libération même dans ces deux cas. */
        const canvas = el.querySelector('canvas');
        let gl = null;
        if (canvas) { try { gl = canvas.getContext('webgl2') || canvas.getContext('webgl'); } catch (e) { /* déjà perdu */ } }
        el.remove();
        try { gl && gl.getExtension('WEBGL_lose_context') && gl.getExtension('WEBGL_lose_context').loseContext(); } catch (e) { /* déjà perdu */ }
        setPlateState(plate, 'idle');
        writeHud(plate, 'fps', '—'); writeHud(plate, 'time', '0.0 s'); writeHud(plate, 'phase', '—');
        live = null;
        updateLiveCount();
        resolve();
      };
      if (immediate) { clearTimeout(live.dismissTimer); finish(); }
      else { clearTimeout(live.dismissTimer); live.dismissTimer = setTimeout(finish, 320); }
    });
  }

  /* ── survol / épinglage tactile ────────────────────────────────────────
     Un survol seul ne suffit pas au tactile : un appui épingle la plaque
     (data-tl-pinned), un second la relâche. Les boutons du HUD ne comptent
     jamais comme un appui sur la plaque. */
  function wirePlate(plate, key, slug) {
    plate.addEventListener('pointerenter', e => {
      if (e.pointerType !== 'mouse') return;
      plate.dataset.tlActive = '1';
      mount(plate, key, slug);
    });
    plate.addEventListener('pointerleave', e => {
      if (e.pointerType !== 'mouse') return;
      plate.dataset.tlActive = '0';
      if (plate.dataset.tlPinned === '1') return;
      if (live && live.plate === plate) dismount(false);
    });
    plate.addEventListener('pointerdown', e => {
      if (e.pointerType !== 'touch') return;
      if (e.target.closest('[data-tl-hud]')) return;
      const wasPinned = plate.dataset.tlPinned === '1';
      root.querySelectorAll('[data-tl-plate][data-tl-pinned="1"]').forEach(p => {
        p.dataset.tlPinned = '0'; p.classList.remove('is-pinned'); p.dataset.tlActive = '0';
        if (live && live.plate === p) dismount(false);
      });
      if (!wasPinned) {
        plate.dataset.tlPinned = '1'; plate.classList.add('is-pinned'); plate.dataset.tlActive = '1';
        mount(plate, key, slug);
      }
    });
  }

  root.querySelectorAll('[data-tl-card]').forEach(c => {
    const plate = c.querySelector('[data-tl-plate]');
    if (plate) wirePlate(plate, c.dataset.tlKey, c.dataset.tlSlug);
  });

  /* garde-fous : sortie d'écran, onglet caché, sortie de page */
  const io = new IntersectionObserver(entries => {
    entries.forEach(en => { if (!en.isIntersecting && live && live.plate === en.target) dismount(true); });
  }, { threshold: 0 });
  root.querySelectorAll('[data-tl-plate]').forEach(p => io.observe(p));

  document.addEventListener('visibilitychange', () => { if (document.hidden && live) dismount(true); });
  addEventListener('pagehide', () => { if (live) dismount(true); }, { once: true });

  /* palier : lu au démarrage du renderer, ne peut pas changer à chaud —
     remonter est le seul moyen, en conservant la position de lecture. */
  root.querySelectorAll('[data-tl-quality]').forEach(btn => {
    btn.textContent = currentQuality.toUpperCase();
    btn.addEventListener('click', async () => {
      currentQuality = currentQuality === 'low' ? 'high' : 'low';
      root.querySelectorAll('[data-tl-quality]').forEach(b => b.textContent = currentQuality.toUpperCase());
      if (live) {
        const { plate, key, slug, pos } = live;
        await dismount(true);
        await mount(plate, key, slug);
        if (live) live.pos = pos;
      }
    });
  });

  /* scrub + boutons de phase — fonctionnent même en prefers-reduced-motion,
     où startLoop() ne fait plus avancer pos tout seul. */
  root.querySelectorAll('[data-tl-scrub]').forEach(input => {
    input.addEventListener('input', () => {
      const slug = input.closest('[data-tl-card]').dataset.tlSlug;
      const track = TRACKS[slug];
      if (!live || live.slug !== slug || !track) return;
      live.pos = (parseFloat(input.value) / 100) * track.len;
      live.el.setShot(sampleShot(track.fields, live.pos));
      writeHud(live.plate, 'time', live.pos.toFixed(1) + ' s');
      writeHud(live.plate, 'phase', phaseAt(track.phases, live.pos));
    });
  });
  root.querySelectorAll('[data-tl-phase-jump]').forEach(btn => {
    btn.addEventListener('click', () => {
      const slug = btn.closest('[data-tl-card]').dataset.tlSlug;
      const track = TRACKS[slug];
      if (!live || live.slug !== slug || !track) return;
      live.pos = parseFloat(btn.dataset.tlPhaseJump);
      live.el.setShot(sampleShot(track.fields, live.pos));
      writeHud(live.plate, 'time', live.pos.toFixed(1) + ' s');
      writeHud(live.plate, 'phase', phaseAt(track.phases, live.pos));
    });
  });

  /* ── sommaire / navigation entre écrans (même convention que barba-lab.js) */
  const sections = [...root.querySelectorAll('[data-tl-scr]')];
  const railBtns = [...root.querySelectorAll('[data-lab-sec]')];
  const titleEl = root.querySelector('[data-tl-title]');
  const countEl = root.querySelector('[data-tl-count]');
  const progressEl = root.querySelector('[data-tl-progress]');
  const railDot = root.querySelector('[data-lab-sec-dot]');
  const TITLES = sections.map(s => s.dataset.tlLabel || '');
  let i = 0;

  function open(n) {
    const next = (n + sections.length) % sections.length;
    if (next !== i && live) dismount(true); // changer d'écran démonte toute scène de l'écran quitté
    i = next;
    sections.forEach((s, k) => s.classList.toggle('is-active', k === i));
    if (titleEl) titleEl.textContent = TITLES[i];
    if (countEl) countEl.textContent = String(i + 1).padStart(2, '0') + ' / ' + sections.length;
    if (progressEl) progressEl.style.transform = 'scaleX(' + ((i + 1) / sections.length) + ')';
    if (railDot) railDot.style.transform = 'translateY(' + (i * 26) + 'px)';
    railBtns.forEach((b, k) => { b.style.color = k === i ? '#e2ddd1' : 'rgba(226,221,209,.40)'; });
    scrollTo(0, 0);
  }
  railBtns.forEach((btn, k) => btn.addEventListener('click', () => open(k)));
  root.querySelectorAll('[data-tl-cta]').forEach(btn => {
    btn.addEventListener('click', () => open(parseInt(btn.dataset.tlCta, 10)));
  });
  addEventListener('keydown', e => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') open(i + 1);
    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') open(i - 1);
  });

  open(0);
}
