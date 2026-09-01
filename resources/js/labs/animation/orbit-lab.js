/* orbit-lab.js — le carrefour du site (voir pages/welcome-lakeust.js) porté
   dans Animation Lab sous la même interface que anim.js :
   create(stage, ctx) -> { play, reset, resize, destroy }. Contrairement aux
   neuf autres concepts (une passe gsap qui se rejoue), l'orbite tourne en
   continu : play() s'assure juste que la boucle rAF vit, reset() ramène la
   sélection sur l'écran idle et relance l'angle à zéro. Pas de FR/EN ici —
   le switch (welcome.blade.php lignes 43-53) n'a pas été isolé avec le
   reste, l'anglais reste simplement masqué (voir css/labs/animation-lab.css). */

let MOT = null;
export function bind(mod) { MOT = mod; return mod; }
const G = () => (MOT && MOT.gsap) || window.gsap || null;

export function create(stage, ctx = {}) {
  const orbitEl = stage.querySelector('[data-orbit]');
  const nodes = Array.from(stage.querySelectorAll('[data-orbit] [data-node]'));
  const idlePanel = stage.querySelector('[data-panel="idle"]');
  const branchPanels = Array.from(stage.querySelectorAll('[data-panel="branch"]'));
  const exitPlate = stage.querySelector('[data-exit-plate]');
  const exitName = stage.querySelector('[data-exit-name]');
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

  let angle = 0, speedMul = 1, sel = -1, over = false, last = 0, raf = 0;

  function showPanel(target) {
    const g = G();
    [idlePanel, ...branchPanels].forEach(p => {
      if (!p) return;
      if (p === target) {
        p.style.visibility = 'visible';
        p.style.pointerEvents = 'auto';
        if (g) g.to(p, { opacity: 1, y: 0, duration: .5, ease: 'power3.out', overwrite: true });
        else p.style.opacity = 1;
      } else if (g) {
        g.to(p, {
          opacity: 0, y: 8, duration: .32, ease: 'power2.in', overwrite: true,
          onComplete: () => { p.style.visibility = 'hidden'; p.style.pointerEvents = 'none'; }
        });
      } else {
        p.style.opacity = 0; p.style.visibility = 'hidden'; p.style.pointerEvents = 'none';
      }
    });
  }

  function select(idx) {
    if (idx === sel || !branchPanels[idx]) return;
    sel = idx;
    showPanel(branchPanels[idx]);
  }

  function unselect() {
    sel = -1;
    showPanel(idlePanel);
  }

  const onOver = e => {
    const a = e.target.closest && e.target.closest('[data-node]');
    if (a) select(+a.dataset.node);
  };
  const onEnter = () => { over = true; };
  const onLeave = () => { over = false; };
  if (orbitEl) {
    orbitEl.addEventListener('mouseover', onOver);
    orbitEl.addEventListener('focusin', onOver);
    orbitEl.addEventListener('mouseenter', onEnter);
    orbitEl.addEventListener('mouseleave', onLeave);
  }

  const onClick = e => {
    const a = e.target.closest && e.target.closest('[data-node]');
    if (!a) return;
    e.preventDefault();
    select(+a.dataset.node);
    const href = a.getAttribute('href');
    if (!a.hasAttribute('data-open') || !href) return;
    if (exitName) exitName.textContent = a.dataset.label || '';
    const g = G();
    if (exitPlate && g) {
      exitPlate.style.visibility = 'visible';
      exitPlate.style.pointerEvents = 'auto';
      g.to(exitPlate, {
        opacity: 1, duration: .5, ease: 'power2.inOut',
        onComplete: () => { window.location.href = href; }
      });
    } else {
      window.location.href = href;
    }
  };
  stage.addEventListener('click', onClick);

  function frame(now) {
    if (!orbitEl || !nodes.length) { raf = 0; return; }
    const dt = last ? Math.min(50, now - last) : 16;
    last = now;

    const target = over ? 0 : 1;
    speedMul += (target - speedMul) * Math.min(1, dt / 260);
    if (!reduced) angle += dt * 0.00024 * speedMul;

    const w = orbitEl.clientWidth, h = orbitEl.clientHeight;
    if (w) {
      const rx = Math.min(w * 0.37, 336), ry = Math.max(48, rx * 0.30);
      const cx = w / 2, cy = h / 2;
      const step = (Math.PI * 2) / nodes.length;

      nodes.forEach((el, idx) => {
        const a = angle + idx * step + Math.PI / 2;
        const d = (Math.sin(a) + 1) / 2;
        const x = cx + Math.cos(a) * rx, y = cy + Math.sin(a) * ry;
        const on = idx === sel;
        const s = (0.80 + 0.28 * d) * (on ? 1.16 : 1);
        el.style.transform = 'translate3d(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0) translate(-50%,-50%) scale(' + s.toFixed(3) + ')';
        const base = 0.34 + 0.62 * d;
        el.style.opacity = (on ? 1 : (sel >= 0 ? base * 0.46 : base)).toFixed(3);
        el.style.zIndex = String(d > 0.5 ? 6 + Math.round(d * 4) : 1 + Math.round(d * 4));
      });
    }
    raf = requestAnimationFrame(frame);
  }

  function play() {
    if (raf) return;
    last = 0;
    raf = requestAnimationFrame(frame);
  }

  function stop() {
    cancelAnimationFrame(raf);
    raf = 0;
  }

  function reset() {
    unselect();
    if (exitPlate) {
      const g = G();
      if (g) g.killTweensOf(exitPlate);
      exitPlate.style.opacity = 0;
      exitPlate.style.visibility = 'hidden';
      exitPlate.style.pointerEvents = 'none';
    }
    angle = 0;
    play();
  }

  function resize() { /* la géométrie est relue depuis clientWidth/Height à chaque frame */ }

  function destroy() {
    stop();
    if (orbitEl) {
      orbitEl.removeEventListener('mouseover', onOver);
      orbitEl.removeEventListener('focusin', onOver);
      orbitEl.removeEventListener('mouseenter', onEnter);
      orbitEl.removeEventListener('mouseleave', onLeave);
    }
    stage.removeEventListener('click', onClick);
  }

  return { play, reset, resize, destroy };
}
