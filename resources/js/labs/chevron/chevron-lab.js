/* labs/chevron-lab.js — entrée Vite de /chevron-lab, page autonome (pas de
   layout partagé, pas d'app.js) : quatre bandes en chevron partant d'un même
   apex (voir labs/chevron.js pour la géométrie). Survoler une bande la
   distingue des trois autres — atténuées — et fait glisser sa mosaïque de
   tuiles pour révéler l'aperçu derrière. Deux bandes n'ont pas de
   destination encore construite : le clic n'y fait rien. */

import { Chevron } from './chevron.js';

const root = document.querySelector('[data-cvm-root]');
if (root) boot(root);

function boot(root) {
  const stage = root.querySelector('[data-stage]');
  const bands = Array.from(root.querySelectorAll('[data-band]'));
  const tileSlide = 80;

  const ch = new Chevron(stage, bands, {
    apexX: 0.38, apexY: -0.1, arm: 18,
    thickRatio: 1, gap: 10, push: 0.2, dim: 0.9,
    onState(band, i, s) {
      if (!band._tiles) return;
      const dx = (band._apexSign || 1) * tileSlide;
      for (const d of band._tiles) {
        d.style.opacity = s.on ? '0' : '1';
        d.style.transform = s.on ? 'translateX(' + dx + 'px) scaleY(.5)' : 'none';
      }
    }
  });

  const onMove = e => {
    const b = e.target.closest && e.target.closest('[data-band]');
    if (b) ch.focus(bands.indexOf(b)); else ch.blur();
  };
  const onOut = () => ch.blur();
  const onClick = e => {
    const b = e.target.closest && e.target.closest('[data-band]');
    if (b && b.hasAttribute('data-soon')) e.preventDefault();
  };

  stage.addEventListener('mouseover', onMove);
  stage.addEventListener('focusin', onMove);
  stage.addEventListener('mouseleave', onOut);
  stage.addEventListener('click', onClick);

  /* Retour depuis une page externe restaurée par le bfcache : la géométrie
     dépend des dimensions du stage au moment du montage, à recalculer si
     elles ont changé pendant l'absence (même leçon que welcome-lakeust.js). */
  addEventListener('pageshow', e => { if (e.persisted) ch.measure(); });

  addEventListener('pagehide', () => {
    ch.destroy();
    stage.removeEventListener('mouseover', onMove);
    stage.removeEventListener('focusin', onMove);
    stage.removeEventListener('mouseleave', onOut);
    stage.removeEventListener('click', onClick);
  }, { once: true });
}
