/* ui/orbital-preview.js — <black-hole-stage drive="external"> ambiant, sans
   interaction utilisateur : sert de vignette "capture" dans les fiches
   Réalisations (studio/about, web/about) — le même élément que le hero de
   l'accueil, mais sans dépendance au scroll de la page hôte, juste une
   légère rotation continue. drive="external" exige host.setShot() : sans
   lui uFade reste à 0 et la scène ne s'affiche jamais (voir blackhole.js). */

function drive(el) {
  const t0 = performance.now();
  let raf = 0, dead = false;
  const tick = () => {
    if (dead) return;
    if (el.setShot) {
      const t = (performance.now() - t0) / 1000;
      const intro = Math.min(1, t / 1.6);
      el.setShot({
        dist: 94 + Math.sin(t * 0.09) * 1.4,
        azim: 0.3 + t * 0.02,
        elev: 0.3 + Math.sin(t * 0.13) * 0.015,
        fov: 52, roll: 0, parallax: 0.6,
        lens: 0.85, diskBright: 1, diskThick: 0.5, diskMul: 1,
        spin: 0.45, bgFade: 0.85, capture: 0, objDim: 1,
        bloom: 1, vignette: 0.94, aberr: 0.16, grain: 0.02,
        exposure: 0.5 * intro, fade: intro,
      });
    }
    raf = requestAnimationFrame(tick);
  };
  tick();
  return () => { dead = true; cancelAnimationFrame(raf); };
}

function boot() {
  return Array.from(document.querySelectorAll('[data-orbital-preview] black-hole-stage')).map(drive);
}

let stop = boot();

/* Retour depuis une page externe restaurée par le bfcache : les rAF ont été
   coupés au pagehide précédent — même mécanisme que welcome-lakeust.js,
   home.js et chevron-lab.js. */
addEventListener('pageshow', e => { if (e.persisted) { stop.forEach(d => d()); stop = boot(); } });
addEventListener('pagehide', () => { stop.forEach(d => d()); }, { once: true });
