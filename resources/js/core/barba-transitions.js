/* barba-transitions.js — navigation SPA entre les pages du gabarit partagé
   (about / works / project — layouts/site.blade.php), quatre transitions
   tirées du cinématique orbital, branchées sur les hooks leave/enter de
   Barba. Remplace l'ancien rideau à trois panneaux (voir git history) :
   Barba reprend le rôle de la navigation.

   Répartition :
     - transitions[] choisit QUELLE animation visuelle joue pour une paire de
       namespaces donnée, et gate le swap DOM sur sa promesse de leave().
     - hooks.* gère les effets de bord indépendants du visuel : nettoyer les
       systèmes de la page qui part (page-systems.js, ScrollTrigger de /projet),
       remonter ceux de la page qui arrive, tenir aria-current à jour.

   Une seule paire par défaut si aucune règle plus spécifique ne correspond :
   Voile. Ajouter une règle, c'est ajouter une entrée à TRANSITIONS avant
   l'entrée par défaut (Barba retient la première qui correspond).

   Filet de sécurité — chaque leave()/enter() ne résout QUE via le ticker
   GSAP (delayedCall / onComplete). Si ce ticker ne tourne pas au moment où
   Barba lance la transition (onglet mis en arrière-plan pile à ce moment,
   frame perdue, etc.), la promesse ne se résout jamais : Barba a déjà mis à
   jour l'URL/le titre (history.pushState arrive tôt dans son cycle) mais le
   conteneur reste bloqué — visuellement une page qui « devient vide » tant
   qu'on ne rafraîchit pas. withSafety() borne chaque leave/enter dans le
   temps et force un état propre (conteneur visible, styles nettoyés) si la
   promesse d'origine ne s'est pas résolue à temps, pour qu'une transition ne
   puisse plus jamais rester bloquée indéfiniment. */

import barba from '@barba/core';
import gsap from 'gsap';
import { initPage, setActiveNav } from './page-systems.js';
import { refreshI18n } from './i18n.js';

const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

const NAV_LABELS = { 'web.about': 'à propos', 'web.works': 'travaux', 'web.project': 'projet' };

function overlay() { return document.querySelector('[data-pxo]'); }
function part(name) { const o = overlay(); return o && o.querySelector('[data-pxo="' + name + '"]'); }

/* module de /projet : importé à la demande, quel que soit le point d'entrée
   de la session (voir pages/project.js — ses hooks ne s'auto-exécutent
   plus). */
let projectMod = null;
async function loadProjectMod() {
  if (!projectMod) projectMod = await import('../pages/project.js');
  return projectMod;
}

/* ────────────────────────────────────────────── transitions visuelles ─── */

function voileLeave({ current }) {
  const veil = part('veil');
  return new Promise(resolve => {
    gsap.set(veil, { opacity: 0 });
    gsap.to(veil, { opacity: 0.8, duration: 0.52, ease: 'power2.in' });
    gsap.to(current.container, { filter: 'blur(6px)', y: -14, duration: 0.52, ease: 'power2.out', onComplete: resolve });
  });
}

function voileEnter({ next }) {
  const veil = part('veil');
  gsap.fromTo(next.container, { opacity: 0, y: 18, filter: 'blur(5px)' }, { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.68, ease: 'power2.out' });
  return new Promise(resolve => {
    gsap.to(veil, { opacity: 0, duration: 0.68, delay: 0.02, onComplete: () => { gsap.set(veil, { clearProps: 'opacity' }); resolve(); } });
  });
}

function horizonLeave({ current }) {
  const top = part('panelTop'), bottom = part('panelBottom'), line = part('line');
  gsap.set(line, { scaleX: 0, opacity: 0 });
  gsap.to(line, { scaleX: 1, opacity: 1, duration: 0.26, ease: 'expo.out' });
  return new Promise(resolve => {
    gsap.delayedCall(0.16, () => {
      gsap.to(top, { y: '0%', duration: 0.46 });
      gsap.to(bottom, { y: '0%', duration: 0.46, onComplete: resolve });
      gsap.to(current.container, { opacity: 0.3, duration: 0.4 });
    });
  });
}

function horizonEnter({ next }) {
  const top = part('panelTop'), bottom = part('panelBottom'), line = part('line');
  gsap.fromTo(next.container, { opacity: 0 }, { opacity: 1, duration: 0.38 });
  gsap.to(line, { opacity: 0, duration: 0.62, ease: 'power2.out' });
  return new Promise(resolve => {
    gsap.to(top, { y: '-101%', duration: 0.62, onComplete: () => gsap.set(top, { clearProps: 'transform' }) });
    gsap.to(bottom, { y: '101%', duration: 0.62, onComplete: () => {
      gsap.set(bottom, { clearProps: 'transform' });
      gsap.set(line, { clearProps: 'opacity,transform' });
      resolve();
    } });
  });
}

function signalLeave({ current, next }) {
  const dim = part('dim'), scan = part('scan'), readout = part('readout'), routeEl = part('route');
  if (routeEl) routeEl.textContent = 'Route — ' + (NAV_LABELS[current.namespace] || current.namespace) + ' → ' + (NAV_LABELS[next.namespace] || next.namespace);
  gsap.set(dim, { opacity: 0 });
  gsap.set(scan, { opacity: 0.9, y: '-110%' });
  gsap.set(readout, { opacity: 0, y: 8 });
  gsap.to(dim, { opacity: 1, duration: 0.3, ease: 'power2.in' });
  gsap.to(scan, { y: '400%', duration: 0.62, ease: 'none' });
  gsap.to(readout, { opacity: 1, y: 0, duration: 0.42 });
  return new Promise(resolve => gsap.delayedCall(0.62, resolve));
}

function signalEnter({ next }) {
  const dim = part('dim'), scan = part('scan'), readout = part('readout');
  gsap.set(next.container, { opacity: 1 });
  gsap.set(scan, { opacity: 0.6, y: '-110%' });
  gsap.to(scan, { y: '400%', duration: 0.42, ease: 'none' });
  gsap.to(dim, { opacity: 0, duration: 0.54 });
  gsap.to(readout, { opacity: 0, duration: 0.3 });
  gsap.fromTo(next.container, { opacity: 0.25 }, { opacity: 1, duration: 0.54, ease: 'steps(5)' });
  return new Promise(resolve => gsap.delayedCall(0.56, () => {
    gsap.set([dim, scan, readout], { clearProps: 'all' });
    resolve();
  }));
}

/* la couverture vient du box-shadow, à écartement constant : on anime la
   TAILLE de l'ouverture, pas un scale (qui ferait rétrécir l'ombre avec). */
function eclipseLeave({ current }) {
  const hole = part('hole'), ring = part('ring');
  gsap.set(ring, { opacity: 0 });
  gsap.to(hole, { width: '0%', duration: 0.56, ease: 'power3.inOut' });
  gsap.to(ring, { width: '6%', opacity: 1, duration: 0.56, ease: 'power3.inOut' });
  gsap.to(current.container, { opacity: 0, duration: 0.4, delay: 0.16 });
  return new Promise(resolve => gsap.delayedCall(0.56, resolve));
}

function eclipseEnter({ next }) {
  const hole = part('hole'), ring = part('ring');
  gsap.set(next.container, { opacity: 1 });
  return new Promise(resolve => {
    gsap.delayedCall(0.2, () => {
      gsap.fromTo(next.container, { scale: 1.05 }, { scale: 1, duration: 0.9, ease: 'expo.out' });
      gsap.to(hole, { width: '138%', duration: 0.72, ease: 'expo.out' });
      gsap.to(ring, { width: '138%', opacity: 0, duration: 0.72, ease: 'expo.out', onComplete: () => {
        gsap.set(hole, { clearProps: 'width' });
        gsap.set(ring, { clearProps: 'all' });
        resolve();
      } });
    });
  });
}

/* Repli prefers-reduced-motion : un simple fondu d'opacité, sans mouvement ni
   flou — ce que WCAG vise à supprimer, c'est le grand déplacement/parallaxe
   qui déclenche des troubles vestibulaires, pas toute rétroaction visuelle.
   Un cut instantané et muet ressemble à une navigation cassée. */
function instantLeave({ current }) {
  return new Promise(resolve => gsap.to(current.container, { opacity: 0, duration: 0.15, ease: 'power1.out', onComplete: resolve }));
}
function instantEnter({ next }) {
  gsap.set(next.container, { clearProps: 'transform,filter' });
  return new Promise(resolve => gsap.fromTo(next.container, { opacity: 0 }, { opacity: 1, duration: 0.15, ease: 'power1.out', onComplete: resolve }));
}

/* Borne chaque leave()/enter() dans le temps : si la promesse GSAP d'origine
   n'a pas résolu passé `ms`, on force un état final propre (le conteneur qui
   part disparaît réellement, celui qui arrive redevient pleinement visible)
   et on résout quand même — Barba peut alors terminer son swap au lieu de
   rester en attente indéfiniment. `ms` reste large par rapport à la durée
   réelle des tweens (~0.5–1.1s) : ce n'est qu'un filet, pas un minuteur qui
   coupe l'animation normale. */
function withSafety(fn, ms, cleanup) {
  return data => {
    let settled = false;
    const real = Promise.resolve().then(() => fn(data)).then(v => { settled = true; return v; });
    const safety = new Promise(resolve => {
      setTimeout(() => {
        if (settled) return;
        cleanup(data);
        resolve();
      }, ms);
    });
    return Promise.race([real, safety]);
  };
}

const leaveSafe = (fn, ms = 1800) => withSafety(fn, ms, ({ current }) => gsap.set(current.container, { clearProps: 'all' }));
const enterSafe = (fn, ms = 2200) => withSafety(fn, ms, ({ next }) => gsap.set(next.container, { opacity: 1, clearProps: 'transform,filter,scale' }));

const TRANSITIONS = [
  { name: 'horizon', to: { namespace: ['web.about', 'web.works'] }, from: { namespace: ['web.about', 'web.works'] }, leave: leaveSafe(horizonLeave), enter: enterSafe(horizonEnter) },
  { name: 'signal', to: { namespace: ['web.works', 'web.project'] }, from: { namespace: ['web.works', 'web.project'] }, leave: leaveSafe(signalLeave), enter: enterSafe(signalEnter) },
  { name: 'eclipse', to: { namespace: ['web.about', 'web.project'] }, from: { namespace: ['web.about', 'web.project'] }, leave: leaveSafe(eclipseLeave), enter: enterSafe(eclipseEnter) },
  { name: 'voile', leave: leaveSafe(voileLeave), enter: enterSafe(voileEnter) } // catch-all — doit rester en dernier
];

/* ───────────────────────────────────────────────────── amorçage page --- */

/* pages/project.js pilote data-env-stage/data-corridor-stage/data-braid —
   présents sur toute fiche produit détaillée, pas seulement /projet : les
   fiches Studio (studio.project01, studio.project02, ...) le chargent aussi.
   Un simple isProjectPage(namespace) plutôt qu'une liste figée : le namespace
   vient du nom de route Blade, donc toute nouvelle fiche studio.projetNN
   matche automatiquement sans repasser ici. */
const isProjectPage = namespace => namespace === 'web.project' || namespace.startsWith('studio.project');

function bootPageSystems(root, namespace) {
  initPage(root);
  setActiveNav(namespace);
  refreshI18n(root); // le contenu qui vient d'arriver dans <main> est écrit en français, à retraduire si la langue choisie est l'anglais
  if (isProjectPage(namespace)) loadProjectMod().then(m => m.init(root));
}

export function initBarba() {
  const wrapper = document.querySelector('[data-barba="wrapper"]');
  if (!wrapper) return; // page hors gabarit partagé (cinématiques, lab...) : rien à faire

  const initialContainer = document.querySelector('[data-barba="container"]');
  if (initialContainer) bootPageSystems(initialContainer, initialContainer.dataset.barbaNamespace);

  if (reduced()) {
    barba.init({ transitions: [{ name: 'reduced', leave: leaveSafe(instantLeave, 600), enter: enterSafe(instantEnter, 600) }] });
  } else {
    barba.init({ transitions: TRANSITIONS });
  }

  barba.hooks.beforeLeave(data => {
    if (isProjectPage(data.current.namespace) && projectMod) projectMod.dispose(data.current.container);
  });

  barba.hooks.beforeEnter(data => {
    /* la classe page-in ne doit jouer qu'au tout premier chargement réel :
       une transition Barba a déjà sa propre animation d'entrée, et l'ancien
       afterPageIn() de project.js se rend inerte tout seul en son absence. */
    data.next.container.classList.remove('page-in');
  });

  barba.hooks.afterEnter(data => bootPageSystems(data.next.container, data.next.namespace));
}
