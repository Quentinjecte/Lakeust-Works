/* boot-cinematic-page.js — orchestration de page partagée par
   blackhole-cinematic-entry.js et forest-cinematic-entry.js (leur boot()
   était quasi identique : résoudre le stage, lire ?studio, peindre
   skip/phase, câbler les boutons, démarrer la lecture, amorcer au
   DOMContentLoaded). home-cinematic-entry.js n'utilise PAS ceci : son rôle
   est différent (pré-initialiser @theatre/studio avant que @theatre/core
   soit enregistré — voir son fichier), l'orchestration de la page vivant
   dans home-cinematic.js lui-même (bootPage()).

   Corrige au passage un bug réel : ni blackhole-cinematic-entry.js ni
   forest-cinematic-entry.js n'appelaient cine.dispose() au départ de la
   page (home-cinematic.js le faisait déjà) — la boucle de secours par
   échantillonnage (voir shot-driven-cinematic.js) et le minuteur de
   endWatch survivaient donc à la navigation. Câblé ici une bonne fois. */

export function bootCinematicPage({
  waitForStage, createCinematic, labels, startReadout, windowHandle
}) {
  function paintSkip(v) {
    const el = document.querySelector('[data-wc="skip"]');
    if (el) { el.style.opacity = String(v); el.style.pointerEvents = v ? 'auto' : 'none'; }
  }

  function paintPhase(p) {
    const el = document.querySelector('[data-wc="phaseName"]');
    if (el) el.textContent = labels[p] || p;
  }

  async function boot() {
    const root = document.querySelector('[data-wc-root]') || document.body;
    const stage = await waitForStage();
    const studio = new URLSearchParams(location.search).has('studio');

    const cine = await createCinematic({
      stage, root, studio,
      onPhase: p => paintPhase(p),
      onEnd: () => paintSkip(0)
    });

    /* poignée console, ex. __wcCine.seek(12) / .pause() / .replay() pour l'art direction */
    window[windowHandle] = cine;
    const readoutTimer = startReadout ? startReadout(stage) : 0;

    document.querySelector('[data-wc="skip"]')?.addEventListener('click', () => { cine.skipToEnd(); paintSkip(0); });
    document.querySelector('[data-wc="replay"]')?.addEventListener('click', () => { paintSkip(0.5); cine.replay(); });

    cine.play();

    addEventListener('pagehide', () => { clearInterval(readoutTimer); cine.dispose(); }, { once: true });
  }

  if (document.readyState === 'loading') addEventListener('DOMContentLoaded', boot);
  else boot();
}
