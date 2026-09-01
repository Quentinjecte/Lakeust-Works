/* welcome-lakeust.js — entrée Vite de '/' : le carrefour Lakeust. Deux
   branches en orbite (ellipse en perspective : sin(angle) porte la
   profondeur, donc échelle/opacité/z-index), sélection au survol/focus,
   rideau de sortie avant de quitter la page vers une branche ouverte. */

import gsap from 'gsap';
import { initPage } from '../core/page-systems.js';
import { bootI18n } from '../core/i18n.js';

const root = document.querySelector('[data-wl-root]');
if (root) boot(root);

/* Retour arrière depuis une page externe (ex. /travaux) : le navigateur
   restaure ce document depuis le bfcache au lieu de le recharger — mais il a
   déjà figé rAF et coupé les listeners au moment du pagehide (voir la fin de
   boot()), donc sans ce ré-amorçage l'orbite reste inerte et les clics sur
   les nœuds ne répondent plus, jusqu'à un F5. `persisted` distingue une
   vraie restauration bfcache d'un pageshow de premier chargement normal (où
   boot() a déjà tourné une fois juste au-dessus). */
if (root) {
  addEventListener('pageshow', e => { if (e.persisted) boot(root); });
}

function boot(root) {
  const stage = root.querySelector('[data-orbit]');
  const nodes = Array.from(root.querySelectorAll('[data-orbit] [data-node]'));
  const idlePanel = root.querySelector('[data-panel="idle"]');
  const branchPanels = Array.from(root.querySelectorAll('[data-panel="branch"]'));
  const exitPlate = root.querySelector('[data-exit-plate]');
  const exitName = root.querySelector('[data-exit-name]');
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

  let angle = 0, speedMul = 1, sel = -1, over = false, last = 0, raf = 0;

  /* ── survol / sélection de branche ───────────────────────────────────── */
  function showPanel(target) {
    [idlePanel, ...branchPanels].forEach(p => {
      if (!p) return;
      if (p === target) {
        p.style.visibility = 'visible';
        p.style.pointerEvents = 'auto';
        gsap.to(p, { opacity: 1, y: 0, duration: 0.5, ease: 'power3.out', overwrite: true });
      } else {
        gsap.to(p, {
          opacity: 0, y: 8, duration: 0.32, ease: 'power2.in', overwrite: true,
          onComplete: () => { p.style.visibility = 'hidden'; p.style.pointerEvents = 'none'; }
        });
      }
    });
  }

  function select(i) {
    if (i === sel || !branchPanels[i]) return;
    sel = i;
    showPanel(branchPanels[i]);
  }

  const onOver = e => {
    const a = e.target.closest && e.target.closest('[data-node]');
    if (a) select(+a.dataset.node);
  };
  const onEnter = () => { over = true; };
  const onLeave = () => { over = false; };
  if (stage) {
    stage.addEventListener('mouseover', onOver);
    stage.addEventListener('focusin', onOver);
    stage.addEventListener('mouseenter', onEnter);
    stage.addEventListener('mouseleave', onLeave);
  }

  /* ── navigation : rideau de sortie avant de quitter la page ─────────── */
  const onClick = e => {
    const a = e.target.closest && e.target.closest('[data-node]');
    if (!a) return;
    e.preventDefault();
    select(+a.dataset.node);
    const href = a.getAttribute('href');
    if (!a.hasAttribute('data-open') || !href) return;
    if (exitName) exitName.textContent = a.dataset.label || '';
    if (exitPlate) {
      exitPlate.style.visibility = 'visible';
      exitPlate.style.pointerEvents = 'auto';
      gsap.to(exitPlate, {
        opacity: 1, duration: 0.5, ease: 'power2.inOut',
        onComplete: () => { window.location.href = href; }
      });
    } else {
      window.location.href = href;
    }
  };
  root.addEventListener('click', onClick);

  /* ── FR / EN ──────────────────────────────────────────────────────────── */
  bootI18n();

  /* ── orbite ───────────────────────────────────────────────────────────── */
  function frame(now) {
    if (!stage || !nodes.length) return;
    const dt = last ? Math.min(50, now - last) : 16;
    last = now;

    const target = over ? 0 : 1;
    speedMul += (target - speedMul) * Math.min(1, dt / 260);
    if (!reduced) angle += dt * 0.00024 * speedMul;

    const w = stage.clientWidth, h = stage.clientHeight;
    if (w) {
      const rx = Math.min(w * 0.37, 336), ry = Math.max(48, rx * 0.30);
      const cx = w / 2, cy = h / 2;
      const step = (Math.PI * 2) / nodes.length;

      nodes.forEach((el, i) => {
        const a = angle + i * step + Math.PI / 2;
        const d = (Math.sin(a) + 1) / 2;
        const x = cx + Math.cos(a) * rx, y = cy + Math.sin(a) * ry;
        const on = i === sel;
        const s = (0.80 + 0.28 * d) * (on ? 1.16 : 1);
        el.style.transform = 'translate3d(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0) translate(-50%,-50%) scale(' + s.toFixed(3) + ')';
        const base = 0.34 + 0.62 * d;
        el.style.opacity = (on ? 1 : (sel >= 0 ? base * 0.46 : base)).toFixed(3);
        el.style.zIndex = String(d > 0.5 ? 6 + Math.round(d * 4) : 1 + Math.round(d * 4));
      });
    }
    if (!reduced) raf = requestAnimationFrame(frame);
  }
  raf = requestAnimationFrame(frame);

  initPage(root);

  addEventListener('pagehide', () => {
    cancelAnimationFrame(raf);
    if (stage) {
      stage.removeEventListener('mouseover', onOver);
      stage.removeEventListener('focusin', onOver);
      stage.removeEventListener('mouseenter', onEnter);
      stage.removeEventListener('mouseleave', onLeave);
    }
    root.removeEventListener('click', onClick);
  }, { once: true });
}
