/* project.js — entrée additionnelle chargée uniquement sur /projet (via
   @section('entry') dans project.blade.php, en plus du socle app.js).

   Trois emprunts au Scroll Lab, en scroll-driven pur (M.pinned / M.corridor /
   M.braid) pour des moments précis de la page. Le reste de /projet reste sur
   le système léger animation.js déjà chargé par app.js — pas de raison de
   dupliquer les reveals/scrub partout. */

import * as M from './portfolio/motion.js';

/* <main class="shell page-in"> porte l'animation d'entrée de la page (voir
   app.js) : tant qu'elle tourne, elle maintient une valeur "translate"
   active qui établit un containing block pour les descendants en
   position:fixed — dont les stages épinglés ci-dessous. Si on les monte
   pendant ce court intervalle, ScrollTrigger mesure leurs bornes de scroll
   sur une géométrie encore faussée, et aucun refresh() ultérieur ne les
   corrige puisqu'il ne fait que confirmer les mêmes mesures cassées.
   On attend donc la fin réelle de cette animation avant de monter quoi que
   ce soit. */
function afterPageIn() {
  const main = document.querySelector('main.page-in');
  if (!main) return Promise.resolve();
  return new Promise(resolve => main.addEventListener('animationend', resolve, { once: true }));
}

function whenFullyLoaded() {
  if (document.readyState === 'complete') return Promise.resolve();
  return new Promise(resolve => addEventListener('load', resolve, { once: true }));
}

Promise.all([M.boot(), afterPageIn()]).then(() => {
  /* Trois sections épinglées à la suite : chacune insère un pin-spacer qui
     déplace le flux du document sous elle. Un refresh() entre chaque montage
     (pas seulement à la fin) force GSAP à mesurer la section suivante sur un
     document déjà stabilisé — sinon ses bornes de scroll restent calculées
     sur l'ancienne hauteur de page et chevauchent la précédente
     (cf. README-scroll.md, « après toute insertion de contenu,
     ScrollTrigger.refresh() »). */
  setupEnvironment(M);
  setupCorridor(M);
  setupBraid(M);

  /* La page contient de nombreuses images (hero, HQ, galerie...) réparties
     avant et entre ces trois sections : tant qu'elles n'ont pas fini de
     charger, la mise en page continue de bouger sous elles. On attend donc
     le chargement complet (window 'load') avant le calage final.

     ScrollTrigger.refresh() global ne suffit pas ici : sur trois pins
     séquentiels, il ne recalcule pas correctement le "start" des pins
     situés après le premier (mesuré en ignorant la hauteur du pin-spacer
     qui les précède). Rafraîchir chaque trigger via sa propre méthode
     .refresh(), et dans l'ordre INVERSE du document (le plus bas d'abord),
     donne les bonnes valeurs — vérifié empiriquement.

     En prefers-reduced-motion, pinned() désactive déjà chaque trigger
     (tl.scrollTrigger.disable()) : ils sont inertes, donc leurs bornes de
     scroll n'ont aucune importance. Rafraîchir un trigger désactivé peut le
     re-pinner par effet de bord (observé sur le dernier de la chaîne) — on
     saute donc ce calage quand le mouvement est réduit. */
  if (M.reduced()) return;
  whenFullyLoaded().then(() => new Promise(r => setTimeout(r, 300))).then(() => {
    const stages = [
      document.querySelector('[data-braid]'),
      document.querySelector('[data-corridor-stage]'),
      document.querySelector('[data-env-stage]'),
    ];
    const all = M.ScrollTrigger.getAll();
    stages.forEach(el => {
      const t = el && all.find(tr => tr.trigger === el);
      t && t.refresh();
    });
  });
});

/* --------------------------------------------------- environnement --- */
/* Séquence jour → nuit → brouillard. */

function setupEnvironment(M) {
  const stage = document.querySelector('[data-env-stage]');
  if (!stage) return;

  const layers = [...stage.querySelectorAll('[data-env-layer]')];
  const label = stage.querySelector('[data-env-label]');
  const caption = stage.querySelector('[data-env-caption]');
  const bar = stage.querySelector('[data-env-progress] span');
  if (!layers.length) return;

  const STATES = [
    { name: 'JOUR', text: "La visibilité permet d'explorer plus facilement le terrain, mais ne garantit pas votre sécurité." },
    { name: 'NUIT', text: 'Dans l’obscurité, chaque mouvement peut devenir une menace.' },
    { name: 'BROUILLARD', text: 'La visibilité disparaît progressivement. Les sons deviennent votre principal moyen de repérer ce qui vous entoure.' },
  ];

  const n = layers.length; // 3 couches, une par état
  let last = -1;

  const layout = p => {
    const seg = Math.min(n - 1, Math.floor(p * n));
    layers.forEach((el, i) => {
      const local = Math.min(1, Math.max(0, p * n - i));
      el.style.opacity = i === 0 ? 1 : String(local);
    });
    if (bar) bar.style.transform = 'scaleX(' + p + ')';
    if (seg !== last) {
      last = seg;
      const s = STATES[seg];
      if (label) label.textContent = s.name;
      if (caption) caption.textContent = s.text;
    }
  };

  M.pinned(stage, () => {}, {
    length: 3600,
    scrub: 0.4,
    anticipatePin: 0, // plusieurs sections épinglées coexistent dans le flux ; cf. commentaire de boot()
    onUpdate: st => layout(st.progress),
  });

  layout(M.reduced() ? 0.999 : 0);
}

/* ------------------------------------------------------- corridor --- */
/* Traversée du QG : la caméra avance parmi ses cinq espaces. */

function setupCorridor(M) {
  const stage = document.querySelector('[data-corridor-stage]');
  if (!stage) return;
  const z = stage.querySelector('[data-corridor-z]');
  M.corridor(stage, { anticipatePin: 0, onUpdate: (p, cam) => { if (z) z.textContent = Math.round(cam); } });
}

/* --------------------------------------------------------- entrelacs --- */
/* Observe. Track. Adapt. — trois brins qui se tressent. */

function setupBraid(M) {
  const stage = document.querySelector('[data-braid]');
  if (!stage) return;
  const front = stage.querySelector('[data-braid-front]');
  const names = [...stage.querySelectorAll('[data-strand-name]')].map(e => e.textContent.trim());
  M.braid(stage, {
    anticipatePin: 0,
    onUpdate: (p, i) => { if (front) front.textContent = names[i] || ''; },
  });
}
