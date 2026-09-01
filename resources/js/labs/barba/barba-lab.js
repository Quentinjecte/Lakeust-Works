/* barba-lab.js — entrée Vite de /barba-lab, page autonome (pas de layout
   partagé, pas de core/app.js) : documentation interactive du système de
   transitions Barba. Dix scènes, une visible à la fois (même convention que
   les autres labs : classe .is-active), chacune une démonstration autonome
   sur un mock de navigateur — aucune vraie navigation n'a lieu ici.

   Les scènes 01-04 et 07 reflètent le code réellement chargé sur le site
   (voir core/barba-transitions.js) ; 05, 08, 09, 10 illustrent des
   techniques Barba que le site n'utilise pas encore — leur copie le dit
   explicitement. */

/* Le catalogue est le premier écran (data-blab-scr="0" dans le gabarit
   Blade, même convention que les autres labs) ; les dix scènes le suivent
   aux index 1-10. */
const LABS = [
  { n: 'X', title: 'Catalogue' },
  { n: '01', title: 'Voile' },
  { n: '02', title: 'Horizon' },
  { n: '03', title: 'Signal' },
  { n: '04', title: 'Éclipse' },
  { n: '05', title: 'Synchrone' },
  { n: '06', title: 'Règles de route' },
  { n: '07', title: 'Vues' },
  { n: '08', title: 'Élément partagé' },
  { n: '09', title: 'Préchargement' },
  { n: '10', title: 'Toile persistante' }
];

/* Reflète exactement TRANSITIONS dans resources/js/barba-transitions.js —
   si ce fichier change de règles, cette table doit suivre. */
const RULES = [
  { code: "{ from:['about','works'], to:['about','works'] }", name: 'horizon', from: ['about', 'works'], to: ['about', 'works'] },
  { code: "{ from:['works','project'], to:['works','project'] }", name: 'signal', from: ['works', 'project'], to: ['works', 'project'] },
  { code: "{ from:['about','project'], to:['about','project'] }", name: 'eclipse', from: ['about', 'project'], to: ['about', 'project'] },
  { code: '{ } — repli', name: 'voile' }
];
const NS_LABEL = { about: 'à propos', works: 'travaux', project: 'projet' };

const root = document.querySelector('[data-blab-root]');
if (root) boot();

function boot() {
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const busy = {};
  const timers = [];
  const wait = ms => new Promise(r => timers.push(setTimeout(r, ms)));

  const titleEl = root.querySelector('[data-blab-title]');
  const countEl = root.querySelector('[data-blab-count]');
  const progressEl = root.querySelector('[data-blab-progress]');
  const railDot = root.querySelector('[data-lab-sec-dot]');
  const railBtns = [...root.querySelectorAll('[data-lab-sec]')];
  const sections = [...root.querySelectorAll('[data-blab-scr]')];

  let i = 0;

  function card(kind) { return root.querySelector('[data-demo="' + kind + '"]'); }

  function anim(el, frames, ms, easing) {
    if (!el) return null;
    return el.animate(frames, { duration: ms, easing: easing || 'cubic-bezier(.32,.72,.24,1)', fill: 'forwards' });
  }

  /* l'échange de conteneur : ce que Barba fait entre leave et enter. Sans
     effet sur les scènes 06 (règles) et 07 (vues), qui n'ont pas de mock de
     page — utile car run() y recourt directement en repli reduced-motion. */
  function swap(cardEl) {
    const a = cardEl.querySelector('[data-page="a"]');
    const b = cardEl.querySelector('[data-page="b"]');
    if (!a || !b) return null;
    const bOn = b.style.opacity === '1';
    a.style.opacity = bOn ? '1' : '0';
    b.style.opacity = bOn ? '0' : '1';
    const url = cardEl.querySelector('[data-el="url"]');
    if (url) url.textContent = bOn ? '/travaux' : '/projet';
    return bOn ? a : b;
  }

  function incoming(cardEl) {
    const a = cardEl.querySelector('[data-page="a"]');
    const b = cardEl.querySelector('[data-page="b"]');
    if (!a || !b) return null;
    return b.style.opacity === '1' ? b : a;
  }

  async function run(kind, fn) {
    const cardEl = card(kind);
    if (!cardEl || busy[kind]) return;
    if (reduced) {
      /* fondu bref plutôt qu'un cut muet — même raisonnement que
         instantLeave/instantEnter dans barba-transitions.js : un swap sans
         aucune rétroaction visuelle ressemble à un bouton qui ne répond pas. */
      const out = incoming(cardEl);
      if (out) anim(out, [{ opacity: 1 }, { opacity: 0 }], 150);
      await new Promise(r => setTimeout(r, 150));
      const next = swap(cardEl);
      if (next) anim(next, [{ opacity: 0 }, { opacity: 1 }], 150);
      return;
    }
    busy[kind] = true;
    try { await fn(cardEl); } finally { busy[kind] = false; }
  }

  /* ── 01 · Voile ──────────────────────────────────────────────────────── */
  async function voile(cardEl) {
    const veil = cardEl.querySelector('[data-el="veil"]');
    const out = incoming(cardEl);
    anim(veil, [{ opacity: 0 }, { opacity: 0.8 }], 520, 'cubic-bezier(.4,.02,.6,.5)');
    anim(out, [{ filter: 'blur(0px)', transform: 'none' }, { filter: 'blur(6px)', transform: 'translateY(-14px)' }], 520);
    await wait(520);
    const next = swap(cardEl);
    anim(veil, [{ opacity: 0.8 }, { opacity: 0 }], 680);
    anim(next, [{ opacity: 0, transform: 'translateY(18px)', filter: 'blur(5px)' }, { opacity: 1, transform: 'none', filter: 'blur(0px)' }], 680);
    await wait(700);
    veil.getAnimations().forEach(a => a.cancel());
    veil.style.opacity = '0';
  }

  /* ── 02 · Horizon ────────────────────────────────────────────────────── */
  async function horizon(cardEl) {
    const top = cardEl.querySelector('[data-el="panelTop"]');
    const bottom = cardEl.querySelector('[data-el="panelBottom"]');
    const line = cardEl.querySelector('[data-el="line"]');
    anim(line, [{ transform: 'scaleX(0)', opacity: 0 }, { transform: 'scaleX(1)', opacity: 1 }], 260, 'cubic-bezier(.22,1,.36,1)');
    await wait(200);
    anim(top, [{ transform: 'translateY(-101%)' }, { transform: 'translateY(0)' }], 460);
    anim(bottom, [{ transform: 'translateY(101%)' }, { transform: 'translateY(0)' }], 460);
    await wait(460);
    const next = swap(cardEl);
    anim(top, [{ transform: 'translateY(0)' }, { transform: 'translateY(-101%)' }], 620);
    anim(bottom, [{ transform: 'translateY(0)' }, { transform: 'translateY(101%)' }], 620);
    anim(line, [{ opacity: 1 }, { opacity: 0 }], 620, 'cubic-bezier(.4,0,.8,.4)');
    anim(next, [{ opacity: 0 }, { opacity: 1 }], 380);
    await wait(640);
    line.getAnimations().forEach(a => a.cancel());
    line.style.opacity = '0';
    line.style.transform = 'scaleX(0)';
  }

  /* ── 03 · Signal ─────────────────────────────────────────────────────── */
  async function signal(cardEl) {
    const dim = cardEl.querySelector('[data-el="dim"]');
    const scan = cardEl.querySelector('[data-el="scan"]');
    const readout = cardEl.querySelector('[data-el="readout"]');
    const route = cardEl.querySelector('[data-el="route"]');
    const toB = cardEl.querySelector('[data-page="b"]').style.opacity !== '1';
    if (route) route.textContent = toB ? 'works → project' : 'project → works';
    anim(dim, [{ opacity: 0 }, { opacity: 1 }], 300, 'cubic-bezier(.4,0,.7,.3)');
    anim(scan, [{ opacity: 0.9, transform: 'translateY(-110%)' }, { opacity: 0.9, transform: 'translateY(400%)' }], 620, 'linear');
    anim(readout, [{ opacity: 0, transform: 'translateY(8px)' }, { opacity: 1, transform: 'none' }], 420);
    await wait(620);
    const next = swap(cardEl);
    anim(scan, [{ opacity: 0.6, transform: 'translateY(-110%)' }, { opacity: 0.6, transform: 'translateY(400%)' }], 420, 'linear');
    anim(dim, [{ opacity: 1 }, { opacity: 0 }], 540);
    anim(readout, [{ opacity: 1 }, { opacity: 0 }], 300);
    anim(next, [{ opacity: 0.25 }, { opacity: 1 }], 540, 'steps(5, end)');
    await wait(560);
    [dim, scan, readout].forEach(el => { el.getAnimations().forEach(a => a.cancel()); el.style.opacity = '0'; });
  }

  /* ── 04 · Éclipse — la couverture vient d'un box-shadow à écartement
     constant : on anime la TAILLE de l'ouverture, jamais un scale. ───────── */
  async function eclipse(cardEl) {
    const hole = cardEl.querySelector('[data-el="hole"]');
    const ring = cardEl.querySelector('[data-el="ring"]');
    anim(hole, [{ width: '132%' }, { width: '0%' }], 560, 'cubic-bezier(.5,.05,.35,1)');
    anim(ring, [{ width: '132%', opacity: 0 }, { width: '6%', opacity: 1 }], 560, 'cubic-bezier(.5,.05,.35,1)');
    await wait(560);
    const next = swap(cardEl);
    await wait(200);
    anim(next, [{ transform: 'scale(1.05)' }, { transform: 'none' }], 900, 'cubic-bezier(.16,1,.3,1)');
    anim(hole, [{ width: '0%' }, { width: '138%' }], 720, 'cubic-bezier(.16,1,.3,1)');
    anim(ring, [{ width: '6%', opacity: 1 }, { width: '138%', opacity: 0 }], 720, 'cubic-bezier(.16,1,.3,1)');
    await wait(740);
    hole.getAnimations().forEach(a => a.cancel());
    hole.style.width = '132%';
    ring.getAnimations().forEach(a => a.cancel());
    ring.style.opacity = '0';
    ring.style.width = '132%';
  }

  /* ── 05 · Synchrone (concept) — sync:true, les deux conteneurs coexistent */
  async function sync(cardEl) {
    const out = incoming(cardEl);
    const a = cardEl.querySelector('[data-page="a"]');
    const b = cardEl.querySelector('[data-page="b"]');
    const next = out === a ? b : a;
    const seam = cardEl.querySelector('[data-el="seam"]');
    next.style.opacity = '1';
    next.style.transform = 'translateX(100%)';
    anim(seam, [{ opacity: 0 }, { opacity: 1 }, { opacity: 0 }], 720, 'linear');
    anim(out, [{ transform: 'translateX(0)' }, { transform: 'translateX(-100%)' }], 720, 'cubic-bezier(.62,.02,.28,1)');
    anim(next, [{ transform: 'translateX(100%)' }, { transform: 'translateX(0)' }], 720, 'cubic-bezier(.62,.02,.28,1)');
    await wait(740);
    out.getAnimations().forEach(x => x.cancel());
    next.getAnimations().forEach(x => x.cancel());
    out.style.opacity = '0';
    out.style.transform = 'none';
    next.style.transform = 'none';
    const url = cardEl.querySelector('[data-el="url"]');
    if (url) url.textContent = next === b ? '/projet' : '/travaux';
  }

  /* ── 06 · Règles de route ────────────────────────────────────────────── */
  const ns = { from: 'works', to: 'project' };

  function ruleMatch() {
    return RULES.find(r => r.from && r.to && r.from.includes(ns.from) && r.to.includes(ns.to)) || RULES[RULES.length - 1];
  }

  function renderRules() {
    const active = ruleMatch();
    const box = root.querySelector('[data-blab-rules]');
    if (box) {
      box.innerHTML = '';
      RULES.forEach(r => {
        const on = r === active;
        const row = document.createElement('div');
        row.style.cssText = 'display:flex;align-items:center;gap:14px;padding:16px 18px;border-bottom:1px solid rgba(233,233,237,.06);transition:background .3s;background:' + (on ? 'rgba(145,132,217,.10)' : 'transparent');
        row.innerHTML =
          '<span style="flex:none;width:6px;height:6px;border-radius:50%;background:#9184d9;transition:opacity .3s;opacity:' + (on ? '1' : '0.12') + '"></span>' +
          '<span style="flex:1;font-family:ui-monospace,monospace;font-size:11px;line-height:1.6;transition:color .3s;color:' + (on ? '#e2ddd1' : 'rgba(226,221,209,.42)') + '">' + r.code + '</span>' +
          '<span style="flex:none;font-size:10px;letter-spacing:.22em;text-transform:uppercase;transition:color .3s;color:' + (on ? '#b3a9e6' : 'rgba(226,221,209,.26)') + '">' + r.name + '</span>';
        box.appendChild(row);
      });
    }
    const matched = root.querySelector('[data-blab-matched]');
    if (matched) matched.textContent = active.name;
    const pair = root.querySelector('[data-blab-matched-pair]');
    if (pair) pair.textContent = ns.from + ' → ' + ns.to;
    ['from', 'to'].forEach(kind => {
      root.querySelectorAll('[data-blab-ns-pick="' + kind + '"]').forEach(btn => {
        const on = ns[kind] === btn.dataset.blabNsVal;
        btn.style.background = on ? 'rgba(145,132,217,.18)' : 'transparent';
        btn.style.color = on ? '#e2ddd1' : 'rgba(226,221,209,.52)';
      });
    });
  }

  root.querySelectorAll('[data-blab-ns-pick]').forEach(btn => {
    btn.addEventListener('click', () => { ns[btn.dataset.blabNsPick] = btn.dataset.blabNsVal; renderRules(); });
  });

  /* ── 07 · Vues — cycle de vie tracé hook par hook ───────────────────── */
  let t0 = 0;
  function stamp() { if (!t0) t0 = performance.now(); return (Math.round(performance.now() - t0) / 1000).toFixed(2) + 's'; }

  let viewNs = 'works', viewCount = 0;
  const logEl = root.querySelector('[data-blab-log]');
  const viewNsEl = root.querySelector('[data-blab-view-ns]');
  const viewCountEl = root.querySelector('[data-blab-view-count]');
  const sceneStateEl = root.querySelector('[data-blab-scene-state]');
  let log = [];

  function renderLog() {
    if (!logEl) return;
    logEl.innerHTML = '';
    log.slice(-8).forEach(l => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex;gap:12px;padding:7px 0;border-bottom:1px solid rgba(233,233,237,.05)';
      row.innerHTML =
        '<span style="flex:none;width:56px;color:rgba(226,221,209,.28);font-variant-numeric:tabular-nums">' + l.t + '</span>' +
        '<span style="flex:none;width:104px;color:#9184d9">' + l.hook + '</span>' +
        '<span style="flex:1;color:rgba(226,221,209,.60)">' + l.msg + '</span>';
      logEl.appendChild(row);
    });
  }

  async function views(cardEl) {
    const to = viewNs === 'works' ? 'project' : 'works';
    const from = viewNs;
    const scene = cardEl.querySelector('[data-el="scene"]');
    const push = (hook, msg) => { log.push({ t: stamp(), hook, msg }); renderLog(); };
    log = []; renderLog();
    push('before', 'lien intercepté — barba.go(' + to + ')');
    await wait(220);
    push('beforeLeave', 'dispose() — abonnés relâchés');
    if (sceneStateEl) sceneStateEl.textContent = 'Scène démontée — 0 abonné';
    if (scene) { scene.style.animation = 'none'; scene.style.opacity = '.25'; }
    await wait(320);
    /* pas de mock data-page ici : cette scène illustre le minutage des hooks,
       pas un échange visuel de page (voir 01-04 pour ça). */
    push('afterLeave', 'conteneur retiré du document');
    await wait(220);
    push('beforeEnter', 'scrollTo(0,0) — nouvelles bornes');
    await wait(240);
    push('afterEnter', 'mount(' + to + ') — socle réinstancié');
    viewNs = to; viewCount++;
    if (viewNsEl) viewNsEl.textContent = viewNs;
    if (viewCountEl) viewCountEl.textContent = viewCount + ' navigation' + (viewCount > 1 ? 's' : '');
    if (sceneStateEl) sceneStateEl.textContent = 'Scène montée — 1 abonné';
    if (scene) { scene.style.animation = 'lab-spin 3.4s linear infinite'; scene.style.opacity = '1'; }
    await wait(260);
    push('after', 'ScrollTrigger.refresh() — ' + from + ' → ' + to);
  }

  /* ── 08 · Élément partagé (concept) — FLIP vignette → image de tête ──── */
  function flip(cardEl) {
    const thumb = cardEl.querySelector('[data-el="thumb"]');
    const hero = cardEl.querySelector('[data-el="hero"]');
    const heroText = cardEl.querySelector('[data-el="heroText"]');
    const a = cardEl.querySelector('[data-page="a"]');
    const b = cardEl.querySelector('[data-page="b"]');
    if (b.style.opacity === '1') return Promise.resolve();
    const t = thumb.getBoundingClientRect();
    b.style.opacity = '1';
    const h = hero.getBoundingClientRect();
    const url = cardEl.querySelector('[data-el="url"]');
    if (url) url.textContent = '/projet';
    anim(a, [{ opacity: 1 }, { opacity: 0 }], 320);
    anim(hero, [
      { transform: 'translate(' + (t.left - h.left) + 'px,' + (t.top - h.top) + 'px) scale(' + (t.width / h.width) + ',' + (t.height / h.height) + ')', transformOrigin: '0 0' },
      { transform: 'none', transformOrigin: '0 0' }
    ], 760, 'cubic-bezier(.16,1,.3,1)');
    anim(heroText, [{ opacity: 0, transform: 'translateY(14px)' }, { opacity: 1, transform: 'none' }], 520);
    return wait(780);
  }

  function resetFlip() {
    const cardEl = card('flip');
    if (!cardEl) return;
    const a = cardEl.querySelector('[data-page="a"]');
    const b = cardEl.querySelector('[data-page="b"]');
    const hero = cardEl.querySelector('[data-el="hero"]');
    const heroText = cardEl.querySelector('[data-el="heroText"]');
    [a, b, hero, heroText].forEach(el => el.getAnimations().forEach(x => x.cancel()));
    b.style.opacity = '0';
    a.style.opacity = '1';
    hero.style.transform = 'none';
    heroText.style.opacity = '0';
    const url = cardEl.querySelector('[data-el="url"]');
    if (url) url.textContent = '/travaux';
  }
  root.querySelector('[data-blab-reset-flip]')?.addEventListener('click', resetFlip);
  root.querySelector('[data-el="thumb"]')?.addEventListener('click', () => run('flip', c => flip(c)));

  /* ── 09 · Préchargement (concept) — le survol chauffe le cache ───────── */
  const pre = {
    cold: { state: 'inactif', fill: 0, note: 'sans greffon — requête au clic' },
    warm: { state: 'inactif', fill: 0, note: 'prefetch au survol' }
  };

  function renderPrefetch(key) {
    const box = root.querySelector('[data-blab-prefetch="' + key + '"]');
    if (!box) return;
    const p = pre[key];
    const stateEl = box.querySelector('[data-blab-link-state]');
    const fillEl = box.querySelector('[data-blab-link-fill]');
    const noteEl = box.querySelector('[data-blab-link-note]');
    if (stateEl) { stateEl.textContent = p.state; stateEl.style.color = p.state === 'en cache' ? '#9184d9' : (p.state === 'inactif' ? 'rgba(226,221,209,.30)' : '#b3a9e6'); }
    if (fillEl) fillEl.style.transform = 'scaleX(' + p.fill + ')';
    if (noteEl) noteEl.textContent = p.note;
  }

  function hoverLink(key) {
    if (key !== 'warm' || pre.warm.state === 'en cache') return;
    pre.warm = { state: 'requête…', fill: 0.15, note: 'GET /projet — au survol' };
    renderPrefetch('warm');
    timers.push(setTimeout(() => { pre.warm = { state: 'en cache', fill: 1, note: 'réponse gardée par barba.cache' }; renderPrefetch('warm'); }, 280));
  }

  function goLink(key) {
    const cached = key === 'warm' && pre.warm.state === 'en cache';
    const ms = cached ? 0 : 240;
    const waitEl = root.querySelector('[data-blab-wait]');
    const waitFromEl = root.querySelector('[data-blab-wait-from]');
    if (waitEl) waitEl.textContent = cached ? '0 ms' : '240 ms';
    if (waitFromEl) waitFromEl.textContent = cached ? 'cache chaud — peinture seule' : 'requête au clic';
    if (!cached) {
      pre[key] = { ...pre[key], state: 'requête…', fill: 0.4, note: 'attente réseau ' + ms + ' ms' };
      renderPrefetch(key);
      timers.push(setTimeout(() => { pre[key] = { ...pre[key], state: 'reçue', fill: 1, note: 'transition démarrée après ' + ms + ' ms' }; renderPrefetch(key); }, ms));
    }
  }

  root.querySelectorAll('[data-blab-link]').forEach(btn => {
    const key = btn.dataset.blabLink;
    btn.addEventListener('mouseenter', () => hoverLink(key));
    btn.addEventListener('click', () => goLink(key));
  });

  /* ── 10 · Toile persistante (concept) ─────────────────────────────────── */
  async function canvas(cardEl) {
    const out = incoming(cardEl);
    anim(out, [{ opacity: 1, transform: 'none' }, { opacity: 0, transform: 'translateY(-12px)' }], 380);
    await wait(380);
    const next = swap(cardEl);
    out.style.transform = 'none';
    anim(next, [{ opacity: 0, transform: 'translateY(14px)' }, { opacity: 1, transform: 'none' }], 560);
    await wait(560);
  }

  /* ── câblage des boutons « Naviguer » ──────────────────────────────── */
  const PLAYERS = { voile, horizon, signal, eclipse, sync, views, canvas };
  root.querySelectorAll('[data-blab-play]').forEach(btn => {
    const kind = btn.dataset.blabPlay;
    const fn = PLAYERS[kind];
    if (fn) btn.addEventListener('click', () => run(kind, fn));
  });

  /* ── navigation entre scènes ─────────────────────────────────────────── */
  function open(n) {
    i = (n + LABS.length) % LABS.length;
    sections.forEach((s, k) => s.classList.toggle('is-active', k === i));
    if (titleEl) titleEl.textContent = LABS[i].title;
    if (countEl) countEl.textContent = LABS[i].n + ' / ' + LABS.length;
    if (progressEl) progressEl.style.transform = 'scaleX(' + ((i + 1) / LABS.length) + ')';
    if (railDot) railDot.style.transform = 'translateY(' + (i * 26) + 'px)';
    railBtns.forEach((b, k) => { b.style.color = k === i ? '#e2ddd1' : 'rgba(226,221,209,.40)'; });
    if (i === 6) renderRules();
    scrollTo(0, 0);
  }

  railBtns.forEach((btn, k) => btn.addEventListener('click', () => open(k)));
  root.querySelectorAll('[data-blab-cta]').forEach(btn => {
    btn.addEventListener('click', () => open(parseInt(btn.dataset.blabCta, 10)));
  });
  addEventListener('keydown', e => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') open(i + 1);
    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') open(i - 1);
  });

  open(0);
}
