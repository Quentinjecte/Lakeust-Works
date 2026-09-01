/* animation-lab.js — entrée Vite de /animation-lab, page autonome
   (pas de layout partagé) : onze scènes, une visible à la fois (même
   convention que labs/barba/barba-lab.js / labs/three/three-lab.js). Le
   catalogue, premier écran, liste les dix qui suivent. Les neuf suivantes
   rejouent un concept d'animation d'entrée de section (voir anim.js) ; la
   dixième est le carrefour du site (Orbite, voir orbit-lab.js).

   Une seule instance vit à la fois : elle est fabriquée à l'ouverture de sa
   section et détruite quand on la quitte, exactement comme le composant DC
   d'origine (attach/detach autour de S.create() / O.create()). */

import * as M from '../../animation/motion.js';
import * as S from './anim.js';
import * as O from './orbit-lab.js';

/* Le catalogue est le premier écran (data-sa-scr="0" dans le gabarit Blade,
   même convention que les autres labs) ; les neuf concepts le suivent aux
   index 1-9, donc CONCEPTS[i - 1] — pas CONCEPTS[i] — donne le bon concept
   pour un index d'écran donné. Le dixième écran (data-sa-scr="10") est
   l'orbite du carrefour (voir orbit-lab.js) — pas un concept d'anim.js,
   donc traité à part dans attach()/detach(). */
const CONCEPTS = S.CONCEPTS;
const SCREENS = [{ id: null, n: 'X', name: 'Catalogue', tech: '' }]
  .concat(CONCEPTS)
  .concat([{ id: 'orbit', n: '10', name: 'Orbite', tech: 'DOM · ellipse en perspective · rideau de sortie' }]);
const ORBIT_INDEX = SCREENS.length - 1;

const root = document.querySelector('[data-sa-root]');
if (root) boot();

async function boot() {
  const titleEl = root.querySelector('[data-sa-title]');
  const countEl = root.querySelector('[data-sa-count]');
  const progressEl = root.querySelector('[data-sa-progress]');
  const railDot = root.querySelector('[data-lab-sec-dot]');
  const railBtns = [...root.querySelectorAll('[data-lab-sec]')];
  const sections = [...root.querySelectorAll('[data-sa-scr]')];

  try {
    await M.boot();
  } catch (e) { console.warn('animation-lab: motion boot failed', e); return; }
  S.bind(M);
  O.bind(M);

  const anim = { speed: 1 };
  const TRIGGER = 'ouverture';
  let i = 0, current = null, played = false, timer = 0;

  function stage(k) { return sections[k].querySelector('[data-stage]'); }

  function attach() {
    if (i === ORBIT_INDEX) {
      const el = sections[i];
      if (!el) return;
      const ctrl = O.create(el, anim);
      if (!ctrl) return;
      current = ctrl;
      ctrl.reset();
      played = false;
      if (TRIGGER === 'ouverture') timer = setTimeout(play, 280);
      return;
    }
    const c = CONCEPTS[i - 1];
    if (!c) return;
    const el = stage(i);
    if (!el) return;
    const ctrl = S.create(c.id, el, anim);
    if (!ctrl) return;
    current = ctrl;
    ctrl.reset();
    played = false;
    if (TRIGGER === 'ouverture') timer = setTimeout(play, 280);
  }

  function detach() {
    clearTimeout(timer);
    if (current) { try { current.destroy(); } catch (e) {} current = null; }
    played = false;
  }

  function live() { return !!current; }

  function play() {
    if (!live()) return;
    current.play();
    played = true;
  }

  function replay() {
    if (!live()) return;
    current.reset();
    timer = setTimeout(play, 60);
  }

  function onResize() {
    if (!live()) return;
    current.resize();
    if (played) current.play(true); else current.reset();
  }

  function open(n) {
    const next = (n + SCREENS.length) % SCREENS.length;
    if (next === i) return replay();
    i = next;
    detach();
    sections.forEach((s, k) => s.classList.toggle('is-active', k === i));
    if (titleEl) titleEl.textContent = SCREENS[i].name;
    if (countEl) countEl.textContent = SCREENS[i].n + ' / ' + ORBIT_INDEX;
    if (progressEl) progressEl.style.transform = 'scaleX(' + ((i + 1) / SCREENS.length) + ')';
    if (railDot) railDot.style.transform = 'translateY(' + (i * 26) + 'px)';
    railBtns.forEach((b, k) => { b.style.color = k === i ? '#e2ddd1' : 'rgba(226,221,209,.40)'; });
    scrollTo(0, 0);
    attach();
  }

  root.querySelectorAll('[data-sa-replay]').forEach(btn => btn.addEventListener('click', replay));
  root.querySelectorAll('[data-sa-registre-open]').forEach(btn => {
    btn.addEventListener('click', () => open(parseInt(btn.dataset.saRegistreOpen, 10)));
  });
  railBtns.forEach((btn, k) => btn.addEventListener('click', () => open(k)));

  addEventListener('keydown', e => {
    if (e.metaKey || e.ctrlKey || e.altKey) return;
    if (e.key === '0') { e.preventDefault(); return open(ORBIT_INDEX); }
    const n = parseInt(e.key, 10);
    if (n >= 1 && n <= 9) { e.preventDefault(); return open(n); }
    if (e.key.toLowerCase() === 'r') { e.preventDefault(); return replay(); }
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); return open(i + 1); }
    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); return open(i - 1); }
  });

  let t = 0;
  const ro = new ResizeObserver(() => { clearTimeout(t); t = setTimeout(onResize, 180); });
  ro.observe(root);

  attach();
}
